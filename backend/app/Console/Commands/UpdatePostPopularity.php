<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Site;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleSearchConsoleService;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePostPopularity extends Command
{
    protected $signature = 'posts:update-popularity
                            {--site= : Process a single site by domain}
                            {--period=28d : Lookback period (e.g. 28d, 7d)}
                            {--dry-run : Show scores without writing to DB}';

    protected $description = 'Update post popularity scores using GSC and GA4 data';

    public function handle(
        TenantManager $tenantManager,
        GoogleSearchConsoleService $gscService,
        GoogleAnalyticsService $gaService,
    ): int {
        $period = $this->parsePeriod($this->option('period'));
        $startDate = now()->subDays($period)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        $dryRun = (bool) $this->option('dry-run');

        $query = Site::where('is_active', true)->whereNull('fallback_domain');

        if ($siteDomain = $this->option('site')) {
            $query->where('domain', $siteDomain);
        }

        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->warn('No active sites found.');

            return self::SUCCESS;
        }

        $this->info("Processing {$sites->count()} site(s) — period: {$startDate} to {$endDate}" . ($dryRun ? ' [DRY RUN]' : ''));

        $totalUpdated = 0;

        foreach ($sites as $site) {
            $updated = $this->processSite($site, $tenantManager, $gscService, $gaService, $startDate, $endDate, $dryRun);
            $totalUpdated += $updated;
        }

        $this->info("Done. Updated {$totalUpdated} post(s) total.");

        return self::SUCCESS;
    }

    private function processSite(
        Site $site,
        TenantManager $tenantManager,
        GoogleSearchConsoleService $gscService,
        GoogleAnalyticsService $gaService,
        string $startDate,
        string $endDate,
        bool $dryRun,
    ): int {
        $this->line("  [{$site->domain}]");

        $tenantManager->setTenant($site);

        $siteUrl = "sc-domain:{$site->domain}";
        $gscData = [];
        $gaData = [];

        // Fetch GSC page performance
        try {
            $gscPages = $gscService->getPagePerformance($siteUrl, $startDate, $endDate, 200);
            foreach ($gscPages as $page) {
                $slug = $this->extractSlugFromUrl($page['page']);
                if ($slug) {
                    $gscData[$slug] = [
                        'clicks' => $page['clicks'],
                        'impressions' => $page['impressions'],
                    ];
                }
            }
            $this->line("    GSC: {$this->countEntries($gscData)} blog page(s) found");
        } catch (\Exception $e) {
            $this->warn("    GSC failed: {$e->getMessage()}");
            Log::warning("UpdatePostPopularity GSC error for {$site->domain}: {$e->getMessage()}");
        }

        // Fetch GA4 top pages
        if ($site->ga_measurement_id) {
            try {
                $gaPages = $gaService->getTopPages($site->ga_measurement_id, $startDate, $endDate, 200, $site->domain);
                foreach ($gaPages as $page) {
                    $slug = $this->extractSlugFromPath($page['path']);
                    if ($slug) {
                        $gaData[$slug] = $page['page_views'];
                    }
                }
                $this->line("    GA4: {$this->countEntries($gaData)} blog page(s) found");
            } catch (\Exception $e) {
                $this->warn("    GA4 failed: {$e->getMessage()}");
                Log::warning("UpdatePostPopularity GA4 error for {$site->domain}: {$e->getMessage()}");
            }
        } else {
            $this->line('    GA4: no measurement ID configured');
        }

        // Merge slugs
        $allSlugs = array_unique(array_merge(array_keys($gscData), array_keys($gaData)));

        if (empty($allSlugs)) {
            $this->line('    No data to process');

            return 0;
        }

        // Get existing posts
        $posts = Post::whereIn('slug', $allSlugs)->get()->keyBy('slug');
        $updated = 0;

        foreach ($allSlugs as $slug) {
            $post = $posts->get($slug);
            if (!$post) {
                continue;
            }

            $clicks = $gscData[$slug]['clicks'] ?? 0;
            $impressions = $gscData[$slug]['impressions'] ?? 0;
            $pageViews = $gaData[$slug] ?? 0;

            $score = $this->calculateScore($clicks, $impressions, $pageViews);

            if ($dryRun) {
                $this->line("    [{$slug}] clicks={$clicks} imp={$impressions} pv={$pageViews} → score={$score}");
            } else {
                $post->update([
                    'gsc_clicks' => $clicks,
                    'gsc_impressions' => $impressions,
                    'ga_page_views' => $pageViews,
                    'popularity_score' => $score,
                    'popularity_scored_at' => now(),
                ]);
            }

            $updated++;
        }

        $this->line("    Updated: {$updated} post(s)");

        return $updated;
    }

    /**
     * Calculate composite popularity score using log scaling.
     * Weights: clicks 50%, impressions 30%, pageviews 20%
     */
    private function calculateScore(int $clicks, int $impressions, int $pageViews): int
    {
        $clickScore = $clicks > 0 ? log($clicks + 1) * 50 : 0;
        $impScore = $impressions > 0 ? log($impressions + 1) * 30 : 0;
        $pvScore = $pageViews > 0 ? log($pageViews + 1) * 20 : 0;

        return (int) round($clickScore + $impScore + $pvScore);
    }

    /**
     * Extract slug from a full URL: https://domain.com/blog/my-slug → my-slug
     */
    private function extractSlugFromUrl(string $url): ?string
    {
        if (preg_match('#/blog/([a-z0-9_-]+)#i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract slug from a path: /blog/my-slug → my-slug
     */
    private function extractSlugFromPath(string $path): ?string
    {
        if (preg_match('#^/blog/([a-z0-9_-]+)#i', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function parsePeriod(string $period): int
    {
        if (preg_match('/^(\d+)d$/i', $period, $m)) {
            return (int) $m[1];
        }

        return 28;
    }

    private function countEntries(array $data): int
    {
        return count($data);
    }
}
