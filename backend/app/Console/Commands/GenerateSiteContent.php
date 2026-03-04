<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BotTask;
use App\Models\Page;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateSiteContent extends Command
{
    protected $signature = 'content:generate-pages
        {--site= : Generate only for a specific domain}
        {--fresh : Delete existing pages and regenerate from scratch}
        {--dry-run : Show what would be generated without creating content}
        {--no-image : Skip featured image generation}';

    protected $description = 'Generate all 15 site pages (landing + static + cluster) with unique AI content';

    public function __construct(
        private TenantManager $tenantManager,
        private AIContentService $aiService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $fresh = (bool) $this->option('fresh');
        $siteFilter = $this->option('site');

        $this->info('Starting site page generation (15 pages per site)...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - no content will be created or deleted.');
        }
        if ($fresh) {
            $this->warn('FRESH MODE - existing pages will be deleted before regeneration.');
        }

        $query = Site::where('is_active', true);
        if ($siteFilter) {
            $query->where('domain', $siteFilter);
        }
        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->error('No active sites found.');
            return 1;
        }

        $this->info("Found {$sites->count()} active site(s).");
        $totalCreated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($sites as $site) {
            $this->newLine();
            $this->info("=== {$site->name} ({$site->domain}) ===");

            try {
                $this->tenantManager->setTenant($site);

                $botTask = BotTask::create([
                    'type' => 'generate_pages',
                    'status' => 'processing',
                    'source_site_id' => $site->id,
                    'target_site_id' => $site->id,
                    'payload' => ['fresh' => $fresh, 'dry_run' => $dryRun],
                    'progress' => 0,
                ]);

                if ($fresh && !$dryRun) {
                    $deletedCount = Page::count();
                    Page::truncate();
                    $this->warn("  Deleted {$deletedCount} existing pages.");
                }

                $brandName = $site->name;
                $domain = $site->domain;
                $slug = Str::slug($brandName);

                $siteInfo = [
                    'country' => 'Türkiye',
                    'domain' => $domain,
                    'name' => $brandName,
                    'brand_name' => $brandName,
                    'content_prompt_template' => $site->content_prompt_template,
                    'social_links' => $site->social_links ?? [],
                    'login_url' => $site->login_url ?? '',
                    'entry_url' => $site->entry_url ?? '',
                ];

                $allPages = $this->buildPageDefinitions($brandName, $domain, $slug);
                $existingSlugs = Page::pluck('slug')->toArray();
                $clusterSlugs = $this->getClusterSlugs($slug);

                $siteCreated = 0;
                $siteSkipped = 0;
                $siteErrors = [];

                foreach ($allPages as $i => $pageDef) {
                    $progress = (int) (($i + 1) / count($allPages) * 100);
                    $botTask->update(['progress' => $progress]);

                    // Skip if slug already exists (idempotent)
                    if (in_array($pageDef['slug'], $existingSlugs)) {
                        $this->line("  [SKIP] {$pageDef['slug']} (already exists)");
                        $siteSkipped++;
                        continue;
                    }

                    $this->line("  [{$pageDef['type']}] Generating: {$pageDef['slug']}");

                    if ($dryRun) {
                        $this->line("    [DRY RUN] Would create: {$pageDef['slug']} — {$pageDef['topic']}");
                        continue;
                    }

                    try {
                        $generated = $this->generatePage($pageDef, $brandName, $domain, $siteInfo, $clusterSlugs, $existingSlugs);

                        Page::create([
                            'slug' => $pageDef['slug'],
                            'title' => $generated['title'],
                            'content' => $generated['content'],
                            'meta_title' => $generated['meta_title'] ?? $generated['title'],
                            'meta_description' => $generated['meta_description'] ?? '',
                            'meta_keywords' => $generated['meta_keywords'] ?? '',
                            'is_published' => true,
                            'sort_order' => $pageDef['sort_order'],
                            'content_differentiated_at' => now(),
                        ]);

                        $existingSlugs[] = $pageDef['slug'];
                        $siteCreated++;
                        $this->info("    Created: {$generated['title']}");

                        Log::info('GeneratePages: Created page', [
                            'site' => $domain,
                            'slug' => $pageDef['slug'],
                            'type' => $pageDef['type'],
                        ]);
                    } catch (\Throwable $e) {
                        $siteErrors[] = "{$pageDef['slug']}: {$e->getMessage()}";
                        $this->error("    Error: {$e->getMessage()}");
                        Log::error('GeneratePages: Failed', [
                            'site' => $domain,
                            'slug' => $pageDef['slug'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $totalCreated += $siteCreated;
                $totalSkipped += $siteSkipped;
                $totalErrors += count($siteErrors);

                $botTask->update([
                    'status' => empty($siteErrors) ? 'completed' : 'completed_with_errors',
                    'progress' => 100,
                    'result' => [
                        'created' => $siteCreated,
                        'skipped' => $siteSkipped,
                        'errors' => $siteErrors,
                    ],
                ]);

                $this->info("  Summary: Created={$siteCreated}, Skipped={$siteSkipped}, Errors=" . count($siteErrors));
            } catch (\Throwable $e) {
                $totalErrors++;
                $this->error("  Site error: {$e->getMessage()}");
                Log::error('GeneratePages: Site failed', [
                    'site' => $site->domain,
                    'error' => $e->getMessage(),
                ]);

                if (isset($botTask)) {
                    $botTask->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->newLine();
        $this->info("All done! Created={$totalCreated}, Skipped={$totalSkipped}, Errors={$totalErrors}");

        Log::info('GeneratePages: Completed', [
            'created' => $totalCreated,
            'skipped' => $totalSkipped,
            'errors' => $totalErrors,
        ]);

        return $totalErrors > 0 && $totalCreated === 0 ? 1 : 0;
    }

    private function generatePage(array $pageDef, string $brandName, string $domain, array $siteInfo, array $clusterSlugs, array $existingSlugs): array
    {
        return match ($pageDef['type']) {
            'landing' => $this->aiService->generateLandingPage($brandName, $domain, $siteInfo, $existingSlugs),
            'static' => $this->aiService->generateStaticPage($brandName, $domain, $pageDef['page_type'], $pageDef['instructions'], $siteInfo),
            'cluster' => $this->aiService->generateClusterArticle($brandName, $domain, $pageDef['topic'], $pageDef['instructions'], $siteInfo, $clusterSlugs, $existingSlugs),
        };
    }

    private function buildPageDefinitions(string $brandName, string $domain, string $slug): array
    {
        $pages = [];

        // 1. Landing page
        $pages[] = [
            'slug' => 'anasayfa',
            'type' => 'landing',
            'topic' => "{$brandName} Ana Sayfa",
            'instructions' => '',
            'sort_order' => 0,
        ];

        // 2-5. Static pages
        $staticPages = [
            ['slug' => 'hakkimizda', 'page_type' => 'kurumsal', 'topic' => 'Hakkımızda', 'instructions' => "{$brandName} sitesinin hakkımızda sayfası. Sitenin misyonu, vizyonu, güvenilirliği ve lisans bilgileri hakkında profesyonel bir anlatım. Domain: {$domain}"],
            ['slug' => 'iletisim', 'page_type' => 'iletişim', 'topic' => 'İletişim', 'instructions' => "{$brandName} sitesinin iletişim sayfası. E-posta, Telegram ve canlı destek bilgileri. Domain: {$domain}"],
            ['slug' => 'gizlilik-politikasi', 'page_type' => 'yasal', 'topic' => 'Gizlilik Politikası', 'instructions' => "{$brandName} sitesinin gizlilik politikası. KVKK ve GDPR uyumlu yasal metin. Domain: {$domain}"],
            ['slug' => 'sorumluluk-reddi', 'page_type' => 'yasal', 'topic' => 'Sorumluluk Reddi', 'instructions' => "{$brandName} sitesinin sorumluluk reddi. Bahis riskleri, 18 yaş sınırı, sorumlu bahis ilkeleri. Domain: {$domain}"],
        ];
        foreach ($staticPages as $i => $sp) {
            $pages[] = array_merge($sp, ['type' => 'static', 'sort_order' => $i + 1]);
        }

        // 6-15. Cluster pages (10 total)
        $clusterPages = [
            ['slug' => "{$slug}-giris", 'topic' => "{$brandName} Giriş", 'instructions' => "{$brandName} platformuna giriş rehberi. 1500-2200 kelime."],
            ['slug' => "{$slug}-yeni-giris", 'topic' => "{$brandName} Yeni Giriş Adresi", 'instructions' => "{$brandName} yeni giriş adresi bilgilendirme. 1500-2200 kelime."],
            ['slug' => "{$slug}-guncel-adres", 'topic' => "{$brandName} Güncel Adres " . date('Y'), 'instructions' => "{$brandName} " . date('Y') . " güncel erişim bilgileri. 1500-2200 kelime."],
            ['slug' => "{$slug}-bonus", 'topic' => "{$brandName} Bonus ve Kampanyalar", 'instructions' => "{$brandName} bonus türleri ve çevrim şartları. 1500-2200 kelime."],
            ['slug' => "{$slug}-mobil-giris", 'topic' => "{$brandName} Mobil Giriş", 'instructions' => "{$brandName} mobil giriş rehberi. 1500-2200 kelime."],
            ['slug' => "{$slug}-casino", 'topic' => "{$brandName} Casino Oyunları Rehberi", 'instructions' => "{$brandName} casino oyunları rehberi. Rulet, blackjack, poker, baccarat gibi masa oyunları ve casino deneyimi. Oyun kuralları, masa limitleri, strateji ipuçları, popüler oyun sağlayıcıları. 1500-2200 kelime."],
            ['slug' => "{$slug}-slot", 'topic' => "{$brandName} Slot Oyunları Rehberi", 'instructions' => "{$brandName} slot oyunları detaylı rehberi. Popüler slotlar, RTP oranları, bonus özellikler, free spin mekanikleri, jackpot oyunları. Pragmatic Play, NetEnt gibi sağlayıcılar ve en çok kazandıran slotlar. 1500-2200 kelime."],
            ['slug' => "{$slug}-canli-casino", 'topic' => "{$brandName} Canlı Casino Deneyimi", 'instructions' => "{$brandName} canlı casino rehberi. Canlı rulet, canlı blackjack, game show oyunları (Crazy Time, Monopoly Live), canlı poker masaları. Gerçek krupiyelerle oynama deneyimi, yayın kalitesi, masa limitleri. 1500-2200 kelime."],
            ['slug' => "{$slug}-kayit", 'topic' => "{$brandName} Kayıt ve Üyelik Rehberi", 'instructions' => "{$brandName} üyelik açma adım adım rehber. Kayıt formu doldurma, hesap doğrulama süreci, ilk giriş adımları, hesap güvenliği ayarları. Kayıt sırasında dikkat edilmesi gerekenler. 1500-2200 kelime."],
            ['slug' => "{$slug}-para-yatirma", 'topic' => "{$brandName} Para Yatırma ve Çekme Rehberi", 'instructions' => "{$brandName} ödeme yöntemleri rehberi. Papara, kripto para, banka havalesi ile para yatırma ve çekme. Minimum/maksimum limitler, çekim süreleri, komisyon bilgileri, hesap doğrulama gereklilikleri. 1500-2200 kelime."],
        ];
        foreach ($clusterPages as $i => $cp) {
            $pages[] = array_merge($cp, ['type' => 'cluster', 'sort_order' => 10 + $i]);
        }

        return $pages;
    }

    private function getClusterSlugs(string $slug): array
    {
        return [
            "{$slug}-giris",
            "{$slug}-yeni-giris",
            "{$slug}-guncel-adres",
            "{$slug}-bonus",
            "{$slug}-mobil-giris",
            "{$slug}-casino",
            "{$slug}-slot",
            "{$slug}-canli-casino",
            "{$slug}-kayit",
            "{$slug}-para-yatirma",
        ];
    }
}
