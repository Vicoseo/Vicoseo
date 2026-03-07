<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\FooterLink;
use App\Models\GlobalTopOffer;
use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\Sponsor;
use App\Models\SitePromotion;
use App\Models\TopOffer;
use App\Services\TenantManager;
use Illuminate\Console\Command;

class AuditCrossLinks extends Command
{
    protected $signature = 'audit:cross-links
        {--fix : Automatically remove/disable cross-site links}';

    protected $description = 'Audit all links across sites to detect cross-site linking between network sites';

    private array $networkDomains = [];
    private array $issues = [];

    public function handle(TenantManager $tenantManager): int
    {
        $sites = Site::where('is_active', true)->get();

        // Build list of all network domains (without and with www)
        foreach ($sites as $site) {
            $domain = strtolower($site->domain);
            $this->networkDomains[] = $domain;
            $this->networkDomains[] = 'www.' . $domain;
        }

        $this->info("Network domains ({$sites->count()}): " . implode(', ', $sites->pluck('domain')->toArray()));
        $this->newLine();

        $fix = (bool) $this->option('fix');

        // 1. Sponsors (global — shared across ALL sites)
        $this->auditSponsors($fix);

        // 2. Global Top Offers (shared across ALL sites)
        $this->auditGlobalTopOffers($fix);

        // 3. Site-level fields (fallback_domain, entry_url, login_url, sponsor_contact_url)
        $this->auditSiteFields($sites, $fix);

        // 4. Per-site tenant data
        foreach ($sites as $site) {
            $tenantManager->setTenant($site);
            $this->auditTenantData($site, $fix);
        }

        // Summary
        $this->newLine();
        if (empty($this->issues)) {
            $this->info('No cross-site links found. All clear!');
        } else {
            $this->error('=== CROSS-SITE LINK ISSUES FOUND: ' . count($this->issues) . ' ===');
            $this->newLine();

            $headers = ['#', 'Site', 'Source', 'Link URL', 'Points To', 'Fixed'];
            $rows = [];
            foreach ($this->issues as $i => $issue) {
                $rows[] = [
                    $i + 1,
                    $issue['site'],
                    $issue['source'],
                    strlen($issue['url']) > 60 ? substr($issue['url'], 0, 57) . '...' : $issue['url'],
                    $issue['target_domain'],
                    $issue['fixed'] ? 'YES' : 'NO',
                ];
            }
            $this->table($headers, $rows);
        }

        return self::SUCCESS;
    }

    private function isNetworkUrl(string $url, ?string $excludeDomain = null): ?string
    {
        $url = strtolower(trim($url));

        // Skip empty, relative, or anchor-only URLs
        if (empty($url) || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return null;
        }

        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;

        if (!$host) {
            return null;
        }

        $host = strtolower($host);

        // Remove www prefix for comparison
        $hostClean = preg_replace('/^www\./', '', $host);
        $excludeClean = $excludeDomain ? preg_replace('/^www\./', '', strtolower($excludeDomain)) : null;

        // If it points to the same site, it's not a cross-link
        if ($excludeClean && $hostClean === $excludeClean) {
            return null;
        }

        // Check if the host matches any network domain
        foreach ($this->networkDomains as $networkDomain) {
            $networkClean = preg_replace('/^www\./', '', $networkDomain);
            if ($hostClean === $networkClean) {
                return $networkClean;
            }
        }

        return null;
    }

    private function addIssue(string $site, string $source, string $url, string $targetDomain, bool $fixed = false): void
    {
        $this->issues[] = [
            'site' => $site,
            'source' => $source,
            'url' => $url,
            'target_domain' => $targetDomain,
            'fixed' => $fixed,
        ];
    }

    // ─── AUDITORS ───

    private function auditSponsors(bool $fix): void
    {
        $this->info('--- Checking Sponsors (global) ---');
        $sponsors = Sponsor::all();

        foreach ($sponsors as $sponsor) {
            $target = $this->isNetworkUrl($sponsor->url ?? '');
            if ($target) {
                $this->warn("  [SPONSOR] \"{$sponsor->name}\" links to network site: {$sponsor->url} → {$target}");
                $fixed = false;
                if ($fix) {
                    $sponsor->update(['is_active' => false]);
                    $this->info("    → Deactivated sponsor \"{$sponsor->name}\"");
                    $fixed = true;
                }
                $this->addIssue('GLOBAL', "Sponsor: {$sponsor->name}", $sponsor->url, $target, $fixed);
            }
        }

        $this->line("  Checked {$sponsors->count()} sponsors.");
    }

    private function auditGlobalTopOffers(bool $fix): void
    {
        $this->info('--- Checking Global Top Offers ---');
        $offers = GlobalTopOffer::all();

        foreach ($offers as $offer) {
            $target = $this->isNetworkUrl($offer->target_url ?? '');
            if ($target) {
                $this->warn("  [GLOBAL OFFER] \"{$offer->bonus_text}\" links to: {$offer->target_url} → {$target}");
                $fixed = false;
                if ($fix) {
                    $offer->update(['is_active' => false]);
                    $this->info("    → Deactivated global offer #{$offer->id}");
                    $fixed = true;
                }
                $this->addIssue('GLOBAL', "GlobalTopOffer: {$offer->bonus_text}", $offer->target_url, $target, $fixed);
            }
        }

        $this->line("  Checked {$offers->count()} global top offers.");
    }

    private function auditSiteFields(mixed $sites, bool $fix): void
    {
        $this->info('--- Checking Site Fields (fallback_domain, entry_url, login_url, sponsor_contact_url) ---');

        foreach ($sites as $site) {
            $fields = [
                'fallback_domain' => $site->fallback_domain,
                'entry_url' => $site->entry_url,
                'login_url' => $site->login_url,
                'sponsor_contact_url' => $site->sponsor_contact_url,
            ];

            foreach ($fields as $field => $value) {
                if (empty($value)) {
                    continue;
                }

                // fallback_domain might be just a domain, not a full URL
                $checkUrl = $value;
                if ($field === 'fallback_domain' && !str_contains($value, '://')) {
                    $checkUrl = 'https://' . $value;
                }

                $target = $this->isNetworkUrl($checkUrl, $site->domain);
                if ($target) {
                    $this->warn("  [{$site->domain}] {$field} = {$value} → points to network site: {$target}");
                    $fixed = false;
                    if ($fix) {
                        $site->update([$field => null]);
                        $this->info("    → Cleared {$field} for {$site->domain}");
                        $fixed = true;
                    }
                    $this->addIssue($site->domain, "Site.{$field}", $value, $target, $fixed);
                }
            }
        }
    }

    private function auditTenantData(Site $site, bool $fix): void
    {
        $this->info("--- Checking [{$site->domain}] tenant data ---");

        // Footer Links
        $footerLinks = FooterLink::where('is_active', true)->get();
        foreach ($footerLinks as $link) {
            $target = $this->isNetworkUrl($link->link_url ?? '', $site->domain);
            if ($target) {
                $this->warn("  [FOOTER] \"{$link->link_text}\" → {$link->link_url} → {$target}");
                $fixed = false;
                if ($fix) {
                    $link->update(['is_active' => false]);
                    $this->info("    → Deactivated footer link \"{$link->link_text}\"");
                    $fixed = true;
                }
                $this->addIssue($site->domain, "FooterLink: {$link->link_text}", $link->link_url, $target, $fixed);
            }
        }

        // Top Offers (site-specific)
        $offers = TopOffer::where('is_active', true)->get();
        foreach ($offers as $offer) {
            $target = $this->isNetworkUrl($offer->target_url ?? '', $site->domain);
            if ($target) {
                $this->warn("  [TOP OFFER] \"{$offer->bonus_text}\" → {$offer->target_url} → {$target}");
                $fixed = false;
                if ($fix) {
                    $offer->update(['is_active' => false]);
                    $this->info("    → Deactivated top offer #{$offer->id}");
                    $fixed = true;
                }
                $this->addIssue($site->domain, "TopOffer: {$offer->bonus_text}", $offer->target_url, $target, $fixed);
            }
        }

        // Redirects
        $redirects = Redirect::where('is_active', true)->get();
        foreach ($redirects as $redirect) {
            $target = $this->isNetworkUrl($redirect->target_url ?? '', $site->domain);
            if ($target) {
                $this->warn("  [REDIRECT] /{$redirect->slug} → {$redirect->target_url} → {$target}");
                $fixed = false;
                if ($fix) {
                    $redirect->update(['is_active' => false]);
                    $this->info("    → Deactivated redirect /{$redirect->slug}");
                    $fixed = true;
                }
                $this->addIssue($site->domain, "Redirect: /{$redirect->slug}", $redirect->target_url, $target, $fixed);
            }
        }

        // Site Promotions
        $promotions = SitePromotion::where('site_id', $site->id)->where('is_active', true)->get();
        foreach ($promotions as $promo) {
            $target = $this->isNetworkUrl($promo->link_url ?? '', $site->domain);
            if ($target) {
                $this->warn("  [PROMO] \"{$promo->title}\" → {$promo->link_url} → {$target}");
                $fixed = false;
                if ($fix) {
                    $promo->update(['is_active' => false]);
                    $this->info("    → Deactivated promotion \"{$promo->title}\"");
                    $fixed = true;
                }
                $this->addIssue($site->domain, "Promotion: {$promo->title}", $promo->link_url, $target, $fixed);
            }
        }

        // Pages — scan HTML content for cross-site links
        $pages = Page::all();
        foreach ($pages as $page) {
            $this->scanContentLinks($site, "Page: {$page->slug}", $page->content ?? '', $fix, $page);
        }

        // Posts — scan HTML content for cross-site links
        $posts = Post::all();
        foreach ($posts as $post) {
            $this->scanContentLinks($site, "Post: {$post->slug}", $post->content ?? '', $fix, $post);
        }
    }

    private function scanContentLinks(Site $site, string $label, string $html, bool $fix, $model): void
    {
        // Find all href attributes in the HTML
        if (!preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $matches)) {
            return;
        }

        $foundCrossLinks = [];

        foreach ($matches[1] as $url) {
            $target = $this->isNetworkUrl($url, $site->domain);
            if ($target) {
                $foundCrossLinks[] = ['url' => $url, 'target' => $target];
            }
        }

        if (empty($foundCrossLinks)) {
            return;
        }

        foreach ($foundCrossLinks as $crossLink) {
            $this->warn("  [CONTENT] {$label} contains link to: {$crossLink['url']} → {$crossLink['target']}");
            $fixed = false;

            if ($fix) {
                // Remove the cross-site link from content, keeping the anchor text
                $html = preg_replace(
                    '/<a[^>]*href=["\']' . preg_quote($crossLink['url'], '/') . '["\'][^>]*>(.*?)<\/a>/is',
                    '$1',
                    $html
                );
                $fixed = true;
            }

            $this->addIssue($site->domain, $label, $crossLink['url'], $crossLink['target'], $fixed);
        }

        if ($fix && !empty($foundCrossLinks)) {
            $model->update(['content' => $html]);
            $this->info("    → Removed " . count($foundCrossLinks) . " cross-link(s) from {$label}");
        }
    }
}
