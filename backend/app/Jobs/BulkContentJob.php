<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Concerns\HandlesTenantContext;
use App\Models\Page;
use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\ImageGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class BulkContentJob implements ShouldQueue
{
    use HandlesTenantContext, Queueable;

    public int $tries = 1;
    public int $timeout = 1800;

    public function __construct(
        public int $siteId,
        public int $botTaskId,
        public string $contentType, // 'pages', 'posts', 'daily', 'all'
        public string $provider,
        public bool $overwrite = false,
        public int $dailyCount = 2,
    ) {}

    public function handle(AIContentService $aiService, ImageGenerationService $imageService): void
    {
        $this->updateTaskStatus($this->botTaskId, 'processing', 5);

        try {
            $site = $this->resolveTenant($this->siteId);

            Config::set('ai.provider', $this->provider);

            $brandName = $site->name;
            $domain = $site->domain;
            $siteInfo = ['country' => 'Türkiye'];

            $existingPageSlugs = Page::pluck('slug')->toArray();
            $existingPostSlugs = Post::pluck('slug')->toArray();
            $allExistingSlugs = array_merge($existingPageSlugs, $existingPostSlugs);

            $slug = Str::slug($brandName);
            $clusterSlugs = [
                "{$slug}-giris",
                "{$slug}-yeni-giris",
                "{$slug}-guncel-adres",
                "{$slug}-bonus",
                "{$slug}-mobil-giris",
            ];

            $created = 0;
            $skipped = 0;
            $errors = [];

            // ─── PAGES ───
            if (in_array($this->contentType, ['pages', 'all'])) {
                $this->updateTaskStatus($this->botTaskId, 'processing', 10);

                // Landing page
                $existingLanding = Page::where('slug', 'anasayfa')->first();
                if ($existingLanding && !$this->overwrite) {
                    $skipped++;
                } else {
                    try {
                        if ($existingLanding && $this->overwrite) {
                            $existingLanding->delete();
                        }
                        $generated = $aiService->generateLandingPage($brandName, $domain, $siteInfo, $allExistingSlugs);
                        Page::create([
                            'slug' => 'anasayfa',
                            'title' => $generated['title'],
                            'content' => $generated['content'],
                            'meta_title' => $generated['meta_title'],
                            'meta_description' => $generated['meta_description'],
                            'meta_keywords' => $generated['meta_keywords'],
                            'is_published' => true,
                            'sort_order' => 0,
                        ]);
                        $created++;
                    } catch (\Throwable $e) {
                        $errors[] = "Landing: {$e->getMessage()}";
                    }
                }

                $this->updateTaskStatus($this->botTaskId, 'processing', 20);

                // Static pages
                $staticPages = $this->getStaticPages($brandName, $domain);
                foreach ($staticPages as $i => $pageDef) {
                    try {
                        $existing = Page::where('slug', $pageDef['slug'])->first();
                        if ($existing && !$this->overwrite) {
                            $skipped++;
                            continue;
                        }
                        if ($existing && $this->overwrite) {
                            $existing->delete();
                        }
                        $generated = $aiService->generateStaticPage(
                            $brandName, $domain, $pageDef['type'], $pageDef['instructions'], $siteInfo
                        );
                        Page::create([
                            'slug' => $pageDef['slug'],
                            'title' => $generated['title'],
                            'content' => $generated['content'],
                            'meta_title' => $generated['meta_title'],
                            'meta_description' => $generated['meta_description'],
                            'is_published' => true,
                            'sort_order' => $i + 1,
                        ]);
                        $created++;
                    } catch (\Throwable $e) {
                        $errors[] = "Sayfa '{$pageDef['slug']}': {$e->getMessage()}";
                    }
                }

                $this->updateTaskStatus($this->botTaskId, 'processing', 35);

                // Cluster pages
                $clusterPages = $this->getClusterPages($brandName, $domain);
                foreach ($clusterPages as $i => $pageDef) {
                    try {
                        $existing = Page::where('slug', $pageDef['slug'])->first();
                        if ($existing && !$this->overwrite) {
                            $skipped++;
                            continue;
                        }
                        if ($existing && $this->overwrite) {
                            $existing->delete();
                        }
                        $generated = $aiService->generateClusterArticle(
                            $brandName, $domain, $pageDef['topic'], $pageDef['instructions'], $siteInfo, $clusterSlugs, $allExistingSlugs
                        );
                        Page::create([
                            'slug' => $pageDef['slug'],
                            'title' => $generated['title'],
                            'content' => $generated['content'],
                            'meta_title' => $generated['meta_title'],
                            'meta_description' => $generated['meta_description'],
                            'is_published' => true,
                            'sort_order' => 10 + $i,
                        ]);
                        $created++;
                    } catch (\Throwable $e) {
                        $errors[] = "Cluster '{$pageDef['slug']}': {$e->getMessage()}";
                    }
                }
            }

            $this->updateTaskStatus($this->botTaskId, 'processing', 50);

            // ─── POSTS ───
            if (in_array($this->contentType, ['posts', 'all'])) {
                $postTopics = $this->getClusterArticles($brandName, $domain);
                $totalPosts = count($postTopics);

                foreach ($postTopics as $i => $topic) {
                    try {
                        $topicSlug = Str::slug($topic['suggested_slug']);
                        $existing = Post::where('slug', $topicSlug)->first();
                        if ($existing && !$this->overwrite) {
                            $skipped++;
                            continue;
                        }
                        if ($existing && $this->overwrite) {
                            $existing->delete();
                        }

                        $generated = $aiService->generateClusterArticle(
                            $brandName, $domain, $topic['topic'], $topic['instructions'], $siteInfo, $clusterSlugs, $allExistingSlugs
                        );

                        $featuredImage = $imageService->generateFeaturedImage($topic['topic'], $brandName);

                        Post::create([
                            'slug' => $generated['slug'] ?? $topicSlug,
                            'title' => $generated['title'],
                            'excerpt' => $generated['excerpt'] ?? '',
                            'content' => $generated['content'],
                            'featured_image' => $featuredImage,
                            'meta_title' => $generated['meta_title'] ?? $generated['title'],
                            'meta_description' => $generated['meta_description'] ?? '',
                            'is_published' => true,
                            'published_at' => now()->subDays($i * 2 + 1),
                        ]);
                        $created++;
                    } catch (\Throwable $e) {
                        $errors[] = "Yazı '{$topic['topic']}': {$e->getMessage()}";
                    }

                    $progress = 50 + (int) (($i + 1) / max($totalPosts, 1) * 30);
                    $this->updateTaskStatus($this->botTaskId, 'processing', min($progress, 80));
                }
            }

            // ─── DAILY POSTS ───
            if (in_array($this->contentType, ['daily', 'all'])) {
                $topics = $aiService->pickTopics($brandName, $existingPostSlugs, $this->dailyCount);

                foreach ($topics as $i => $topic) {
                    try {
                        $topicName = str_replace(['{brand}', '{year}'], [$brandName, date('Y')], $topic['topic']);

                        $generated = $aiService->generateDailyPost(
                            $brandName, $domain, $topic['topic'], $topic['instructions'], $existingPostSlugs
                        );

                        $imagePrompt = $generated['featured_image_prompt'] ?? null;
                        $featuredImage = $imageService->generateFeaturedImage($topicName, $brandName, $imagePrompt);

                        Post::create([
                            'slug' => $topic['full_slug'],
                            'title' => $generated['title'],
                            'excerpt' => $generated['excerpt'] ?? '',
                            'content' => $generated['content'],
                            'featured_image' => $featuredImage,
                            'meta_title' => $generated['meta_title'] ?? $generated['title'],
                            'meta_description' => $generated['meta_description'] ?? '',
                            'is_published' => true,
                            'published_at' => now(),
                        ]);

                        $existingPostSlugs[] = $topic['full_slug'];
                        $created++;
                    } catch (\Throwable $e) {
                        $errors[] = "Günlük '{$topic['topic']}': {$e->getMessage()}";
                    }
                }
            }

            $this->updateTaskStatus($this->botTaskId, 'completed', 100, [
                'site_id' => $this->siteId,
                'domain' => $domain,
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
            ]);
        } catch (\Throwable $e) {
            $this->updateTaskStatus($this->botTaskId, 'failed', 0, null, $e->getMessage());
            throw $e;
        }
    }

    private function getStaticPages(string $brandName, string $domain): array
    {
        return [
            ['slug' => 'hakkimizda', 'type' => 'kurumsal', 'instructions' => "{$brandName} sitesinin hakkımızda sayfası. Sitenin misyonu, vizyonu, güvenilirliği ve lisans bilgileri hakkında profesyonel bir anlatım. Domain: {$domain}"],
            ['slug' => 'iletisim', 'type' => 'iletişim', 'instructions' => "{$brandName} sitesinin iletişim sayfası. E-posta, Telegram ve canlı destek bilgileri. Domain: {$domain}"],
            ['slug' => 'gizlilik-politikasi', 'type' => 'yasal', 'instructions' => "{$brandName} sitesinin gizlilik politikası. KVKK ve GDPR uyumlu yasal metin. Domain: {$domain}"],
            ['slug' => 'sorumluluk-reddi', 'type' => 'yasal', 'instructions' => "{$brandName} sitesinin sorumluluk reddi. Bahis riskleri, 18 yaş sınırı, sorumlu bahis ilkeleri. Domain: {$domain}"],
        ];
    }

    private function getClusterPages(string $brandName, string $domain): array
    {
        $slug = Str::slug($brandName);
        return [
            ['slug' => "{$slug}-giris", 'topic' => "{$brandName} Giriş", 'instructions' => "{$brandName} platformuna giriş rehberi. 1500-2200 kelime."],
            ['slug' => "{$slug}-yeni-giris", 'topic' => "{$brandName} Yeni Giriş Adresi", 'instructions' => "{$brandName} yeni giriş adresi bilgilendirme. 1500-2200 kelime."],
            ['slug' => "{$slug}-guncel-adres", 'topic' => "{$brandName} Güncel Adres " . date('Y'), 'instructions' => "{$brandName} " . date('Y') . " güncel erişim bilgileri. 1500-2200 kelime."],
            ['slug' => "{$slug}-bonus", 'topic' => "{$brandName} Bonus ve Kampanyalar", 'instructions' => "{$brandName} bonus türleri ve çevrim şartları. 1500-2200 kelime."],
            ['slug' => "{$slug}-mobil-giris", 'topic' => "{$brandName} Mobil Giriş", 'instructions' => "{$brandName} mobil giriş rehberi. 1500-2200 kelime."],
        ];
    }

    private function getClusterArticles(string $brandName, string $domain): array
    {
        $slug = Str::slug($brandName);
        return [
            ['suggested_slug' => "{$slug}-giris-acilmiyor-cozum", 'topic' => "{$brandName} giriş açılmıyor çözüm", 'instructions' => "Giriş yapamama çözüm rehberi. 1500-2200 kelime."],
            ['suggested_slug' => "{$slug}-guncel-adres-nasil-bulunur", 'topic' => "{$brandName} güncel adres nasıl bulunur?", 'instructions' => "Güncel adres bulma yöntemleri. 1500-2200 kelime."],
            ['suggested_slug' => "{$slug}-mobil-giris-rehberi", 'topic' => "{$brandName} mobil giriş rehberi", 'instructions' => "Mobil giriş ve kullanım rehberi. 1500-2200 kelime."],
        ];
    }
}
