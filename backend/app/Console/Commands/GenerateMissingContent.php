<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BotTask;
use App\Models\Page;
use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateMissingContent extends Command
{
    protected $signature = 'content:generate-missing
        {--site= : Generate only for a specific domain}
        {--dry-run : Show what would be generated without creating content}
        {--posts-count=15 : Number of blog posts to generate per site}';

    protected $description = 'Generate missing pages and blog posts for sites using AI. Idempotent - only creates content that does not exist.';

    public function __construct(
        private TenantManager $tenantManager,
        private AIContentService $aiService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $siteFilter = $this->option('site');
        $postsCount = (int) $this->option('posts-count');

        $this->info('Scanning for missing content...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - no content will be created.');
        }

        $query = Site::where('is_active', true)->whereNull('fallback_domain');
        if ($siteFilter) {
            $query->where('domain', $siteFilter);
        }
        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->error('No active sites found.');
            return 1;
        }

        $this->info("Found {$sites->count()} active site(s).");
        $grandTotalCreated = 0;
        $grandTotalSkipped = 0;
        $grandTotalErrors = 0;

        foreach ($sites as $site) {
            $this->newLine();
            $this->info("=== {$site->name} ({$site->domain}) ===");

            try {
                $this->tenantManager->setTenant($site);

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

                $existingPageSlugs = Page::pluck('slug')->toArray();
                $existingPostSlugs = Post::pluck('slug')->toArray();
                $allExistingSlugs = array_merge($existingPageSlugs, $existingPostSlugs);
                $clusterSlugs = $this->getClusterSlugs($slug);

                $siteCreated = 0;
                $siteSkipped = 0;
                $siteErrors = [];

                // ─── CHECK MISSING PAGES (15 total: 1 landing + 4 static + 10 cluster) ───

                $pageDefinitions = $this->buildPageDefinitions($brandName, $domain, $slug);
                $missingPages = array_filter($pageDefinitions, fn($p) => !in_array($p['slug'], $existingPageSlugs));

                $this->info("  Pages: " . count($existingPageSlugs) . " existing, " . count($missingPages) . " missing");

                foreach ($missingPages as $pageDef) {
                    $this->line("  [PAGE] Generating: {$pageDef['slug']}");

                    if ($dryRun) {
                        $this->line("    [DRY RUN] Would create page: {$pageDef['slug']}");
                        continue;
                    }

                    try {
                        $generated = $this->generatePage($pageDef, $brandName, $domain, $siteInfo, $clusterSlugs, $allExistingSlugs);

                        Page::create([
                            'slug' => $pageDef['slug'],
                            'title' => $generated['title'],
                            'content' => $generated['content'],
                            'meta_title' => $generated['meta_title'] ?? $generated['title'],
                            'meta_description' => $generated['meta_description'] ?? '',
                            'meta_keywords' => $generated['meta_keywords'] ?? '',
                            'is_published' => true,
                            'sort_order' => $pageDef['sort_order'],
                        ]);

                        $allExistingSlugs[] = $pageDef['slug'];
                        $siteCreated++;
                        $this->info("    Created page: {$generated['title']}");
                    } catch (\Throwable $e) {
                        $siteErrors[] = "Page '{$pageDef['slug']}': {$e->getMessage()}";
                        $this->error("    Error: {$e->getMessage()}");
                    }
                }

                // ─── CHECK MISSING POSTS ───

                $existingPostCount = count($existingPostSlugs);
                $targetPostCount = $postsCount;
                $postsToGenerate = max(0, $targetPostCount - $existingPostCount);

                $this->info("  Posts: {$existingPostCount} existing, target {$targetPostCount}, generating {$postsToGenerate}");

                if ($postsToGenerate > 0 && !$dryRun) {
                    $availableTopics = $this->aiService->getAvailableTopics($brandName, $existingPostSlugs);

                    if (empty($availableTopics)) {
                        $this->warn("    No available topics remaining for {$domain}");
                    } else {
                        $topicsToUse = array_slice($availableTopics, 0, $postsToGenerate);

                        foreach ($topicsToUse as $i => $topic) {
                            $topicName = str_replace(
                                ['{brand}', '{year}'],
                                [$brandName, date('Y')],
                                $topic['topic']
                            );

                            $this->line("  [POST] Generating: {$topic['full_slug']}");

                            try {
                                $generated = $this->aiService->generateDailyPost(
                                    $brandName,
                                    $domain,
                                    $topic['topic'],
                                    $topic['instructions'],
                                    $existingPostSlugs,
                                    $siteInfo,
                                );

                                // Auto-assign category
                                $categoryId = $this->resolveCategoryId($topic['category'] ?? 'genel');

                                Post::create([
                                    'slug' => $topic['full_slug'],
                                    'title' => $generated['title'],
                                    'excerpt' => $generated['excerpt'] ?? '',
                                    'content' => $generated['content'],
                                    'meta_title' => $generated['meta_title'] ?? $generated['title'],
                                    'meta_description' => $generated['meta_description'] ?? '',
                                    'is_published' => true,
                                    'published_at' => now()->subDays($i * 2 + 1),
                                    'category_id' => $categoryId,
                                ]);

                                $existingPostSlugs[] = $topic['full_slug'];
                                $allExistingSlugs[] = $topic['full_slug'];
                                $siteCreated++;
                                $this->info("    Created post: {$generated['title']}");
                            } catch (\Throwable $e) {
                                $siteErrors[] = "Post '{$topic['full_slug']}': {$e->getMessage()}";
                                $this->error("    Error: {$e->getMessage()}");
                            }
                        }
                    }
                } elseif ($postsToGenerate > 0 && $dryRun) {
                    $availableTopics = $this->aiService->getAvailableTopics($brandName, $existingPostSlugs);
                    $topicsToUse = array_slice($availableTopics, 0, $postsToGenerate);
                    foreach ($topicsToUse as $topic) {
                        $this->line("    [DRY RUN] Would create post: {$topic['full_slug']}");
                    }
                }

                $grandTotalCreated += $siteCreated;
                $grandTotalSkipped += $siteSkipped;
                $grandTotalErrors += count($siteErrors);

                $this->info("  Summary: Created={$siteCreated}, Errors=" . count($siteErrors));

                if (!empty($siteErrors)) {
                    foreach ($siteErrors as $err) {
                        Log::error('GenerateMissing: ' . $err, ['site' => $domain]);
                    }
                }
            } catch (\Throwable $e) {
                $grandTotalErrors++;
                $this->error("  Site error: {$e->getMessage()}");
                Log::error('GenerateMissing: Site failed', [
                    'site' => $site->domain,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("All done! Created={$grandTotalCreated}, Skipped={$grandTotalSkipped}, Errors={$grandTotalErrors}");

        return $grandTotalErrors > 0 && $grandTotalCreated === 0 ? 1 : 0;
    }

    private function generatePage(array $pageDef, string $brandName, string $domain, array $siteInfo, array $clusterSlugs, array $existingSlugs): array
    {
        return match ($pageDef['type']) {
            'landing' => $this->aiService->generateLandingPage($brandName, $domain, $siteInfo, $existingSlugs),
            'static' => $this->aiService->generateStaticPage($brandName, $domain, $pageDef['page_type'], $pageDef['instructions'], $siteInfo),
            'cluster' => $this->aiService->generateClusterArticle($brandName, $domain, $pageDef['topic'], $pageDef['instructions'], $siteInfo, $clusterSlugs, $existingSlugs),
        };
    }

    private function resolveCategoryId(string $categorySlug): ?int
    {
        $category = \App\Models\Category::on('tenant')->where('slug', $categorySlug)->first();
        return $category?->id;
    }

    private function buildPageDefinitions(string $brandName, string $domain, string $slug): array
    {
        $pages = [];

        $pages[] = ['slug' => 'anasayfa', 'type' => 'landing', 'topic' => "{$brandName} Ana Sayfa", 'instructions' => '', 'sort_order' => 0];

        $staticPages = [
            ['slug' => 'hakkimizda', 'page_type' => 'kurumsal', 'topic' => 'Hakkımızda', 'instructions' => "{$brandName} sitesinin hakkımızda sayfası. Sitenin misyonu, vizyonu, güvenilirliği ve lisans bilgileri hakkında profesyonel bir anlatım. Domain: {$domain}"],
            ['slug' => 'iletisim', 'page_type' => 'iletişim', 'topic' => 'İletişim', 'instructions' => "{$brandName} sitesinin iletişim sayfası. E-posta, Telegram ve canlı destek bilgileri. Domain: {$domain}"],
            ['slug' => 'gizlilik-politikasi', 'page_type' => 'yasal', 'topic' => 'Gizlilik Politikası', 'instructions' => "{$brandName} sitesinin gizlilik politikası. KVKK ve GDPR uyumlu yasal metin. Domain: {$domain}"],
            ['slug' => 'sorumluluk-reddi', 'page_type' => 'yasal', 'topic' => 'Sorumluluk Reddi', 'instructions' => "{$brandName} sitesinin sorumluluk reddi. Bahis riskleri, 18 yaş sınırı, sorumlu bahis ilkeleri. Domain: {$domain}"],
        ];
        foreach ($staticPages as $i => $sp) {
            $pages[] = array_merge($sp, ['type' => 'static', 'sort_order' => $i + 1]);
        }

        $clusterPages = [
            ['slug' => "{$slug}-giris", 'topic' => "{$brandName} Giriş", 'instructions' => "{$brandName} platformuna giriş rehberi. 1500-2200 kelime."],
            ['slug' => "{$slug}-yeni-giris", 'topic' => "{$brandName} Yeni Giriş Adresi", 'instructions' => "{$brandName} yeni giriş adresi bilgilendirme. 1500-2200 kelime."],
            ['slug' => "{$slug}-guncel-adres", 'topic' => "{$brandName} Güncel Adres " . date('Y'), 'instructions' => "{$brandName} " . date('Y') . " güncel erişim bilgileri. 1500-2200 kelime."],
            ['slug' => "{$slug}-bonus", 'topic' => "{$brandName} Bonus ve Kampanyalar", 'instructions' => "{$brandName} bonus türleri ve çevrim şartları. 1500-2200 kelime."],
            ['slug' => "{$slug}-mobil-giris", 'topic' => "{$brandName} Mobil Giriş", 'instructions' => "{$brandName} mobil giriş rehberi. 1500-2200 kelime."],
            ['slug' => "{$slug}-casino", 'topic' => "{$brandName} Casino Oyunları Rehberi", 'instructions' => "{$brandName} casino oyunları rehberi. Rulet, blackjack, poker, baccarat gibi masa oyunları ve casino deneyimi. 1500-2200 kelime."],
            ['slug' => "{$slug}-slot", 'topic' => "{$brandName} Slot Oyunları Rehberi", 'instructions' => "{$brandName} slot oyunları detaylı rehberi. Popüler slotlar, RTP oranları, bonus özellikler, free spin mekanikleri. 1500-2200 kelime."],
            ['slug' => "{$slug}-canli-casino", 'topic' => "{$brandName} Canlı Casino Deneyimi", 'instructions' => "{$brandName} canlı casino rehberi. Canlı rulet, blackjack, game show oyunları, gerçek krupiyelerle deneyim. 1500-2200 kelime."],
            ['slug' => "{$slug}-kayit", 'topic' => "{$brandName} Kayıt ve Üyelik Rehberi", 'instructions' => "{$brandName} üyelik açma adım adım rehber. Kayıt formu, hesap doğrulama, ilk giriş adımları. 1500-2200 kelime."],
            ['slug' => "{$slug}-para-yatirma", 'topic' => "{$brandName} Para Yatırma ve Çekme Rehberi", 'instructions' => "{$brandName} ödeme yöntemleri rehberi. Papara, kripto, banka havalesi. Limitler, süreler, komisyonlar. 1500-2200 kelime."],
        ];
        foreach ($clusterPages as $i => $cp) {
            $pages[] = array_merge($cp, ['type' => 'cluster', 'sort_order' => 10 + $i]);
        }

        return $pages;
    }

    private function getClusterSlugs(string $slug): array
    {
        return [
            "{$slug}-giris", "{$slug}-yeni-giris", "{$slug}-guncel-adres",
            "{$slug}-bonus", "{$slug}-mobil-giris", "{$slug}-casino",
            "{$slug}-slot", "{$slug}-canli-casino", "{$slug}-kayit",
            "{$slug}-para-yatirma",
        ];
    }
}
