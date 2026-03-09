<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\ImageGenerationService;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateRamadanContent extends Command
{
    protected $signature = 'content:generate-ramadan
        {--site= : Generate only for a specific domain}
        {--count=3 : Number of posts to generate per site}
        {--dry-run : Show what would be generated without actually creating content}
        {--no-image : Skip image generation}';

    protected $description = 'Generate Ramadan & earning-money-from-home themed blog posts for all sites';

    /**
     * Large topic pool - each site gets different topics via rotation.
     */
    private const TOPIC_POOL = [
        [
            'slug_suffix' => 'ramazanda-evden-para-kazanmanin-yollari',
            'topic' => 'Ramazanda Evden Para Kazanmanın En Etkili Yolları 2026',
            'instructions' => 'Ramazan ayında evden çalışarak para kazanmanın en etkili yollarını detaylı şekilde anlat. Freelance iş, online satış, dijital pazarlama, içerik üretimi, e-ticaret gibi farklı yöntemleri kapsayan kapsamlı rehber. Her yöntem için tahmini kazanç potansiyeli ve başlangıç adımlarını belirt. Ramazan ayının esnek çalışma saatleri avantajını vurgula.',
        ],
        [
            'slug_suffix' => 'ramazan-ek-gelir-fursatlari',
            'topic' => 'Ramazan Ayında Ek Gelir Fırsatları: Kapsamlı Rehber',
            'instructions' => 'Ramazan döneminde artan online alışveriş, iftar hazırlıkları ve dijital hizmet talebinden yararlanarak ek gelir elde etme fırsatlarını detaylı şekilde anlat. Yemek siparişi platformları, online eğitim, sosyal medya yönetimi, sanal asistanlık gibi alanları incele. Ramazan özel kampanya fikirlerini de dahil et.',
        ],
        [
            'slug_suffix' => 'ramazanda-freelance-calisma',
            'topic' => 'Ramazanda Freelance Çalışma Rehberi: Esnek Saatlerle Kazanç',
            'instructions' => 'Ramazan ayında oruç tutarken freelance çalışmanın avantajlarını ve yöntemlerini anlat. Upwork, Fiverr, Freelancer gibi platformları, Türkiye\'deki freelance iş bulma sitelerini detaylandır. Web geliştirme, grafik tasarım, çeviri, copywriting gibi freelance alanları incele. Ramazana uygun çalışma saatleri önerisi sun.',
        ],
        [
            'slug_suffix' => 'ramazanda-online-satis-rehberi',
            'topic' => 'Ramazanda Online Satış ile Para Kazanma: A\'dan Z\'ye Rehber',
            'instructions' => 'Ramazan döneminde online satış yaparak para kazanmanın yollarını anlat. Trendyol, Hepsiburada, n11 gibi pazaryerlerinde mağaza açma, Instagram üzerinden satış, el yapımı ürünler, Ramazan özel ürün fikirleri. Dropshipping ve stoksuz satış modellerini de dahil et. Ramazan hediye setleri, iftar süsleri gibi dönemsel ürün fikirlerini de ekle.',
        ],
        [
            'slug_suffix' => 'ramazanda-dijital-para-kazanma',
            'topic' => 'Ramazanda Dijital Ortamda Para Kazanmanın 15 Yolu',
            'instructions' => 'Dijital ortamda para kazanmanın 15 farklı yolunu liste halinde detaylı şekilde anlat. Blog yazarlığı, YouTube, podcast, affiliate marketing, online anketler, uygulama testi, veri girişi, sanal asistanlık, sosyal medya yönetimi, e-kitap yazma, online kurs hazırlama, stock fotoğraf satışı gibi yöntemleri Ramazan bağlamında ele al.',
        ],
        [
            'slug_suffix' => 'ramazanda-pasif-gelir-kaynaklari',
            'topic' => 'Ramazan Ayında Pasif Gelir Kaynakları Oluşturma Rehberi',
            'instructions' => 'Ramazan ayında başlayıp uzun vadede pasif gelir sağlayacak kaynak oluşturma yollarını anlat. E-kitap yazma ve satma, online kurs hazırlama, affiliate blog kurma, YouTube kanalı açma, dijital ürün satışı, baskı üzerine tasarım (print-on-demand). Her yöntem için başlangıç maliyeti, beklenen getiri süresi ve Ramazan avantajlarını belirt.',
        ],
        [
            'slug_suffix' => 'ramazanda-sosyal-medyadan-kazanc',
            'topic' => 'Ramazanda Sosyal Medyadan Para Kazanma Stratejileri',
            'instructions' => 'Ramazan döneminde sosyal medya üzerinden gelir elde etme stratejilerini anlat. Instagram, TikTok, YouTube Shorts ile içerik üretimi, marka işbirlikleri, sponsorlu paylaşımlar, sosyal medya danışmanlığı. Ramazan temalı içerik fikirleri ve viral olma taktiklerini de paylaş. Engagement artırma yöntemleri ve para kazanma eşiklerini belirt.',
        ],
        [
            'slug_suffix' => 'ramazanda-evden-is-fikirleri',
            'topic' => 'Ramazan\'da Evden Yapılabilecek 20 İş Fikri',
            'instructions' => 'Ramazan ayında evden yapılabilecek 20 farklı iş fikrini detaylı şekilde listele. Her iş fikri için gerekli yatırım, beceriler, kazanç potansiyeli ve başlangıç adımlarını anlat. El işi ürünler, yemek siparişi, online eğitim, teknik destek, muhasebe, tercüme gibi farklı kategorilerde iş fikirleri sun.',
        ],
        [
            'slug_suffix' => 'ramazanda-internet-uzerinden-gelir',
            'topic' => 'Ramazanda İnternetten Para Kazanma: Başlangıç Rehberi',
            'instructions' => 'İnternetten para kazanmaya Ramazan ayında başlamak isteyenler için kapsamlı başlangıç rehberi. Gerekli ekipmanlar, internet bağlantısı, temel dijital beceriler, güvenilir platformlar listesi. Dolandırıcılıktan korunma ipuçları, gerçekçi kazanç beklentileri. Sıfırdan başlayarak ilk 30 günde neler yapılabileceğini anlat.',
        ],
        [
            'slug_suffix' => 'ramazanda-e-ticaret-firsatlari',
            'topic' => 'Ramazan Döneminde E-Ticaret Fırsatları ve Para Kazanma',
            'instructions' => 'Ramazan döneminde artan e-ticaret talebinden yararlanarak para kazanma yollarını anlat. Bayram hediyeleri, iftar setleri, Ramazan süsleri, hurma çeşitleri gibi dönemsel ürünlerin online satışı. Shopify, WooCommerce, Etsy gibi platformlarda mağaza açma rehberi. Ramazan SEO stratejileri ve kampanya planlama.',
        ],
        [
            'slug_suffix' => 'ramazanda-evde-uretim-yaparak-kazanc',
            'topic' => 'Ramazanda Evde Üretim Yaparak Para Kazanma Fikirleri',
            'instructions' => 'Ramazan ayında evde üretim yaparak para kazanmanın yollarını anlat. El yapımı sabun, mum, takı, örgü ürünler, dekoratif objeler, Ramazan süsleri, hediyelik paketler. Instagram ve Etsy üzerinden satış stratejileri, fiyatlandırma, paketleme ve kargo ipuçları. Düşük sermaye ile başlama önerileri.',
        ],
        [
            'slug_suffix' => 'ramazanda-online-egitim-vererek-kazanma',
            'topic' => 'Ramazanda Online Eğitim Vererek Para Kazanma Rehberi',
            'instructions' => 'Ramazan döneminde online eğitim vererek gelir elde etme yollarını anlat. Udemy, Skillshare gibi platformlarda kurs hazırlama, özel ders verme, Zoom ile grup eğitimleri, Kuran-ı Kerim ve dini eğitim kursları. Ramazan ayında artan eğitim talebinden yararlanma stratejileri. Platform karşılaştırması ve kazanç hesaplaması.',
        ],
        [
            'slug_suffix' => 'ramazanda-blog-yazarak-para-kazanma',
            'topic' => 'Ramazanda Blog Yazarak Para Kazanmanın Yolları',
            'instructions' => 'Ramazan ayında blog yazarlığı ile para kazanma yollarını detaylı anlat. Niche blog seçimi, WordPress kurulumu, SEO temelleleri, AdSense ve affiliate gelir modelleri. Ramazan temalı blog içerik fikirleri: tarifler, dua rehberleri, iftar menüleri, hediye önerileri. İlk geliri elde etme sürecini realistik şekilde anlat.',
        ],
        [
            'slug_suffix' => 'ramazanda-youtube-ile-para-kazanma',
            'topic' => 'Ramazanda YouTube Kanalı Açarak Para Kazanma',
            'instructions' => 'Ramazan döneminde YouTube kanalı açarak para kazanmanın adımlarını anlat. Kanal açma, ekipman gereksinimleri, Ramazan özel video fikirleri (iftar tarifleri, vlog, bilgilendirme videoları), monetizasyon koşulları, sponsor bulma. Shorts ile hızlı büyüme stratejileri ve içerik planlaması. Ramazan ayındaki yüksek reklam gelirlerini vurgula.',
        ],
        [
            'slug_suffix' => 'ramazanda-affiliate-marketing-rehberi',
            'topic' => 'Ramazanda Affiliate Marketing ile Kazanç Rehberi',
            'instructions' => 'Ramazan döneminde affiliate (satış ortaklığı) pazarlama ile para kazanma rehberi. Türkiye\'de popüler affiliate programları, Ramazan kampanya dönemindeki yüksek komisyon oranları, doğru ürün seçimi stratejileri. Blog, sosyal medya ve email marketing ile affiliate satış yapma yöntemleri. Gerçekçi kazanç beklentileri ve başarı hikayeleri.',
        ],
        [
            'slug_suffix' => 'ramazanda-uzaktan-calisma-ipuclari',
            'topic' => 'Ramazanda Uzaktan Çalışma İpuçları ve İş Bulma Rehberi',
            'instructions' => 'Ramazan ayında uzaktan çalışma imkanlarını ve iş bulma yollarını anlat. Remote iş veren Türk ve yabancı şirketler, uzaktan çalışma platformları (RemoteOK, WeWorkRemotely, Kariyer.net), oruçluyken verimli çalışma ipuçları. Sahur sonrası ve iftar öncesi ideal çalışma programı önerisi. CV hazırlama ve mülakat ipuçları.',
        ],
        [
            'slug_suffix' => 'ramazanda-anket-doldurarak-para-kazanma',
            'topic' => 'Ramazanda Anket Doldurarak ve Mikro İşlerle Para Kazanma',
            'instructions' => 'Ramazan ayında boş zamanları değerlendirerek anket doldurma ve mikro iş yapma yoluyla para kazanma. Güvenilir anket siteleri (Toluna, Swagbucks, Google Opinion Rewards), mikro iş platformları (Amazon Mechanical Turk, Clickworker). Gerçekçi kazanç bilgileri, dolandırıcılıktan korunma uyarıları. İftar beklerken veya sahurdan sonra yapılabilecek kolay işler.',
        ],
        [
            'slug_suffix' => 'ramazanda-el-isi-satarak-kazanc',
            'topic' => 'Ramazanda El İşi Ürünler Satarak Para Kazanma',
            'instructions' => 'Ramazan döneminde el işi ürünler yaparak ve satarak para kazanma rehberi. Ramazan temalı el işi ürün fikirleri: Ramazan süsleri, tesbihler, seccade kılıfları, iftar masa örtüleri, bayram hediyeleri. Instagram, Etsy, jiji.com.tr üzerinden satış stratejileri. Malzeme tedariki, fiyatlandırma ve müşteri bulma ipuçları.',
        ],
        [
            'slug_suffix' => 'ramazanda-grafik-tasarim-ile-kazanc',
            'topic' => 'Ramazanda Grafik Tasarım ve Dijital Sanat ile Para Kazanma',
            'instructions' => 'Ramazan ayında grafik tasarım becerileriyle para kazanma yollarını anlat. Canva, Adobe ile Ramazan temalı tasarımlar, sosyal medya görselleri, davetiye tasarımı, logo yapma. Freelance tasarım platformları, Fiverr\'da gig açma, Instagram üzerinden tasarım hizmeti sunma. Dijital ürün olarak şablon ve mockup satışı.',
        ],
        [
            'slug_suffix' => 'ramazanda-yemek-siparisi-ile-kazanc',
            'topic' => 'Ramazanda Evden Yemek Yaparak ve Sipariş Alarak Para Kazanma',
            'instructions' => 'Ramazan ayında evden yemek yaparak para kazanma rehberi. İftar menüleri hazırlama, ev yemekleri sipariş alma, tatlı ve börek satışı. Yemeksepeti, Getir, Instagram üzerinden sipariş alma yöntemleri. Gıda güvenliği kuralları, izinler, fiyatlandırma stratejileri. Popüler iftar yemekleri ve ramazan tatlıları üretim fikirleri.',
        ],
        [
            'slug_suffix' => 'ramazanda-cevirmenlik-ile-kazanc',
            'topic' => 'Ramazanda Çeviri ve Tercümanlık ile Evden Para Kazanma',
            'instructions' => 'Ramazan döneminde çeviri ve tercümanlık yaparak evden para kazanma rehberi. İngilizce, Arapça, Almanca çeviri fırsatları, çeviri platformları (ProZ, TranslatorsCafe), belge çevirisi, altyazı çevirisi, yerelleştirme hizmetleri. Ramazan döneminde artan Arapça içerik talebi fırsatları. Fiyatlandırma ve müşteri bulma stratejileri.',
        ],
    ];

    public function __construct(
        private TenantManager $tenantManager,
        private AIContentService $aiService,
        private ImageGenerationService $imageService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = (int) $this->option('count');
        $dryRun = (bool) $this->option('dry-run');
        $noImage = (bool) $this->option('no-image');
        $siteFilter = $this->option('site');

        $this->info('🌙 Starting Ramadan content generation...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - no content will be created.');
        }

        // Get active sites (skip redirected domains)
        $query = Site::where('is_active', true)->whereNull('fallback_domain');
        if ($siteFilter) {
            $query->where('domain', $siteFilter);
        }
        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->error('No active sites found.');
            return 1;
        }

        $this->info("Found {$sites->count()} active site(s). Generating {$count} post(s) per site.");
        $totalCreated = 0;
        $totalErrors = 0;

        // Shuffle topics once and assign different topics to each site
        $allTopics = self::TOPIC_POOL;
        shuffle($allTopics);
        $topicIndex = 0;

        foreach ($sites as $siteIdx => $site) {
            $this->newLine();
            $this->info("━━━ [{$siteIdx}/{$sites->count()}] {$site->name} ({$site->domain}) ━━━");

            try {
                $this->tenantManager->setTenant($site);

                $existingSlugs = Post::pluck('slug')->toArray();
                $this->line("  Existing posts: " . count($existingSlugs));

                // Pick topics for this site (rotate through the pool)
                $siteTopics = [];
                for ($i = 0; $i < $count; $i++) {
                    $topic = $allTopics[$topicIndex % count($allTopics)];
                    $slug = Str::slug($site->name) . '-' . $topic['slug_suffix'];

                    // Skip if already exists
                    if (in_array($slug, $existingSlugs)) {
                        $this->line("  Skipping (exists): {$slug}");
                        $topicIndex++;
                        $i--; // Try next topic
                        // Safety: don't loop forever
                        if ($topicIndex > count($allTopics) * 2) {
                            $this->warn("  All topics exhausted for {$site->name}");
                            break;
                        }
                        continue;
                    }

                    $siteTopics[] = array_merge($topic, ['full_slug' => $slug]);
                    $topicIndex++;
                }

                if (empty($siteTopics)) {
                    $this->warn("  No available Ramadan topics for {$site->name}");
                    continue;
                }

                foreach ($siteTopics as $topic) {
                    $this->line("  📝 Generating: {$topic['topic']}");

                    if ($dryRun) {
                        $this->line("    [DRY RUN] Would create: {$topic['full_slug']}");
                        continue;
                    }

                    try {
                        $siteInfo = [
                            'country' => 'Türkiye',
                            'social_links' => $site->social_links ?? [],
                        ];

                        // Use the daily post generator with custom Ramadan topic
                        $generated = $this->aiService->generateDailyPost(
                            $site->name,
                            $site->domain,
                            $topic['topic'],
                            $topic['instructions'],
                            $existingSlugs,
                            $siteInfo
                        );

                        // Generate featured image
                        $featuredImage = null;
                        if (!$noImage) {
                            $imagePrompt = $generated['featured_image_prompt'] ?? null;
                            $this->line("    🎨 Generating featured image...");
                            $featuredImage = $this->imageService->generateFeaturedImage(
                                $topic['topic'],
                                $site->name,
                                $imagePrompt
                            );
                        }

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

                        $existingSlugs[] = $topic['full_slug'];
                        $totalCreated++;
                        $imageStatus = $featuredImage ? '✅ with image' : '⚠️ no image';
                        $this->info("    ✅ Created: {$generated['title']} ({$imageStatus})");

                        Log::info("RamadanContent: Created post", [
                            'site' => $site->domain,
                            'slug' => $topic['full_slug'],
                            'has_image' => !empty($featuredImage),
                        ]);
                    } catch (\Throwable $e) {
                        $totalErrors++;
                        $this->error("    ❌ Error: {$e->getMessage()}");
                        Log::error("RamadanContent: Failed to generate post", [
                            'site' => $site->domain,
                            'topic' => $topic['topic'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                $totalErrors++;
                $this->error("  ❌ Site error: {$e->getMessage()}");
                Log::error("RamadanContent: Site processing failed", [
                    'site' => $site->domain,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("🌙 Done! Created: {$totalCreated} posts, Errors: {$totalErrors}");

        Log::info("RamadanContent: Completed", [
            'created' => $totalCreated,
            'errors' => $totalErrors,
        ]);

        return $totalErrors > 0 && $totalCreated === 0 ? 1 : 0;
    }
}
