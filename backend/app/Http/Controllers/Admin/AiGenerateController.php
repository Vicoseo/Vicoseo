<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\ImageGenerationService;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AiGenerateController extends Controller
{
    public function __construct(
        private TenantManager $tenantManager,
        private AIContentService $aiService,
        private ImageGenerationService $imageService,
    ) {}

    public function generate(Request $request, int $siteId): JsonResponse
    {
        set_time_limit(600);

        $request->validate([
            'provider' => ['required', 'in:openai,anthropic'],
            'content_type' => ['required', 'in:pages,posts,daily,all'],
        ]);

        $provider = $request->input('provider');
        $contentType = $request->input('content_type');

        // Validate API key exists
        $apiKey = config("ai.{$provider}.api_key");
        if (empty($apiKey)) {
            $label = $provider === 'openai' ? 'OPENAI_API_KEY' : 'ANTHROPIC_API_KEY';
            return response()->json([
                'message' => "{$label} .env dosyasında tanımlanmamış. Lütfen API anahtarınızı ekleyin.",
            ], 422);
        }

        // Resolve site & tenant
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);

        // Override provider for this request
        Config::set('ai.provider', $provider);

        $brandName = $site->name;
        $domain = $site->domain;
        $siteInfo = [
            'country' => 'Türkiye',
            'social_links' => $site->social_links ?? [],
        ];

        // Collect existing slugs for uniqueness
        $existingPageSlugs = Page::pluck('slug')->toArray();
        $existingPostSlugs = Post::pluck('slug')->toArray();
        $allExistingSlugs = array_merge($existingPageSlugs, $existingPostSlugs);

        // Collect cluster page slugs for internal linking
        $slug = Str::slug($brandName);
        $clusterSlugs = [
            "{$slug}-giris",
            "{$slug}-yeni-giris",
            "{$slug}-guncel-adres",
            "{$slug}-bonus",
            "{$slug}-mobil-giris",
        ];

        $results = ['pages' => [], 'posts' => []];
        $errors = [];

        // ─── GENERATE PAGES ───
        if (in_array($contentType, ['pages', 'all'])) {
            // 1) Main Landing Page (SEO paravan content)
            try {
                $existing = Page::where('slug', 'anasayfa')->first();
                if ($existing) {
                    $results['pages'][] = ['title' => $existing->title, 'status' => 'skipped'];
                } else {
                    $generated = $this->aiService->generateLandingPage($brandName, $domain, $siteInfo, $allExistingSlugs);

                    $page = Page::create([
                        'slug' => 'anasayfa',
                        'title' => $generated['title'],
                        'content' => $generated['content'],
                        'meta_title' => $generated['meta_title'],
                        'meta_description' => $generated['meta_description'],
                        'meta_keywords' => $generated['meta_keywords'],
                        'is_published' => true,
                        'sort_order' => 0,
                    ]);

                    $results['pages'][] = ['title' => $generated['title'], 'status' => 'created'];
                }
            } catch (\Throwable $e) {
                $errors[] = "Ana Sayfa: " . $e->getMessage();
                $results['pages'][] = ['title' => 'Ana Sayfa', 'status' => 'error'];
            }

            // 2) Static pages
            $staticPages = $this->getStaticPages($brandName, $domain);
            foreach ($staticPages as $i => $pageDef) {
                try {
                    $existing = Page::where('slug', $pageDef['slug'])->first();
                    if ($existing) {
                        $results['pages'][] = ['title' => $existing->title, 'status' => 'skipped'];
                        continue;
                    }

                    $generated = $this->aiService->generateStaticPage(
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

                    $results['pages'][] = ['title' => $generated['title'], 'status' => 'created'];
                } catch (\Throwable $e) {
                    $errors[] = "Sayfa '{$pageDef['slug']}': " . $e->getMessage();
                    $results['pages'][] = ['title' => $pageDef['slug'], 'status' => 'error'];
                }
            }

            // 3) Cluster SEO pages (slug-plan based)
            $clusterPages = $this->getClusterPages($brandName, $domain);
            foreach ($clusterPages as $i => $pageDef) {
                try {
                    $existing = Page::where('slug', $pageDef['slug'])->first();
                    if ($existing) {
                        $results['pages'][] = ['title' => $existing->title, 'status' => 'skipped'];
                        continue;
                    }

                    $generated = $this->aiService->generateClusterArticle(
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

                    $results['pages'][] = ['title' => $generated['title'], 'status' => 'created'];
                } catch (\Throwable $e) {
                    $errors[] = "Cluster sayfa '{$pageDef['slug']}': " . $e->getMessage();
                    $results['pages'][] = ['title' => $pageDef['slug'], 'status' => 'error'];
                }
            }
        }

        // ─── GENERATE POSTS (supporting cluster articles) ───
        if (in_array($contentType, ['posts', 'all'])) {
            $postTopics = $this->getClusterArticles($brandName, $domain);

            foreach ($postTopics as $i => $topic) {
                try {
                    $topicSlug = Str::slug($topic['suggested_slug']);
                    $existing = Post::where('slug', $topicSlug)->first();
                    if ($existing) {
                        $results['posts'][] = ['title' => $existing->title, 'status' => 'skipped'];
                        continue;
                    }

                    $generated = $this->aiService->generateClusterArticle(
                        $brandName, $domain, $topic['topic'], $topic['instructions'], $siteInfo, $clusterSlugs, $allExistingSlugs
                    );

                    // Generate featured image for blog posts
                    $featuredImage = $this->imageService->generateFeaturedImage(
                        $topic['topic'], $brandName
                    );

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

                    $results['posts'][] = ['title' => $generated['title'], 'status' => 'created'];
                } catch (\Throwable $e) {
                    $errors[] = "Yazı '{$topic['topic']}': " . $e->getMessage();
                    $results['posts'][] = ['title' => $topic['topic'], 'status' => 'error'];
                }
            }
        }

        // ─── GENERATE DAILY POSTS (from topic pool) ───
        if ($contentType === 'daily') {
            $count = (int) $request->input('count', 2);
            $topics = $this->aiService->pickTopics($brandName, $existingPostSlugs, $count);

            if (empty($topics)) {
                return response()->json([
                    'message' => 'Tüm konular zaten kullanılmış. Yeni konu havuzu tükenmiş.',
                    'data' => $results,
                    'errors' => [],
                ]);
            }

            foreach ($topics as $topic) {
                try {
                    $topicName = str_replace(['{brand}', '{year}'], [$brandName, date('Y')], $topic['topic']);

                    $generated = $this->aiService->generateDailyPost(
                        $brandName, $domain, $topic['topic'], $topic['instructions'], $existingPostSlugs, $siteInfo
                    );

                    // Generate featured image
                    $imagePrompt = $generated['featured_image_prompt'] ?? null;
                    $featuredImage = $this->imageService->generateFeaturedImage(
                        $topicName, $brandName, $imagePrompt
                    );

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
                    $results['posts'][] = ['title' => $generated['title'], 'status' => 'created'];
                } catch (\Throwable $e) {
                    $errors[] = "Günlük yazı '{$topic['topic']}': " . $e->getMessage();
                    $results['posts'][] = ['title' => $topic['topic'] ?? 'Unknown', 'status' => 'error'];
                }
            }
        }

        $createdPages = collect($results['pages'])->where('status', 'created')->count();
        $createdPosts = collect($results['posts'])->where('status', 'created')->count();
        $skippedPages = collect($results['pages'])->where('status', 'skipped')->count();
        $skippedPosts = collect($results['posts'])->where('status', 'skipped')->count();

        $providerLabel = $provider === 'openai' ? 'ChatGPT' : 'Claude';
        $message = "{$providerLabel} ile {$createdPages} sayfa, {$createdPosts} yazı oluşturuldu.";
        if ($skippedPages + $skippedPosts > 0) {
            $message .= " ({$skippedPages} sayfa, {$skippedPosts} yazı zaten mevcut — atlandı.)";
        }

        return response()->json([
            'message' => $message,
            'data' => $results,
            'errors' => $errors,
        ], count($errors) > 0 && $createdPages + $createdPosts === 0 ? 500 : 200);
    }

    /**
     * Static/legal pages that every site needs.
     */
    private function getStaticPages(string $brandName, string $domain): array
    {
        return [
            [
                'slug' => 'hakkimizda',
                'type' => 'kurumsal',
                'instructions' => "{$brandName} sitesinin hakkımızda sayfası. Sitenin misyonu, vizyonu, güvenilirliği ve lisans bilgileri hakkında profesyonel bir anlatım. Kurumsal ton kullan. Domain: {$domain}",
            ],
            [
                'slug' => 'iletisim',
                'type' => 'iletişim',
                'instructions' => "{$brandName} sitesinin iletişim sayfası. E-posta, Telegram ve canlı destek bilgileri. Kısa, profesyonel ve güven veren bir yapıda olsun. Domain: {$domain}",
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'type' => 'yasal',
                'instructions' => "{$brandName} sitesinin gizlilik politikası. Kişisel verilerin korunması, çerezler, üçüncü taraf paylaşımı. KVKK ve GDPR uyumlu profesyonel yasal metin. Domain: {$domain}",
            ],
            [
                'slug' => 'sorumluluk-reddi',
                'type' => 'yasal',
                'instructions' => "{$brandName} sitesinin sorumluluk reddi. Bahis riskleri, 18 yaş sınırı, sorumlu bahis ilkeleri, 7258 sayılı kanun referansı. Nötr ve bilgilendirici ton. Domain: {$domain}",
            ],
        ];
    }

    /**
     * Cluster SEO pages based on slug plan.
     */
    private function getClusterPages(string $brandName, string $domain): array
    {
        $slug = Str::slug($brandName);

        return [
            [
                'slug' => "{$slug}-giris",
                'topic' => "{$brandName} Giriş",
                'instructions' => "{$brandName} platformuna giriş rehberi. Güncel giriş adresi, adres değişikliği nedenleri, güvenli erişim yöntemleri. Doğal internal linkler ekle. 1500-2200 kelime.",
            ],
            [
                'slug' => "{$slug}-yeni-giris",
                'topic' => "{$brandName} Yeni Giriş Adresi",
                'instructions' => "{$brandName} yeni giriş adresi hakkında bilgilendirme. Adres güncelleme süreci, sahte sitelerden korunma, resmi kanal bilgileri. 1500-2200 kelime.",
            ],
            [
                'slug' => "{$slug}-guncel-adres",
                'topic' => "{$brandName} Güncel Adres " . date('Y'),
                'instructions' => "{$brandName} " . date('Y') . " yılı güncel erişim bilgileri. DNS değişiklikleri, VPN gerekliliği, alternatif erişim yöntemleri. 1500-2200 kelime.",
            ],
            [
                'slug' => "{$slug}-bonus",
                'topic' => "{$brandName} Bonus ve Kampanyalar",
                'instructions' => "{$brandName} bonus türleri: hoş geldin, kayıp iadesi, yatırım bonusu, arkadaş daveti. Çevrim şartları ve kullanım koşulları. 1500-2200 kelime.",
            ],
            [
                'slug' => "{$slug}-mobil-giris",
                'topic' => "{$brandName} Mobil Giriş",
                'instructions' => "{$brandName} mobil cihazlardan giriş rehberi. iOS ve Android uyumluluk, mobil site vs uygulama, ana ekrana ekleme. 1500-2200 kelime.",
            ],
        ];
    }

    /**
     * Supporting cluster blog articles.
     */
    private function getClusterArticles(string $brandName, string $domain): array
    {
        $slug = Str::slug($brandName);

        return [
            [
                'suggested_slug' => "{$slug}-giris-acilmiyor-cozum",
                'topic' => "{$brandName} giriş açılmıyor çözüm",
                'instructions' => "{$brandName} giriş yapamama sorunlarının çözüm rehberi. DNS sorunları, tarayıcı cache, VPN kullanımı, alternatif adresler. Kullanıcıların yaşadığı gerçek sorunlara pratik çözümler sun. 1500-2200 kelime.",
            ],
            [
                'suggested_slug' => "{$slug}-guncel-adres-nasil-bulunur",
                'topic' => "{$brandName} güncel adres nasıl bulunur?",
                'instructions' => "{$brandName} güncel giriş adresini güvenli şekilde bulma yöntemleri. Resmi kanallar, Telegram, sosyal medya takibi. Sahte sitelerden korunma uyarıları. 1500-2200 kelime.",
            ],
            [
                'suggested_slug' => "{$slug}-mobil-giris-rehberi",
                'topic' => "{$brandName} mobil giriş rehberi",
                'instructions' => "{$brandName} mobil cihazlardan giriş ve kullanım rehberi. Telefon ve tablet uyumluluğu, mobil bahis özellikleri, performans ipuçları. 1500-2200 kelime.",
            ],
        ];
    }
}
