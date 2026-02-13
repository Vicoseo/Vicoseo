<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Site;
use App\Models\TopOffer;
use App\Services\TenantManager;
use Illuminate\Database\Seeder;

class ExampleSitesSeeder extends Seeder
{
    private TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function run(): void
    {
        $sites = $this->getSiteDefinitions();

        foreach ($sites as $siteData) {
            $pages = $siteData['pages'];
            $posts = $siteData['posts'];
            $offers = $siteData['offers'];
            $redirects = $siteData['redirects'];
            unset($siteData['pages'], $siteData['posts'], $siteData['offers'], $siteData['redirects']);

            // Create site in landlord DB
            $site = Site::create($siteData);
            $this->command->info("Created site: {$site->name} ({$site->domain})");

            // Provision tenant database
            $this->tenantManager->createTenantDatabase($site);
            $this->command->info("  -> Tenant DB provisioned: {$site->db_name}");

            // Switch to tenant
            $this->tenantManager->setTenant($site);

            // Seed pages
            foreach ($pages as $page) {
                Page::create($page);
            }
            $this->command->info("  -> " . count($pages) . " pages created");

            // Seed posts
            foreach ($posts as $post) {
                Post::create($post);
            }
            $this->command->info("  -> " . count($posts) . " posts created");

            // Seed top offers
            foreach ($offers as $offer) {
                TopOffer::create($offer);
            }
            $this->command->info("  -> " . count($offers) . " site offers created");

            // Seed redirects
            foreach ($redirects as $redirect) {
                Redirect::create($redirect);
            }
            $this->command->info("  -> " . count($redirects) . " redirects created");
        }

        $this->command->info("\nAll example sites created successfully!");
    }

    private function getSiteDefinitions(): array
    {
        return [
            // ─── Site 1: BahisKrali ───
            [
                'domain' => 'bahiskrali.test',
                'name' => 'Bahis Kralı',
                'logo_url' => null,
                'favicon_url' => null,
                'primary_color' => '#E53935',
                'secondary_color' => '#FF8F00',
                'db_name' => 'tenant_bahiskrali',
                'is_active' => true,
                'meta_title' => 'Bahis Kralı - Güncel Bahis Siteleri ve Bonuslar',
                'meta_description' => 'En güvenilir bahis siteleri, güncel bonus kampanyaları ve canlı bahis tavsiyeleri. Bahis dünyasının kralı burada!',
                'entry_url' => 'https://example.com/go/bahiskrali',
                'login_url' => 'https://example.com/login/bahiskrali',
                'show_sponsors' => true,
                'pages' => $this->bahisKraliPages(),
                'posts' => $this->bahisKraliPosts(),
                'offers' => $this->bahisKraliOffers(),
                'redirects' => $this->bahisKraliRedirects(),
            ],

            // ─── Site 2: CasinoMerkezi ───
            [
                'domain' => 'casinomerkezi.test',
                'name' => 'Casino Merkezi',
                'logo_url' => null,
                'favicon_url' => null,
                'primary_color' => '#1B5E20',
                'secondary_color' => '#FFD600',
                'db_name' => 'tenant_casinomerkezi',
                'is_active' => true,
                'meta_title' => 'Casino Merkezi - Online Casino Rehberi ve İncelemeleri',
                'meta_description' => 'En iyi online casino siteleri, slot oyunları incelemeleri ve casino bonusları hakkında detaylı bilgiler.',
                'entry_url' => 'https://example.com/go/casinomerkezi',
                'login_url' => 'https://example.com/login/casinomerkezi',
                'show_sponsors' => true,
                'pages' => $this->casinoMerkeziPages(),
                'posts' => $this->casinoMerkeziPosts(),
                'offers' => $this->casinoMerkeziOffers(),
                'redirects' => $this->casinoMerkeziRedirects(),
            ],

            // ─── Site 3: BonusAvi ───
            [
                'domain' => 'bonusavi.test',
                'name' => 'Bonus Avı',
                'logo_url' => null,
                'favicon_url' => null,
                'primary_color' => '#6A1B9A',
                'secondary_color' => '#00E5FF',
                'db_name' => 'tenant_bonusavi',
                'is_active' => true,
                'meta_title' => 'Bonus Avı - En İyi Bahis Bonusları ve Promosyonlar',
                'meta_description' => 'Tüm bahis ve casino sitelerinin güncel bonusları, promosyon kodları ve deneme bonusları tek adreste.',
                'entry_url' => 'https://example.com/go/bonusavi',
                'login_url' => 'https://example.com/login/bonusavi',
                'show_sponsors' => true,
                'pages' => $this->bonusAviPages(),
                'posts' => $this->bonusAviPosts(),
                'offers' => $this->bonusAviOffers(),
                'redirects' => $this->bonusAviRedirects(),
            ],

            // ─── Site 4: IddiaMaster ───
            [
                'domain' => 'iddiamaster.test',
                'name' => 'İddia Master',
                'logo_url' => null,
                'favicon_url' => null,
                'primary_color' => '#0D47A1',
                'secondary_color' => '#FF6D00',
                'db_name' => 'tenant_iddiamaster',
                'is_active' => true,
                'meta_title' => 'İddia Master - Profesyonel Bahis Tahminleri ve Analizler',
                'meta_description' => 'Uzman bahis analizleri, maç tahminleri, istatistikler ve en güvenilir bahis siteleri incelemeleri.',
                'entry_url' => 'https://example.com/go/iddiamaster',
                'login_url' => 'https://example.com/login/iddiamaster',
                'show_sponsors' => true,
                'pages' => $this->iddiaMasterPages(),
                'posts' => $this->iddiaMasterPosts(),
                'offers' => $this->iddiaMasterOffers(),
                'redirects' => $this->iddiaMasterRedirects(),
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Site 1: Bahis Kralı
    // ─────────────────────────────────────────────────────────────────────

    private function bahisKraliPages(): array
    {
        return [
            [
                'slug' => 'ana-sayfa',
                'title' => 'Ana Sayfa',
                'content' => '<h1>Bahis Kralı\'na Hoş Geldiniz</h1>
<p>Türkiye\'nin en kapsamlı bahis rehberine hoş geldiniz. Bahis Kralı olarak, size en güvenilir bahis sitelerini, güncel bonus kampanyalarını ve canlı bahis tavsiyelerini sunuyoruz.</p>
<h2>Neden Bahis Kralı?</h2>
<ul>
<li><strong>Güvenilir İncelemeler:</strong> Her siteyi detaylıca inceliyor, gerçek kullanıcı deneyimlerini paylaşıyoruz.</li>
<li><strong>Güncel Bonuslar:</strong> En son bonus kampanyalarını anında sizlere ulaştırıyoruz.</li>
<li><strong>Profesyonel Analizler:</strong> Uzman kadromuz ile bahis dünyasındaki gelişmeleri takip ediyoruz.</li>
</ul>
<p>Hemen üye olun ve kazanmaya başlayın!</p>',
                'meta_title' => 'Bahis Kralı - En İyi Bahis Siteleri Rehberi',
                'meta_description' => 'Güvenilir bahis siteleri, güncel bonuslar ve canlı bahis tavsiyeleri.',
                'is_published' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'hakkimizda',
                'title' => 'Hakkımızda',
                'content' => '<h1>Bahis Kralı Hakkında</h1>
<p>Bahis Kralı, 2024 yılından beri bahis severler için güvenilir bir rehber olmayı amaçlamaktadır. Deneyimli editör kadromuz, bahis dünyasındaki tüm gelişmeleri yakından takip eder.</p>
<h2>Misyonumuz</h2>
<p>Kullanıcılarımıza en doğru ve güncel bilgiyi sunarak, güvenli bir bahis deneyimi yaşamalarını sağlamak.</p>
<h2>Ekibimiz</h2>
<p>Alanında uzman yazarlar, bahis analistleri ve teknoloji geliştiricilerinden oluşan ekibimiz, sizin için en iyi içerikleri hazırlamaktadır.</p>',
                'meta_title' => 'Hakkımızda - Bahis Kralı',
                'meta_description' => 'Bahis Kralı ekibi ve misyonumuz hakkında bilgi edinin.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'content' => '<h1>Bize Ulaşın</h1>
<p>Sorularınız, önerileriniz veya şikayetleriniz için bize her zaman ulaşabilirsiniz.</p>
<h2>İletişim Bilgileri</h2>
<ul>
<li><strong>E-posta:</strong> info@bahiskrali.test</li>
<li><strong>Telegram:</strong> @bahiskrali</li>
</ul>
<p>Mesajlarınıza en kısa sürede dönüş yapılacaktır.</p>',
                'meta_title' => 'İletişim - Bahis Kralı',
                'meta_description' => 'Bahis Kralı iletişim bilgileri.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => 'Gizlilik Politikası',
                'content' => '<h1>Gizlilik Politikası</h1>
<p>Bahis Kralı olarak kullanıcılarımızın gizliliğine büyük önem veriyoruz. Bu politika, kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklamaktadır.</p>
<h2>Toplanan Veriler</h2>
<p>Sitemizi ziyaret ettiğinizde, IP adresi, tarayıcı bilgileri ve ziyaret edilen sayfalar gibi standart web bilgileri otomatik olarak toplanabilir.</p>
<h2>Çerezler</h2>
<p>Sitemiz, kullanıcı deneyimini iyileştirmek amacıyla çerezler kullanmaktadır. Tarayıcı ayarlarınızdan çerez tercihlerinizi yönetebilirsiniz.</p>
<h2>Üçüncü Taraf Bağlantıları</h2>
<p>Sitemiz üzerinden yönlendirilen bahis sitelerinin gizlilik politikaları kendi sorumlulukları altındadır.</p>',
                'meta_title' => 'Gizlilik Politikası - Bahis Kralı',
                'meta_description' => 'Bahis Kralı gizlilik politikası ve kişisel verilerin korunması.',
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'sorumluluk-reddi',
                'title' => 'Sorumluluk Reddi',
                'content' => '<h1>Sorumluluk Reddi</h1>
<p>Bu sitede yer alan tüm içerikler yalnızca bilgilendirme amacı taşımaktadır. Bahis Kralı, herhangi bir bahis sitesini doğrudan teşvik etmez. Bahis oynarken yasal düzenlemelere uymanız ve sorumlu bir şekilde oynamanız önemlidir.</p>
<p>18 yaşından küçüklerin bahis oynaması yasaktır. Lütfen sorumlu bahis oynayın.</p>',
                'meta_title' => 'Sorumluluk Reddi - Bahis Kralı',
                'meta_description' => 'Bahis Kralı yasal uyarı ve sorumluluk reddi.',
                'is_published' => true,
                'sort_order' => 4,
            ],
        ];
    }

    private function bahisKraliPosts(): array
    {
        return [
            [
                'slug' => '2026-en-iyi-bahis-siteleri',
                'title' => '2026 Yılının En İyi Bahis Siteleri',
                'excerpt' => '2026 yılında öne çıkan güvenilir bahis sitelerini sizler için derledik. Lisans bilgileri, ödeme yöntemleri ve bonus detayları.',
                'content' => '<h1>2026 Yılının En İyi Bahis Siteleri</h1>
<p>Bahis dünyası her geçen gün büyümeye devam ediyor. 2026 yılında da birçok yeni site piyasaya girerken, güvenilir olanlarını ayırt etmek giderek daha önemli hale geliyor.</p>
<h2>Değerlendirme Kriterleri</h2>
<p>Bahis sitelerini değerlendirirken şu kriterleri göz önünde bulunduruyoruz:</p>
<ul>
<li>Lisans ve yasal durum</li>
<li>Ödeme hızı ve yöntem çeşitliliği</li>
<li>Bonus kampanyaları ve çevrim şartları</li>
<li>Müşteri hizmetleri kalitesi</li>
<li>Mobil uyumluluk</li>
</ul>
<h2>En İyi 5 Bahis Sitesi</h2>
<p>Detaylı incelememize göre 2026\'nın en güvenilir bahis siteleri sıralamaya alınmıştır. Her sitenin artı ve eksi yönlerini ayrıntılı olarak ele aldık.</p>
<p>Güvenli bahisler!</p>',
                'meta_title' => '2026 En İyi Bahis Siteleri - Bahis Kralı',
                'meta_description' => '2026 yılının en güvenilir bahis siteleri listesi ve detaylı incelemeleri.',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'slug' => 'canli-bahis-taktikleri',
                'title' => 'Canlı Bahis Taktikleri: Kazanma Şansınızı Artırın',
                'excerpt' => 'Canlı bahiste başarılı olmanın yolları ve profesyonel taktikler.',
                'content' => '<h1>Canlı Bahis Taktikleri</h1>
<p>Canlı bahis, maç devam ederken bahis yapabilme imkanı sunan heyecan verici bir formattır. Ancak başarılı olabilmek için doğru stratejilere sahip olmanız gerekir.</p>
<h2>Temel Taktikler</h2>
<h3>1. Maç Analizi</h3>
<p>Bahis yapmadan önce takımların form durumunu, sakatlık haberlerini ve karşılaşma istatistiklerini detaylıca inceleyin.</p>
<h3>2. Bankroll Yönetimi</h3>
<p>Bütçenizi doğru yönetmek, uzun vadede başarının anahtarıdır. Toplam bütçenizin %5\'inden fazlasını tek bir bahise yatırmayın.</p>
<h3>3. Duygusal Kararlardan Kaçının</h3>
<p>Favori takımınıza bahis yaparken objektif olmaya çalışın. Duygusal kararlar genellikle kayıpla sonuçlanır.</p>
<h3>4. Canlı İstatistikleri Takip Edin</h3>
<p>Maç sırasında top hakimiyeti, şut sayıları ve tehlikeli atak oranlarını takip edin.</p>',
                'meta_title' => 'Canlı Bahis Taktikleri - Bahis Kralı',
                'meta_description' => 'Profesyonel canlı bahis taktikleri ve kazanma stratejileri.',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'slug' => 'deneme-bonusu-veren-siteler',
                'title' => 'Deneme Bonusu Veren Siteler 2026',
                'excerpt' => 'Yatırım yapmadan deneme bonusu alabileceğiniz güvenilir bahis siteleri.',
                'content' => '<h1>Deneme Bonusu Veren Siteler 2026</h1>
<p>Deneme bonusu, yeni üyelere herhangi bir yatırım yapmadan verilen bedava bahis kredisidir. Bu bonuslar sayesinde siteyi risk almadan deneyebilirsiniz.</p>
<h2>Deneme Bonusu Nedir?</h2>
<p>Deneme bonusu, bahis sitelerinin yeni üyelerine sunduğu ücretsiz bir başlangıç kredisidir. Genellikle 50 TL ile 500 TL arasında değişen tutarlarda verilir.</p>
<h2>Dikkat Edilmesi Gerekenler</h2>
<ul>
<li>Çevrim şartlarını mutlaka okuyun</li>
<li>Minimum çekim tutarını kontrol edin</li>
<li>Süre kısıtlamalarına dikkat edin</li>
<li>Geçerli oyun türlerini öğrenin</li>
</ul>',
                'meta_title' => 'Deneme Bonusu Veren Siteler 2026 - Bahis Kralı',
                'meta_description' => '2026 yılında deneme bonusu veren güvenilir bahis siteleri listesi.',
                'is_published' => true,
                'published_at' => now()->subDays(8),
            ],
            [
                'slug' => 'bahis-siteleri-para-yatirma-cekme',
                'title' => 'Bahis Sitelerinde Para Yatırma ve Çekme Yöntemleri',
                'excerpt' => 'Bahis sitelerinde kullanabileceğiniz güvenli ödeme yöntemleri rehberi.',
                'content' => '<h1>Para Yatırma ve Çekme Rehberi</h1>
<p>Bahis sitelerinde para yatırma ve çekme işlemleri, kullanıcıların en çok merak ettiği konuların başında gelmektedir.</p>
<h2>Popüler Ödeme Yöntemleri</h2>
<h3>Banka Havalesi</h3>
<p>En yaygın yöntemlerden biridir. İşlem süresi genellikle 15-30 dakika arasındadır.</p>
<h3>Kripto Para</h3>
<p>Bitcoin, USDT ve diğer kripto paralar ile hızlı ve anonim işlem yapabilirsiniz.</p>
<h3>E-Cüzdanlar</h3>
<p>Papara, Payfix gibi e-cüzdan uygulamaları ile anında para yatırma imkanı.</p>
<h2>Güvenlik İpuçları</h2>
<p>Para çekme işlemlerinde kimlik doğrulaması yapmanız gerekebilir. Bu süreç güvenliğiniz için önemlidir.</p>',
                'meta_title' => 'Para Yatırma ve Çekme Yöntemleri - Bahis Kralı',
                'meta_description' => 'Bahis sitelerinde güvenli para yatırma ve çekme yöntemleri rehberi.',
                'is_published' => true,
                'published_at' => now()->subDays(12),
            ],
            [
                'slug' => 'futbol-bahis-stratejileri',
                'title' => 'Futbol Bahis Stratejileri: Detaylı Rehber',
                'excerpt' => 'Futbol bahislerinde uzun vadede kazanmanızı sağlayacak stratejiler.',
                'content' => '<h1>Futbol Bahis Stratejileri</h1>
<p>Futbol, dünya genelinde en çok bahis oynanan spor dalıdır. Doğru stratejilerle kazanma şansınızı önemli ölçüde artırabilirsiniz.</p>
<h2>Alt/Üst Bahisleri</h2>
<p>Maçta atılacak toplam gol sayısını tahmin etmeye dayanan bu bahis türü, istatistiksel analiz ile yüksek başarı oranı sunar.</p>
<h2>Handikap Bahisleri</h2>
<p>Güçlü ve zayıf takımlar arasındaki farkı dengelemek için kullanılan handikap bahisleri, daha yüksek oranlar sunar.</p>
<h2>İlk Yarı / Maç Sonucu</h2>
<p>Çift şans kombinasyonları ile risk/getiri dengesini optimize edebilirsiniz.</p>',
                'meta_title' => 'Futbol Bahis Stratejileri - Bahis Kralı',
                'meta_description' => 'Profesyonel futbol bahis stratejileri ve analiz yöntemleri.',
                'is_published' => true,
                'published_at' => now()->subDays(15),
            ],
        ];
    }

    private function bahisKraliOffers(): array
    {
        return [
            [
                'slug' => 'betpremium',
                'logo_url' => 'https://placehold.co/120x60/E53935/white?text=BetPremium',
                'bonus_text' => '%150 Hoş Geldin Bonusu',
                'cta_text' => 'HEMEN KATIL',
                'target_url' => 'https://example.com/go/betpremium',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'kralbet',
                'logo_url' => 'https://placehold.co/120x60/FF8F00/white?text=KralBet',
                'bonus_text' => '500 TL Deneme Bonusu',
                'cta_text' => 'ÜYE OL',
                'target_url' => 'https://example.com/go/kralbet',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];
    }

    private function bahisKraliRedirects(): array
    {
        return [
            ['slug' => 'login', 'target_url' => 'https://example.com/login/bahiskrali', 'is_active' => true],
            ['slug' => 'betpremium', 'target_url' => 'https://example.com/go/betpremium', 'is_active' => true],
            ['slug' => 'kralbet', 'target_url' => 'https://example.com/go/kralbet', 'is_active' => true],
            ['slug' => 'telegram', 'target_url' => 'https://t.me/bahiskrali', 'is_active' => true],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Site 2: Casino Merkezi
    // ─────────────────────────────────────────────────────────────────────

    private function casinoMerkeziPages(): array
    {
        return [
            [
                'slug' => 'ana-sayfa',
                'title' => 'Ana Sayfa',
                'content' => '<h1>Casino Merkezi\'ne Hoş Geldiniz</h1>
<p>Online casino dünyasının en kapsamlı rehberine hoş geldiniz! Casino Merkezi olarak, en güvenilir casino sitelerini inceliyor, slot oyunlarını değerlendiriyor ve size en iyi bonusları sunuyoruz.</p>
<h2>Neden Casino Merkezi?</h2>
<ul>
<li><strong>Detaylı İncelemeler:</strong> Her casino sitesini tüm yönleriyle ele alıyoruz.</li>
<li><strong>Oyun Rehberleri:</strong> Slot, rulet, blackjack ve daha fazlası için stratejik rehberler.</li>
<li><strong>Özel Bonuslar:</strong> Casino Merkezi kullanıcılarına özel bonus kodları.</li>
</ul>',
                'meta_title' => 'Casino Merkezi - Online Casino Rehberi',
                'meta_description' => 'En güvenilir online casino siteleri, slot incelemeleri ve casino bonusları.',
                'is_published' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'hakkimizda',
                'title' => 'Hakkımızda',
                'content' => '<h1>Casino Merkezi Hakkında</h1>
<p>Casino Merkezi, online casino tutkunları için hazırlanmış bağımsız bir inceleme platformudur. Amacımız, oyuncuların bilinçli kararlar vermelerini sağlamaktır.</p>
<h2>Değerlendirme Sürecimiz</h2>
<p>Her casino sitesini lisans, oyun çeşitliliği, ödeme hızı, müşteri hizmetleri ve güvenlik açısından değerlendiriyoruz.</p>',
                'meta_title' => 'Hakkımızda - Casino Merkezi',
                'meta_description' => 'Casino Merkezi hakkında bilgi ve değerlendirme sürecimiz.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'content' => '<h1>İletişim</h1>
<p>Casino Merkezi ekibine aşağıdaki kanallardan ulaşabilirsiniz.</p>
<ul>
<li><strong>E-posta:</strong> info@casinomerkezi.test</li>
<li><strong>Telegram:</strong> @casinomerkezi</li>
</ul>',
                'meta_title' => 'İletişim - Casino Merkezi',
                'meta_description' => 'Casino Merkezi iletişim bilgileri.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => 'Gizlilik Politikası',
                'content' => '<h1>Gizlilik Politikası</h1>
<p>Casino Merkezi olarak kişisel verilerinizin korunmasına önem veriyoruz. Bu politika, verilerinizin nasıl işlendiğini açıklamaktadır.</p>
<p>Sitemizde toplanan veriler yalnızca hizmet kalitesini artırmak amacıyla kullanılır ve üçüncü taraflarla paylaşılmaz.</p>',
                'meta_title' => 'Gizlilik Politikası - Casino Merkezi',
                'meta_description' => 'Casino Merkezi gizlilik politikası.',
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];
    }

    private function casinoMerkeziPosts(): array
    {
        return [
            [
                'slug' => 'en-populer-slot-oyunlari-2026',
                'title' => 'En Popüler Slot Oyunları 2026',
                'excerpt' => '2026 yılında en çok oynanan slot oyunları ve RTP oranları.',
                'content' => '<h1>En Popüler Slot Oyunları 2026</h1>
<p>Slot oyunları, online casinoların en popüler kategorisi olmaya devam ediyor. 2026 yılında öne çıkan slot oyunlarını inceledik.</p>
<h2>Sweet Bonanza</h2>
<p>Pragmatic Play\'in efsanevi slot oyunu Sweet Bonanza, %96.51 RTP oranı ile oyuncuların favorisi olmaya devam ediyor. Scatter sembollerinin tetiklediği free spin özelliği ile büyük kazançlar mümkün.</p>
<h2>Gates of Olympus</h2>
<p>Zeus temalı bu slot oyunu, çarpan özellikleri ile büyük kazanç potansiyeli sunuyor. Maksimum 5000x çarpan ile heyecan dorukta.</p>
<h2>Book of Dead</h2>
<p>Play\'n GO\'nun klasik Mısır temalı slot oyunu, free spin turunda genişleyen semboller ile dikkat çekiyor.</p>',
                'meta_title' => 'En Popüler Slot Oyunları 2026 - Casino Merkezi',
                'meta_description' => '2026 yılının en çok oynanan slot oyunları listesi ve detaylı incelemeleri.',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'slug' => 'blackjack-strateji-rehberi',
                'title' => 'Blackjack Strateji Rehberi: Temel ve İleri Seviye',
                'excerpt' => 'Blackjack masasında kazanma şansınızı artıracak stratejiler.',
                'content' => '<h1>Blackjack Strateji Rehberi</h1>
<p>Blackjack, casino oyunları arasında en düşük kasa avantajına sahip oyunlardan biridir. Doğru strateji ile kasa avantajını %0.5\'in altına düşürebilirsiniz.</p>
<h2>Temel Strateji</h2>
<p>Temel blackjack stratejisi, elinizde ve krupiyenin açık kartına göre matematiksel olarak en doğru kararı almanızı sağlar.</p>
<h3>Ne Zaman Hit?</h3>
<p>Elinizin toplamı 11 veya altındaysa her zaman hit almalısınız. 12-16 arası ise krupiyenin kartına bağlıdır.</p>
<h3>Ne Zaman Stand?</h3>
<p>17 ve üzeri toplama sahipseniz her zaman stand yapın. Krupiyenin zayıf kartı varsa (2-6) 12+ ile stand yapabilirsiniz.</p>',
                'meta_title' => 'Blackjack Strateji Rehberi - Casino Merkezi',
                'meta_description' => 'Blackjack oyununda temel ve ileri seviye kazanma stratejileri.',
                'is_published' => true,
                'published_at' => now()->subDays(4),
            ],
            [
                'slug' => 'canli-casino-nedir',
                'title' => 'Canlı Casino Nedir? Nasıl Oynanır?',
                'excerpt' => 'Canlı casino deneyimi hakkında bilmeniz gereken her şey.',
                'content' => '<h1>Canlı Casino Rehberi</h1>
<p>Canlı casino, gerçek krupiyeler eşliğinde, stüdyo ortamında oynanan online casino oyunlarıdır. HD video akışı ile gerçek casino atmosferini evinize taşır.</p>
<h2>Canlı Casino Oyunları</h2>
<ul>
<li><strong>Canlı Rulet:</strong> Lightning Roulette, Immersive Roulette</li>
<li><strong>Canlı Blackjack:</strong> VIP masalar dahil çeşitli seçenekler</li>
<li><strong>Canlı Baccarat:</strong> Speed Baccarat, Squeeze Baccarat</li>
<li><strong>Game Shows:</strong> Crazy Time, Monopoly Live, Deal or No Deal</li>
</ul>
<h2>En İyi Canlı Casino Sağlayıcıları</h2>
<p>Evolution Gaming ve Pragmatic Play Live, sektörün lider canlı casino sağlayıcılarıdır.</p>',
                'meta_title' => 'Canlı Casino Nedir? - Casino Merkezi',
                'meta_description' => 'Canlı casino oyunları, kuralları ve en iyi sağlayıcılar hakkında detaylı rehber.',
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'slug' => 'casino-bonusu-cevirme-sartlari',
                'title' => 'Casino Bonusu Çevirme Şartları Rehberi',
                'excerpt' => 'Casino bonuslarının çevirme şartlarını anlamak ve en avantajlı bonusları seçmek.',
                'content' => '<h1>Casino Bonusu Çevirme Şartları</h1>
<p>Casino bonusları cazip görünse de çevirme şartlarını anlamak çok önemlidir. Bu rehberde, bonus çevirme şartlarını detaylıca açıklıyoruz.</p>
<h2>Çevirme Şartı Nedir?</h2>
<p>Çevirme şartı (wagering requirement), bonus tutarının çekilebilir hale gelmesi için kaç kez bahis yapılması gerektiğini belirtir. Örneğin, 100 TL bonus + 30x çevirme = 3000 TL bahis yapmanız gerekir.</p>
<h2>İdeal Çevirme Şartı</h2>
<p>25x-35x arası çevirme şartları makul kabul edilir. 40x ve üzeri ise zorlu sayılır.</p>',
                'meta_title' => 'Casino Bonusu Çevirme Şartları - Casino Merkezi',
                'meta_description' => 'Casino bonusu çevirme şartları nasıl hesaplanır? Detaylı rehber.',
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'slug' => 'rulet-taktikleri-ve-sistemleri',
                'title' => 'Rulet Taktikleri ve Bahis Sistemleri',
                'excerpt' => 'Martingale, Fibonacci ve diğer rulet sistemlerinin analizi.',
                'content' => '<h1>Rulet Taktikleri ve Sistemleri</h1>
<p>Rulet, şans oyunlarının kralıdır. Çeşitli bahis sistemleri ile oyun deneyiminizi optimize edebilirsiniz.</p>
<h2>Martingale Sistemi</h2>
<p>Her kayıpta bahsi ikiye katlama prensibiyle çalışır. Kısa vadede etkili olsa da uzun vadede risklidir.</p>
<h2>Fibonacci Sistemi</h2>
<p>Fibonacci dizisine göre bahis miktarını artırma stratejisidir. Martingale\'e göre daha muhafazakar bir yaklaşım sunar.</p>
<h2>D\'Alembert Sistemi</h2>
<p>Kayıpta bir birim artırma, kazançta bir birim azaltma mantığıyla çalışır.</p>
<h2>Avrupa vs Amerikan Rulet</h2>
<p>Avrupa ruleti tek sıfırlı (%2.7 kasa avantajı), Amerikan ruleti çift sıfırlıdır (%5.26). Her zaman Avrupa ruletini tercih edin.</p>',
                'meta_title' => 'Rulet Taktikleri ve Sistemleri - Casino Merkezi',
                'meta_description' => 'Rulet bahis taktikleri, sistemleri ve kazanma stratejileri.',
                'is_published' => true,
                'published_at' => now()->subDays(14),
            ],
        ];
    }

    private function casinoMerkeziOffers(): array
    {
        return [
            [
                'slug' => 'casinomax',
                'logo_url' => 'https://placehold.co/120x60/1B5E20/white?text=CasinoMax',
                'bonus_text' => '%200 Casino Hoş Geldin',
                'cta_text' => 'OYNA',
                'target_url' => 'https://example.com/go/casinomax',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'slotpalace',
                'logo_url' => 'https://placehold.co/120x60/FFD600/333?text=SlotPalace',
                'bonus_text' => '100 Free Spin Hediye',
                'cta_text' => 'ÜYE OL',
                'target_url' => 'https://example.com/go/slotpalace',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'slug' => 'livecasino',
                'logo_url' => 'https://placehold.co/120x60/9C27B0/white?text=LiveCasino',
                'bonus_text' => '%100 Canlı Casino Bonusu',
                'cta_text' => 'KATIL',
                'target_url' => 'https://example.com/go/livecasino',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];
    }

    private function casinoMerkeziRedirects(): array
    {
        return [
            ['slug' => 'login', 'target_url' => 'https://example.com/login/casinomerkezi', 'is_active' => true],
            ['slug' => 'casinomax', 'target_url' => 'https://example.com/go/casinomax', 'is_active' => true],
            ['slug' => 'slotpalace', 'target_url' => 'https://example.com/go/slotpalace', 'is_active' => true],
            ['slug' => 'livecasino', 'target_url' => 'https://example.com/go/livecasino', 'is_active' => true],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Site 3: Bonus Avı
    // ─────────────────────────────────────────────────────────────────────

    private function bonusAviPages(): array
    {
        return [
            [
                'slug' => 'ana-sayfa',
                'title' => 'Ana Sayfa',
                'content' => '<h1>Bonus Avı\'na Hoş Geldiniz!</h1>
<p>En güncel bahis ve casino bonuslarını tek bir çatı altında toplayan Bonus Avı\'na hoş geldiniz. Deneme bonusları, hoş geldin bonusları, yatırım bonusları ve daha fazlasını burada bulabilirsiniz.</p>
<h2>Günün Bonusları</h2>
<p>Her gün güncellenen bonus listesi ile hiçbir fırsatı kaçırmayın. Ekibimiz tüm bahis ve casino sitelerini takip ederek en güncel bonusları sizlere sunuyor.</p>
<h2>Bonus Karşılaştırma</h2>
<p>Farklı sitelerin bonuslarını yan yana karşılaştırın ve size en uygun olanı seçin.</p>',
                'meta_title' => 'Bonus Avı - Güncel Bahis ve Casino Bonusları',
                'meta_description' => 'Deneme bonusu, hoş geldin bonusu ve yatırım bonusu veren güvenilir siteler.',
                'is_published' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'hakkimizda',
                'title' => 'Hakkımızda',
                'content' => '<h1>Bonus Avı Hakkında</h1>
<p>Bonus Avı, bahis ve casino dünyasındaki tüm bonus fırsatlarını bir araya getiren bağımsız bir platformdur. Hedefimiz, kullanıcıların en avantajlı bonusları kolayca bulabilmesidir.</p>
<p>Her bonus fırsatını detaylıca inceliyoruz: çevirme şartları, minimum yatırım tutarları, geçerlilik süreleri ve kullanıcı yorumlarını bir arada sunuyoruz.</p>',
                'meta_title' => 'Hakkımızda - Bonus Avı',
                'meta_description' => 'Bonus Avı platformu hakkında bilgi.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'content' => '<h1>İletişim</h1>
<p>Bonus önerileri, site incelemeleri veya işbirliği teklifleri için bize ulaşın.</p>
<ul>
<li><strong>E-posta:</strong> info@bonusavi.test</li>
<li><strong>Telegram:</strong> @bonusavi</li>
</ul>',
                'meta_title' => 'İletişim - Bonus Avı',
                'meta_description' => 'Bonus Avı iletişim bilgileri.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => 'Gizlilik Politikası',
                'content' => '<h1>Gizlilik Politikası</h1>
<p>Bonus Avı olarak kullanıcılarımızın gizliliğine saygı gösteriyoruz. Toplanan veriler yalnızca hizmet geliştirme amacıyla kullanılır.</p>
<p>Ayrıntılı bilgi için bizimle iletişime geçebilirsiniz.</p>',
                'meta_title' => 'Gizlilik Politikası - Bonus Avı',
                'meta_description' => 'Bonus Avı gizlilik politikası.',
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];
    }

    private function bonusAviPosts(): array
    {
        return [
            [
                'slug' => 'deneme-bonusu-rehberi-2026',
                'title' => 'Deneme Bonusu Rehberi 2026: Bilmeniz Gereken Her Şey',
                'excerpt' => 'Deneme bonusu nedir, nasıl alınır, en güncel deneme bonusları.',
                'content' => '<h1>Deneme Bonusu Rehberi 2026</h1>
<p>Deneme bonusları, bahis sitelerinin yeni üyelerine sunduğu bedava bakiyelerdir. Bu rehberde deneme bonusları hakkında bilmeniz gereken her şeyi bulacaksınız.</p>
<h2>Deneme Bonusu Türleri</h2>
<h3>Yatırımsız Deneme Bonusu</h3>
<p>Herhangi bir yatırım yapmadan, sadece üyelik oluşturarak alınan bonuslardır. Genellikle 25-100 TL arasında olur.</p>
<h3>İlk Yatırım Deneme Bonusu</h3>
<p>İlk yatırımınıza eklenen bonus tutarıdır. %50 ile %200 arasında değişebilir.</p>
<h3>Free Spin Deneme Bonusu</h3>
<p>Slot oyunlarında kullanabileceğiniz ücretsiz çevirme hakları. 10 ile 200 arasında free spin verilebilir.</p>',
                'meta_title' => 'Deneme Bonusu Rehberi 2026 - Bonus Avı',
                'meta_description' => '2026 yılının en güncel deneme bonusu rehberi ve bonus veren siteler.',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'slug' => 'hos-geldin-bonusu-karsilastirma',
                'title' => 'Hoş Geldin Bonusu Karşılaştırma Tablosu',
                'excerpt' => 'En iyi hoş geldin bonusu veren sitelerin karşılaştırmalı analizi.',
                'content' => '<h1>Hoş Geldin Bonusu Karşılaştırma</h1>
<p>Farklı bahis sitelerinin hoş geldin bonuslarını karşılaştırdık. Çevirme şartları, maksimum bonus tutarları ve geçerlilik sürelerini detaylıca inceledik.</p>
<h2>Değerlendirme Kriterleri</h2>
<ul>
<li>Bonus yüzdesi ve maksimum tutarı</li>
<li>Çevirme şartı (wager)</li>
<li>Geçerlilik süresi</li>
<li>Minimum yatırım tutarı</li>
<li>Geçerli oyun türleri</li>
</ul>
<h2>En İyi Hoş Geldin Bonusları</h2>
<p>Analiz sonuçlarımıza göre en avantajlı hoş geldin bonuslarını sunan siteler yukarıdaki sponsorlarımız arasında yer almaktadır.</p>',
                'meta_title' => 'Hoş Geldin Bonusu Karşılaştırma - Bonus Avı',
                'meta_description' => 'Bahis sitelerinin hoş geldin bonusu karşılaştırma tablosu.',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'slug' => 'kayip-bonusu-nedir',
                'title' => 'Kayıp Bonusu Nedir? En İyi Kayıp Bonusu Veren Siteler',
                'excerpt' => 'Kayıp bonusu hakkında detaylı bilgi ve en avantajlı kampanyalar.',
                'content' => '<h1>Kayıp Bonusu Nedir?</h1>
<p>Kayıp bonusu, belirli bir dönemde yaşadığınız kayıpların bir kısmının geri iade edilmesidir. Genellikle %10 ile %20 arasında değişir.</p>
<h2>Kayıp Bonusu Nasıl Çalışır?</h2>
<p>Örneğin, haftalık %15 kayıp bonusu olan bir sitede 1000 TL kaybettiyseniz, 150 TL geri alırsınız. Bu bonus genellikle düşük çevrim şartıyla veya çevrimsiz olarak verilir.</p>
<h2>Kayıp Bonusu Seçerken Dikkat</h2>
<ul>
<li>Bonus yüzdesi</li>
<li>Hesaplama periyodu (günlük, haftalık, aylık)</li>
<li>Minimum kayıp tutarı</li>
<li>Çevirme şartı olup olmadığı</li>
</ul>',
                'meta_title' => 'Kayıp Bonusu Nedir? - Bonus Avı',
                'meta_description' => 'Kayıp bonusu nedir, nasıl hesaplanır? En iyi kayıp bonusu veren siteler.',
                'is_published' => true,
                'published_at' => now()->subDays(6),
            ],
            [
                'slug' => 'promosyon-kodu-nasil-kullanilir',
                'title' => 'Promosyon Kodu Nasıl Kullanılır?',
                'excerpt' => 'Bahis sitelerinde promosyon kodu kullanma rehberi.',
                'content' => '<h1>Promosyon Kodu Kullanma Rehberi</h1>
<p>Promosyon kodları, bahis sitelerinden ekstra bonus almanızı sağlayan özel kodlardır. İşte adım adım kullanım rehberi.</p>
<h2>Adımlar</h2>
<ol>
<li>Güvenilir bir kaynaktan promosyon kodunu alın</li>
<li>Bahis sitesine giriş yapın veya üye olun</li>
<li>Yatırım sayfasına gidin</li>
<li>Promosyon kodu alanına kodu girin</li>
<li>Yatırımınızı tamamlayın</li>
</ol>
<h2>Dikkat Edilecekler</h2>
<p>Promosyon kodlarının genellikle son kullanma tarihi vardır. Kodun geçerli olduğundan emin olun.</p>',
                'meta_title' => 'Promosyon Kodu Nasıl Kullanılır? - Bonus Avı',
                'meta_description' => 'Bahis sitelerinde promosyon kodu kullanma adımları ve ipuçları.',
                'is_published' => true,
                'published_at' => now()->subDays(9),
            ],
            [
                'slug' => 'free-spin-bonusu-veren-siteler',
                'title' => 'Free Spin Bonusu Veren Siteler 2026',
                'excerpt' => 'Bedava çevirme (free spin) bonusu veren en iyi casino siteleri.',
                'content' => '<h1>Free Spin Bonusu Veren Siteler</h1>
<p>Free spin bonusları, slot oyunlarında ücretsiz çevirme hakkı sunan popüler bir bonus türüdür.</p>
<h2>Free Spin Nedir?</h2>
<p>Free spin, slot oyunlarında kendi bakiyenizden herhangi bir düşüm olmadan yapılan çevirmelerdir. Kazandığınız tutarlar genellikle bonus bakiyesi olarak yansır.</p>
<h2>Free Spin Türleri</h2>
<ul>
<li><strong>Kayıt Free Spin:</strong> Üyelik oluşturduğunuzda verilen bedava çevirmeler</li>
<li><strong>Yatırım Free Spin:</strong> Yatırım yaptığınızda eklenen free spinler</li>
<li><strong>Haftalık Free Spin:</strong> Düzenli olarak dağıtılan bedava çevirmeler</li>
</ul>',
                'meta_title' => 'Free Spin Bonusu Veren Siteler 2026 - Bonus Avı',
                'meta_description' => '2026 free spin bonusu veren güvenilir casino siteleri listesi.',
                'is_published' => true,
                'published_at' => now()->subDays(13),
            ],
            [
                'slug' => 'cevirme-sarti-hesaplama',
                'title' => 'Çevirme Şartı Hesaplama: Kolay Rehber',
                'excerpt' => 'Bonus çevirme şartlarını kolayca hesaplama yöntemi.',
                'content' => '<h1>Çevirme Şartı Hesaplama</h1>
<p>Bonus çevirme şartını doğru hesaplamak, bonusun gerçek değerini anlamanıza yardımcı olur.</p>
<h2>Hesaplama Formülü</h2>
<p><strong>Toplam çevrilmesi gereken tutar = Bonus Tutarı × Çevirme Şartı</strong></p>
<h2>Örnek</h2>
<p>100 TL bonus + 30x çevirme şartı = 100 × 30 = 3000 TL toplam bahis yapmanız gerekir.</p>
<h2>Sadece Bonus mu, Yatırım + Bonus mu?</h2>
<p>Bazı siteler çevirme şartını sadece bonus tutarına, bazıları ise yatırım + bonus toplamına uygular. Bu önemli farkı mutlaka kontrol edin.</p>',
                'meta_title' => 'Çevirme Şartı Hesaplama - Bonus Avı',
                'meta_description' => 'Bahis bonusu çevirme şartı nasıl hesaplanır? Kolay hesaplama rehberi.',
                'is_published' => true,
                'published_at' => now()->subDays(16),
            ],
        ];
    }

    private function bonusAviOffers(): array
    {
        return [
            [
                'slug' => 'bonusking',
                'logo_url' => 'https://placehold.co/120x60/6A1B9A/white?text=BonusKing',
                'bonus_text' => '750 TL Deneme Bonusu',
                'cta_text' => 'BONUS AL',
                'target_url' => 'https://example.com/go/bonusking',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'megabonus',
                'logo_url' => 'https://placehold.co/120x60/00E5FF/333?text=MegaBonus',
                'bonus_text' => '%300 Hoş Geldin Paketi',
                'cta_text' => 'ÜYE OL',
                'target_url' => 'https://example.com/go/megabonus',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];
    }

    private function bonusAviRedirects(): array
    {
        return [
            ['slug' => 'login', 'target_url' => 'https://example.com/login/bonusavi', 'is_active' => true],
            ['slug' => 'bonusking', 'target_url' => 'https://example.com/go/bonusking', 'is_active' => true],
            ['slug' => 'megabonus', 'target_url' => 'https://example.com/go/megabonus', 'is_active' => true],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Site 4: İddia Master
    // ─────────────────────────────────────────────────────────────────────

    private function iddiaMasterPages(): array
    {
        return [
            [
                'slug' => 'ana-sayfa',
                'title' => 'Ana Sayfa',
                'content' => '<h1>İddia Master\'a Hoş Geldiniz</h1>
<p>Profesyonel bahis tahminleri, detaylı maç analizleri ve uzman yorumları ile İddia Master\'a hoş geldiniz. Spor bahisleri dünyasında bir adım öne geçin!</p>
<h2>Neden İddia Master?</h2>
<ul>
<li><strong>Uzman Analizler:</strong> Profesyonel analistler tarafından hazırlanan maç tahminleri.</li>
<li><strong>İstatistik Tabanlı:</strong> Veriye dayalı analizler ve trendler.</li>
<li><strong>Günlük Güncellemeler:</strong> Her gün yeni tahminler ve öneriler.</li>
</ul>
<h2>Bugünün Öne Çıkan Maçları</h2>
<p>Günlük tahminlerimiz ve banko kupon önerilerimiz için blog sayfamızı takip edin.</p>',
                'meta_title' => 'İddia Master - Profesyonel Bahis Tahminleri',
                'meta_description' => 'Uzman bahis analizleri, maç tahminleri ve güvenilir bahis siteleri incelemeleri.',
                'is_published' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'hakkimizda',
                'title' => 'Hakkımızda',
                'content' => '<h1>İddia Master Hakkında</h1>
<p>İddia Master, spor bahisleri konusunda uzmanlaşmış bir analiz ve tahmin platformudur. Amacımız, bahis severlerle kaliteli ve güvenilir içerik paylaşmaktır.</p>
<h2>Analiz Yöntemimiz</h2>
<p>Takım formları, oyuncu istatistikleri, karşılaşma geçmişi ve çeşitli dış faktörleri bir arada değerlendirerek tahminlerimizi oluşturuyoruz.</p>',
                'meta_title' => 'Hakkımızda - İddia Master',
                'meta_description' => 'İddia Master analiz ekibi ve yöntemleri hakkında bilgi.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'content' => '<h1>İletişim</h1>
<p>Tahmin istekleri, işbirliği teklifleri veya genel sorularınız için bizimle iletişime geçin.</p>
<ul>
<li><strong>E-posta:</strong> info@iddiamaster.test</li>
<li><strong>Telegram:</strong> @iddiamaster</li>
<li><strong>Twitter/X:</strong> @iddiamaster</li>
</ul>',
                'meta_title' => 'İletişim - İddia Master',
                'meta_description' => 'İddia Master iletişim bilgileri.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => 'Gizlilik Politikası',
                'content' => '<h1>Gizlilik Politikası</h1>
<p>İddia Master, kullanıcı verilerinin gizliliğini korumayı taahhüt eder. Kişisel verileriniz üçüncü taraflarla paylaşılmaz ve yalnızca hizmet kalitesini artırmak amacıyla kullanılır.</p>',
                'meta_title' => 'Gizlilik Politikası - İddia Master',
                'meta_description' => 'İddia Master gizlilik politikası.',
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];
    }

    private function iddiaMasterPosts(): array
    {
        return [
            [
                'slug' => 'super-lig-hafta-analizi',
                'title' => 'Süper Lig Bu Hafta: Detaylı Maç Analizleri',
                'excerpt' => 'Süper Lig\'de bu haftanın maçlarının detaylı analizi ve tahminleri.',
                'content' => '<h1>Süper Lig Haftalık Analiz</h1>
<p>Süper Lig\'de bu haftanın kritik maçlarını analiz ettik. Form durumları, eksik oyuncular ve tarihsel istatistikler ışığında tahminlerimizi sunuyoruz.</p>
<h2>Galatasaray - Fenerbahçe</h2>
<p>Sezonun en büyük derbisi yaklaşıyor. Her iki takım da liderlik yarışında kritik bir virajda. Galatasaray iç saha performansıyla öne çıkarken, Fenerbahçe deplasman serisine güveniyor.</p>
<h2>Beşiktaş - Trabzonspor</h2>
<p>İki köklü kulübün karşılaşması her zaman heyecanlı geçiyor. İstatistikler, son 5 maçta 3 beraberlik olduğunu gösteriyor.</p>
<h2>Tahminimiz</h2>
<p>Detaylı analiz ve istatistiklere dayalı tahminlerimizi düzenli olarak güncelliyoruz.</p>',
                'meta_title' => 'Süper Lig Haftalık Analiz - İddia Master',
                'meta_description' => 'Bu haftanın Süper Lig maç analizleri ve bahis tahminleri.',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'slug' => 'bankroll-yonetimi-rehberi',
                'title' => 'Bankroll Yönetimi: Profesyonel Bahisçinin Rehberi',
                'excerpt' => 'Bahis bütçenizi profesyonelce yönetmek için altın kurallar.',
                'content' => '<h1>Bankroll Yönetimi Rehberi</h1>
<p>Başarılı bir bahisçinin en önemli silahı bankroll yönetimidir. Doğru bütçe yönetimi ile uzun vadede karlı kalabilirsiniz.</p>
<h2>Altın Kurallar</h2>
<h3>1. Bütçe Belirleyin</h3>
<p>Kaybetmeyi göze alabileceğiniz bir miktar belirleyin ve bu sınırı asla aşmayın.</p>
<h3>2. Birim Bahis Sistemi</h3>
<p>Toplam bankroll\'ünüzün %1-5\'i arasında bir birim belirleyin. Her bahiste 1-3 birim kullanın.</p>
<h3>3. Kayıp Serilerinde Artırmayın</h3>
<p>Kayıp serisindeyken bahis miktarını artırmak en büyük hatadır. Disiplininizi koruyun.</p>
<h3>4. Kayıt Tutun</h3>
<p>Tüm bahislerinizi kayıt altına alın. Hangi bahis türlerinde başarılı olduğunuzu analiz edin.</p>',
                'meta_title' => 'Bankroll Yönetimi Rehberi - İddia Master',
                'meta_description' => 'Profesyonel bahis bütçe yönetimi kuralları ve stratejileri.',
                'is_published' => true,
                'published_at' => now()->subDays(4),
            ],
            [
                'slug' => 'value-bet-nedir',
                'title' => 'Value Bet Nedir? Değerli Bahisleri Nasıl Bulursunuz?',
                'excerpt' => 'Value bet kavramı ve değerli bahisleri tespit etme yöntemleri.',
                'content' => '<h1>Value Bet Nedir?</h1>
<p>Value bet, bahis şirketinin sunduğu oranın, gerçek olasılıktan daha yüksek olduğu durumlardır. Uzun vadede kar etmenin temel prensibidir.</p>
<h2>Value Bet Nasıl Hesaplanır?</h2>
<p>Bir sonucun gerçek olasılığını tahmin edip, bahis oranıyla karşılaştırırsınız.</p>
<p><strong>Formül:</strong> Değer = (Oran × Tahmini Olasılık) - 1</p>
<p>Sonuç pozitifse, value bet vardır.</p>
<h2>Örnek</h2>
<p>Bir takımın kazanma olasılığını %50 olarak tahmin ediyorsunuz. Bahis oranı 2.20. Değer = (2.20 × 0.50) - 1 = 0.10 (pozitif = value bet var)</p>
<h2>Value Bet Bulma İpuçları</h2>
<ul>
<li>İstatistikleri detaylıca inceleyin</li>
<li>Birden fazla bahis sitesinin oranlarını karşılaştırın</li>
<li>Kamuoyunun etkisine kapılmayın</li>
</ul>',
                'meta_title' => 'Value Bet Nedir? - İddia Master',
                'meta_description' => 'Value bet kavramı, hesaplama yöntemi ve değerli bahis bulma ipuçları.',
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'slug' => 'sampiyonlar-ligi-tahminleri',
                'title' => 'Şampiyonlar Ligi Tahminleri ve Analizleri',
                'excerpt' => 'UEFA Şampiyonlar Ligi maç tahminleri ve detaylı analizler.',
                'content' => '<h1>Şampiyonlar Ligi Tahminleri</h1>
<p>Avrupa\'nın en prestijli turnuvası Şampiyonlar Ligi\'nin bu haftaki maçlarını analiz ettik.</p>
<h2>Grup Maçları Analizi</h2>
<p>Grup aşamasındaki kritik maçları form durumları, kadro güçleri ve taktiksel yaklaşımlar açısından inceledik.</p>
<h2>İstatistiksel Veriler</h2>
<p>Son 10 yılın Şampiyonlar Ligi verilerini analiz ettiğimizde, ev sahibi takımların grup maçlarında %48 kazanma oranına sahip olduğunu görüyoruz.</p>
<h2>Banko Maçlar</h2>
<p>Bu haftanın en güvenilir tahminlerini ve banko kuponu blog sayfamızda paylaşıyoruz.</p>',
                'meta_title' => 'Şampiyonlar Ligi Tahminleri - İddia Master',
                'meta_description' => 'Şampiyonlar Ligi maç analizleri ve bahis tahminleri.',
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'slug' => 'bahis-terimleri-sozlugu',
                'title' => 'Bahis Terimleri Sözlüğü: A\'dan Z\'ye',
                'excerpt' => 'Bahis dünyasında kullanılan tüm terimler ve anlamları.',
                'content' => '<h1>Bahis Terimleri Sözlüğü</h1>
<p>Bahis dünyasına yeni adım atanlar için kapsamlı bir terim sözlüğü hazırladık.</p>
<h2>A</h2>
<p><strong>Accumulator (Akümülatör):</strong> Birden fazla bahisin tek kuponda birleştirilmesi. Kombine bahis olarak da bilinir.</p>
<h2>B</h2>
<p><strong>Banko:</strong> Kazanma olasılığı çok yüksek görülen bahis.</p>
<p><strong>Bankroll:</strong> Bahis için ayrılan toplam bütçe.</p>
<h2>H</h2>
<p><strong>Handikap:</strong> Takımlar arasındaki güç farkını dengelemek için verilen avantaj/dezavantaj.</p>
<h2>K</h2>
<p><strong>Kupon:</strong> Yapılan bahislerin toplandığı bilet.</p>
<h2>M</h2>
<p><strong>Maç Sonucu (1X2):</strong> Ev sahibi kazanır (1), beraberlik (X), deplasman kazanır (2).</p>
<h2>V</h2>
<p><strong>Value Bet:</strong> Gerçek olasılığa göre oranı yüksek olan bahis.</p>',
                'meta_title' => 'Bahis Terimleri Sözlüğü - İddia Master',
                'meta_description' => 'Bahis dünyasında kullanılan tüm terimlerin anlamları ve açıklamaları.',
                'is_published' => true,
                'published_at' => now()->subDays(18),
            ],
        ];
    }

    private function iddiaMasterOffers(): array
    {
        return [
            [
                'slug' => 'sportbet',
                'logo_url' => 'https://placehold.co/120x60/0D47A1/white?text=SportBet',
                'bonus_text' => '%100 Spor Hoş Geldin',
                'cta_text' => 'HEMEN BAHİS YAP',
                'target_url' => 'https://example.com/go/sportbet',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'slug' => 'iddaapro',
                'logo_url' => 'https://placehold.co/120x60/FF6D00/white?text=İddaaPro',
                'bonus_text' => '250 TL Freebet',
                'cta_text' => 'ÜYE OL',
                'target_url' => 'https://example.com/go/iddaapro',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'slug' => 'liveskor',
                'logo_url' => 'https://placehold.co/120x60/4CAF50/white?text=LiveSkor',
                'bonus_text' => '%50 Canlı Bahis Bonusu',
                'cta_text' => 'KATIL',
                'target_url' => 'https://example.com/go/liveskor',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];
    }

    private function iddiaMasterRedirects(): array
    {
        return [
            ['slug' => 'login', 'target_url' => 'https://example.com/login/iddiamaster', 'is_active' => true],
            ['slug' => 'sportbet', 'target_url' => 'https://example.com/go/sportbet', 'is_active' => true],
            ['slug' => 'iddaapro', 'target_url' => 'https://example.com/go/iddaapro', 'is_active' => true],
            ['slug' => 'liveskor', 'target_url' => 'https://example.com/go/liveskor', 'is_active' => true],
            ['slug' => 'telegram', 'target_url' => 'https://t.me/iddiamaster', 'is_active' => true],
        ];
    }
}
