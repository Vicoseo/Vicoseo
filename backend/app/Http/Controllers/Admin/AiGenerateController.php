<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
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
    ) {}

    public function generate(Request $request, int $siteId): JsonResponse
    {
        set_time_limit(600);

        $request->validate([
            'provider' => ['required', 'in:openai,anthropic'],
            'content_type' => ['required', 'in:pages,posts,all'],
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

        $siteInfo = [
            'site_name' => $site->name,
            'site_type' => 'bahis ve casino inceleme blogu',
            'country' => 'Türkiye',
        ];

        $results = ['pages' => [], 'posts' => []];
        $errors = [];

        // Generate pages
        if (in_array($contentType, ['pages', 'all'])) {
            $pageTopics = $this->getPageTopics($site);

            foreach ($pageTopics as $i => $topic) {
                try {
                    $existing = Page::where('slug', $topic['slug'])->first();
                    if ($existing) {
                        $results['pages'][] = ['title' => $existing->title, 'status' => 'skipped'];
                        continue;
                    }

                    $generated = $this->aiService->generateContent(
                        'page',
                        $topic['title'],
                        $topic['instructions'],
                        array_merge($siteInfo, [
                            'page_type' => $topic['type'],
                            'keywords' => $topic['keywords'],
                        ])
                    );

                    Page::create([
                        'slug' => $topic['slug'],
                        'title' => $generated['title'],
                        'content' => $generated['content'],
                        'meta_title' => $generated['meta_title'] ?? $generated['title'],
                        'meta_description' => $generated['meta_description'] ?? '',
                        'is_published' => true,
                        'sort_order' => $i,
                    ]);

                    $results['pages'][] = ['title' => $generated['title'], 'status' => 'created'];
                } catch (\Throwable $e) {
                    $errors[] = "Sayfa '{$topic['title']}': " . $e->getMessage();
                    $results['pages'][] = ['title' => $topic['title'], 'status' => 'error'];
                }
            }
        }

        // Generate posts
        if (in_array($contentType, ['posts', 'all'])) {
            $postTopics = $this->getPostTopics($site);

            foreach ($postTopics as $i => $topic) {
                try {
                    $slug = Str::slug($topic['title']);
                    $existing = Post::where('slug', $slug)->first();
                    if ($existing) {
                        $results['posts'][] = ['title' => $existing->title, 'status' => 'skipped'];
                        continue;
                    }

                    $generated = $this->aiService->generateContent(
                        'post',
                        $topic['title'],
                        $topic['instructions'],
                        array_merge($siteInfo, [
                            'page_type' => 'blog yazısı',
                            'keywords' => $topic['keywords'],
                        ])
                    );

                    Post::create([
                        'slug' => $generated['slug'] ?? $slug,
                        'title' => $generated['title'],
                        'excerpt' => $generated['excerpt'] ?? '',
                        'content' => $generated['content'],
                        'meta_title' => $generated['meta_title'] ?? $generated['title'],
                        'meta_description' => $generated['meta_description'] ?? '',
                        'is_published' => true,
                        'published_at' => now()->subDays($i * 3 + 1),
                    ]);

                    $results['posts'][] = ['title' => $generated['title'], 'status' => 'created'];
                } catch (\Throwable $e) {
                    $errors[] = "Yazı '{$topic['title']}': " . $e->getMessage();
                    $results['posts'][] = ['title' => $topic['title'], 'status' => 'error'];
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

    private function getPageTopics(Site $site): array
    {
        $name = $site->name;

        return [
            [
                'slug' => 'ana-sayfa',
                'title' => "{$name} - Ana Sayfa",
                'type' => 'anasayfa',
                'keywords' => "{$name}, bahis, casino, bonus",
                'instructions' => "Bu {$name} sitesinin ana sayfasıdır. Siteyi tanıtan, güvenilirliğini vurgulayan ve kullanıcıyı içeriklere yönlendiren bir ana sayfa oluştur. CTA (call-to-action) öğeleri ekle.",
            ],
            [
                'slug' => 'hakkimizda',
                'title' => "{$name} Hakkında",
                'type' => 'kurumsal',
                'keywords' => "{$name}, hakkımızda, ekip",
                'instructions' => "{$name} sitesinin hakkımızda sayfası. Sitenin misyonu, vizyonu, ekibi ve güvenilirliği hakkında bilgi ver. SSS bölümünde siteyle ilgili sorular olsun.",
            ],
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'type' => 'iletişim',
                'keywords' => 'iletişim, destek, e-posta',
                'instructions' => "{$name} sitesinin iletişim sayfası. E-posta, Telegram, sosyal medya kanalları ve işbirliği bilgileri olsun. Kısa ve profesyonel olsun. SSS bölümünde iletişimle ilgili sorular olsun.",
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => 'Gizlilik Politikası',
                'type' => 'yasal',
                'keywords' => 'gizlilik politikası, KVKK, çerezler',
                'instructions' => "{$name} sitesinin gizlilik politikası. Kişisel verilerin korunması, çerezler, üçüncü taraf bağlantıları hakkında bilgi ver. Yasal bir dille yazılsın.",
            ],
            [
                'slug' => 'sorumluluk-reddi',
                'title' => 'Sorumluluk Reddi',
                'type' => 'yasal',
                'keywords' => 'sorumluluk reddi, yasal uyarı, 18 yaş',
                'instructions' => "{$name} sitesinin sorumluluk reddi sayfası. Bahis risklerini, 18 yaş sınırını, sorumlu bahis ilkelerini ve yasal uyarıları içersin.",
            ],
        ];
    }

    private function getPostTopics(Site $site): array
    {
        $name = $site->name;
        $desc = $site->meta_description ?? 'bahis ve casino inceleme blogu';

        return [
            [
                'title' => "2026 En İyi Bahis Siteleri - {$name} Önerileri",
                'keywords' => 'en iyi bahis siteleri, güvenilir bahis, 2026',
                'instructions' => "{$name} editörleri tarafından hazırlanan 2026 yılının en güvenilir bahis siteleri listesi. Lisans, ödeme, bonus ve müşteri hizmetleri kriterlerini değerlendir.",
            ],
            [
                'title' => 'Deneme Bonusu Veren Siteler 2026 Güncel Liste',
                'keywords' => 'deneme bonusu, bedava bonus, yatırımsız bonus, 2026',
                'instructions' => "Deneme bonusu kavramını açıkla, farklı türlerini (yatırımsız, free spin, ilk yatırım) anlat. Dikkat edilmesi gereken çevirme şartlarını detaylandır.",
            ],
            [
                'title' => 'Canlı Bahis Taktikleri ve Kazanma Stratejileri',
                'keywords' => 'canlı bahis, bahis taktikleri, kazanma stratejileri',
                'instructions' => "Canlı bahiste başarılı olmanın yolları. Bankroll yönetimi, maç analizi, istatistik takibi ve duygusal kontrol konularını ele al.",
            ],
            [
                'title' => "Casino Oyunları Rehberi: Slot, Rulet ve Blackjack",
                'keywords' => 'casino oyunları, slot, rulet, blackjack, canlı casino',
                'instructions' => "Popüler casino oyunlarını tanıt. Her oyun türü için temel strateji ve kuralları açıkla. RTP kavramını anlat.",
            ],
            [
                'title' => 'Bahis Sitelerine Para Yatırma ve Çekme Rehberi',
                'keywords' => 'para yatırma, para çekme, papara, kripto, banka havalesi',
                'instructions' => "Bahis sitelerinde kullanılan ödeme yöntemlerini (banka havalesi, Papara, kripto para, e-cüzdan) detaylıca anlat. Güvenlik ipuçları ver.",
            ],
        ];
    }
}
