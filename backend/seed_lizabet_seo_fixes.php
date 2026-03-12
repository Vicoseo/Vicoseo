<?php
/**
 * Lizabet 4 site SEO düzeltmeleri:
 * 1. Site meta_title, meta_description ekleme
 * 2. Kategoriler oluşturma ve makalelere atama
 * 3. "Hakkımızda" ve "İletişim" sayfaları ekleme
 * 4. "Gizlilik Politikası" ve "Çerez Politikası" sayfaları ekleme
 *
 * Run: php artisan tinker --execute="require 'seed_lizabet_seo_fixes.php';"
 */

use App\Models\Site;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

// ═══════════════════════════════════════════════════
// 1. SITE META TITLE & DESCRIPTION
// ═══════════════════════════════════════════════════

$siteMeta = [
    'lizabetcasino.com' => [
        'meta_title' => 'Lizabet Casino - Canlı Casino, Slot Oyunları ve Hoşgeldin Bonusu',
        'meta_description' => 'Lizabet Casino resmi sitesi. Canlı casino, slot oyunları, rulet, blackjack ve özel hoşgeldin bonusu ile güvenilir casino deneyimi. Hemen üye olun.',
    ],
    'lizabetgiris.site' => [
        'meta_title' => 'Lizabet Giriş - Güncel Giriş Adresi ve Yeni Link 2026',
        'meta_description' => 'Lizabet güncel giriş adresi 2026. Yeni giriş linki, mobil erişim, VPN ayarları ve hesap kurtarma rehberi. Lizabet\'e hızlı ve güvenli erişim.',
    ],
    'lizabahis.site' => [
        'meta_title' => 'Lizabet Bahis - Spor Bahisleri, Canlı Bahis ve Yüksek Oranlar',
        'meta_description' => 'Lizabet spor bahisleri platformu. Canlı bahis, futbol, basketbol, e-spor bahisleri ve yüksek oranlar. Kombine kupon stratejileri ve bonuslar.',
    ],
    'girislizabet.site' => [
        'meta_title' => 'Lizabet - Güvenilir Bahis ve Casino Platformu İnceleme',
        'meta_description' => 'Lizabet platform incelemesi. Ödeme yöntemleri, Papara, kripto para işlemleri, müşteri hizmetleri ve kullanıcı deneyimleri. Detaylı rehber.',
    ],
];

foreach ($siteMeta as $domain => $meta) {
    Site::where('domain', $domain)->update($meta);
    echo "[META] {$domain} updated\n";
}

// ═══════════════════════════════════════════════════
// 2. KATEGORİLER
// ═══════════════════════════════════════════════════

$categoryMap = [
    'lizabetcasino.com' => [
        ['slug' => 'casino-oyunlari', 'name' => 'Casino Oyunları', 'description' => 'Canlı casino, slot, rulet ve blackjack oyunları hakkında rehberler ve ipuçları.', 'meta_title' => 'Casino Oyunları Rehberi | Lizabet', 'meta_description' => 'Lizabet casino oyunları: Slot, rulet, blackjack ve canlı casino rehberleri. Strateji ipuçları ve kazanma taktikleri.'],
        ['slug' => 'bonuslar', 'name' => 'Bonuslar ve Promosyonlar', 'description' => 'Hoşgeldin bonusu, freespin kampanyaları, VIP ödülleri ve kripto bonusları.', 'meta_title' => 'Bonuslar ve Promosyonlar | Lizabet', 'meta_description' => 'Lizabet bonus rehberi: Hoşgeldin bonusu, freespin, VIP programı ve kripto bonusları hakkında detaylı bilgi.'],
        ['slug' => 'rehber', 'name' => 'Rehber', 'description' => 'Kayıt, giriş, ödeme ve mobil kullanım rehberleri.', 'meta_title' => 'Kullanım Rehberi | Lizabet', 'meta_description' => 'Lizabet kullanım rehberi: Kayıt olma, giriş yapma, para yatırma ve mobil casino deneyimi.'],
    ],
    'lizabetgiris.site' => [
        ['slug' => 'giris-rehberi', 'name' => 'Giriş Rehberi', 'description' => 'Lizabet güncel giriş adresi, yeni link ve erişim yöntemleri.', 'meta_title' => 'Giriş Rehberi | Lizabet', 'meta_description' => 'Lizabet giriş rehberi: Güncel adres, yeni link, VPN ile giriş ve DNS ayarları.'],
        ['slug' => 'hesap-islemleri', 'name' => 'Hesap İşlemleri', 'description' => 'Kayıt olma, şifre sıfırlama, hesap kurtarma ve güvenlik ayarları.', 'meta_title' => 'Hesap İşlemleri | Lizabet', 'meta_description' => 'Lizabet hesap işlemleri: Üyelik açma, şifre sıfırlama ve hesap güvenliği rehberi.'],
        ['slug' => 'iletisim-destek', 'name' => 'İletişim ve Destek', 'description' => 'Telegram kanalı, müşteri hizmetleri ve destek kanalları.', 'meta_title' => 'İletişim ve Destek | Lizabet', 'meta_description' => 'Lizabet iletişim kanalları: Telegram, canlı destek ve müşteri hizmetleri.'],
    ],
    'lizabahis.site' => [
        ['slug' => 'spor-bahisleri', 'name' => 'Spor Bahisleri', 'description' => 'Futbol, basketbol, tenis ve e-spor bahisleri hakkında stratejiler.', 'meta_title' => 'Spor Bahisleri Rehberi | Lizabet', 'meta_description' => 'Lizabet spor bahisleri: Futbol, basketbol, e-spor bahis stratejileri ve yüksek oran analizleri.'],
        ['slug' => 'bahis-stratejileri', 'name' => 'Bahis Stratejileri', 'description' => 'Kombine kupon taktikleri, oran hesaplama ve cash out kullanımı.', 'meta_title' => 'Bahis Stratejileri | Lizabet', 'meta_description' => 'Lizabet bahis stratejileri: Kombine kupon, oran analizi, cash out ve bankroll yönetimi.'],
        ['slug' => 'bonuslar-kampanyalar', 'name' => 'Bonuslar ve Kampanyalar', 'description' => 'Spor bahis bonusları, kayıp bonusu ve özel kampanyalar.', 'meta_title' => 'Bahis Bonusları | Lizabet', 'meta_description' => 'Lizabet bahis bonusları: Hoşgeldin bonusu, kayıp bonusu ve özel kampanyalar.'],
    ],
    'girislizabet.site' => [
        ['slug' => 'platform-inceleme', 'name' => 'Platform İnceleme', 'description' => 'Lizabet platform değerlendirmesi, avantajlar, dezavantajlar ve karşılaştırmalar.', 'meta_title' => 'Platform İnceleme | Lizabet', 'meta_description' => 'Lizabet detaylı inceleme: Güvenilirlik, avantajlar, dezavantajlar ve rakip karşılaştırma.'],
        ['slug' => 'odeme-yontemleri', 'name' => 'Ödeme Yöntemleri', 'description' => 'Papara, kripto para, banka havalesi ve diğer ödeme seçenekleri.', 'meta_title' => 'Ödeme Yöntemleri | Lizabet', 'meta_description' => 'Lizabet ödeme yöntemleri: Papara, kripto para, banka havalesi ve yatırım/çekim rehberi.'],
        ['slug' => 'kullanici-rehberi', 'name' => 'Kullanıcı Rehberi', 'description' => 'Hesap güvenliği, bonus çevrim şartları ve doğrulama süreçleri.', 'meta_title' => 'Kullanıcı Rehberi | Lizabet', 'meta_description' => 'Lizabet kullanıcı rehberi: Hesap doğrulama, bonus çevrim şartları ve güvenlik ipuçları.'],
    ],
];

// Post -> category assignment map
$postCategories = [
    'lizabetcasino.com' => [
        'casino-oyunlari' => ['lizabet-slot-oyunlari-rehberi', 'lizabet-slot-oyunlari-en-populer-10-slot', 'lizabet-canli-casino-deneyimi', 'lizabet-rulet-rehberi-strateji-ve-ipuclari', 'lizabet-blackjack-nasil-oynanir', 'lizabet-mobil-casino-telefondan-oynama'],
        'bonuslar' => ['lizabet-hosgeldin-bonusu-nasil-alinir', 'lizabet-freespin-kampanyasi-ucretsiz-cevirme', 'lizabet-vip-programi-ve-sadakat-odulleri', 'lizabet-arkadas-davet-bonusu'],
        'rehber' => ['lizabet-casino-giris-rehberi-2026', 'lizabet-kripto-ile-casino-oynama-rehberi'],
    ],
    'lizabetgiris.site' => [
        'giris-rehberi' => ['lizabet-guncel-giris-adresi-mart-2026', 'lizabet-yeni-adres-nasil-bulunur', 'lizabet-mobil-giris-rehberi-2026', 'lizabet-vpn-ile-giris-yapma', 'lizabet-dns-ayarlari-ile-erisim', 'lizabet-guvenli-giris-ipuclari-2026', 'lizabet-giris-sorunu-cozumleri'],
        'hesap-islemleri' => ['lizabet-kayit-olma-rehberi-adim-adim', 'lizabet-uyelik-nasil-acilir', 'lizabet-sifre-sifirlama-ve-hesap-kurtarma'],
        'iletisim-destek' => ['lizabet-telegram-kanali-ve-duyurular', 'lizabet-canli-casino-deneyimi'],
    ],
    'lizabahis.site' => [
        'spor-bahisleri' => ['lizabet-spor-bahisleri-rehberi-2026', 'lizabet-canli-bahis-nasil-yapilir', 'lizabet-futbol-bahis-rehberi', 'lizabet-basketbol-bahis-rehberi', 'lizabet-e-spor-bahisleri-cs2-lol-valorant'],
        'bahis-stratejileri' => ['lizabet-kombine-kupon-stratejileri', 'lizabet-bahis-oranlari-nasil-hesaplanir', 'lizabet-cash-out-ozelligi-nasil-kullanilir', 'lizabet-mobil-bahis-deneyimi-2026'],
        'bonuslar-kampanyalar' => ['lizabet-bahis-bonuslari-ve-kampanyalar', 'lizabet-canli-bahis-rehberi', 'lizabet-papara-ile-yatirim'],
    ],
    'girislizabet.site' => [
        'platform-inceleme' => ['lizabet-guvenilir-mi-detayli-inceleme', 'lizabet-kullanici-yorumlari-deneyimler', 'lizabet-avantajlari-dezavantajlari', 'lizabet-diger-platformlar-karsilastirma'],
        'odeme-yontemleri' => ['lizabet-odeme-yontemleri-rehberi', 'lizabet-papara-yatirim-cekim', 'lizabet-kripto-para-islem-rehberi', 'lizabet-kripto-ile-yatirim'],
        'kullanici-rehberi' => ['lizabet-musteri-hizmetleri-iletisim-rehberi', 'lizabet-bonus-cevrim-sartlari-aciklamasi', 'lizabet-hesap-dogrulama-sureci', 'lizabet-hesap-guvenligi'],
    ],
];

foreach ($categoryMap as $domain => $categories) {
    $site = Site::where('domain', $domain)->firstOrFail();
    Config::set('database.connections.tenant.database', $site->db_name);
    DB::purge('tenant');
    DB::reconnect('tenant');

    echo "\n[CATEGORIES] {$domain}\n";

    foreach ($categories as $cat) {
        $existing = DB::connection('tenant')->table('categories')->where('slug', $cat['slug'])->first();
        if ($existing) {
            echo "  SKIP: {$cat['slug']} (exists)\n";
            $catId = $existing->id;
        } else {
            $catId = DB::connection('tenant')->table('categories')->insertGetId([
                'slug' => $cat['slug'],
                'name' => $cat['name'],
                'description' => $cat['description'],
                'meta_title' => $cat['meta_title'],
                'meta_description' => $cat['meta_description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "  OK: {$cat['slug']} (id: {$catId})\n";
        }

        // Assign posts to category
        if (isset($postCategories[$domain][$cat['slug']])) {
            foreach ($postCategories[$domain][$cat['slug']] as $postSlug) {
                DB::connection('tenant')
                    ->table('posts')
                    ->where('slug', $postSlug)
                    ->whereNull('category_id')
                    ->update(['category_id' => $catId]);
            }
        }
    }
}

// ═══════════════════════════════════════════════════
// 3. EK SAYFALAR — Hakkımızda, İletişim, Gizlilik, Çerez
// ═══════════════════════════════════════════════════

$extraPages = [
    'lizabetcasino.com' => [
        [
            'slug' => 'hakkimizda',
            'title' => 'Hakkımızda',
            'meta_title' => 'Hakkımızda | Lizabet Casino',
            'meta_description' => 'Lizabet Casino hakkında bilgi. Güvenilir casino platformu, lisans bilgileri ve hizmet anlayışımız.',
            'sort_order' => 10,
            'content' => '<h2>Lizabet Casino Hakkında</h2>
<p>Lizabet Casino, çevrimiçi casino ve bahis sektöründe kullanıcılarına güvenilir, adil ve eğlenceli bir oyun deneyimi sunmayı amaçlayan bir platformdur. Geniş oyun yelpazesi, yüksek güvenlik standartları ve kullanıcı odaklı yaklaşımıyla sektörde öne çıkmaktadır.</p>

<h2>Misyonumuz</h2>
<p>Kullanıcılarımıza en kaliteli casino ve bahis deneyimini sunmak, sorumlu oyun ilkelerine bağlı kalmak ve her zaman şeffaf bir hizmet anlayışı benimsemek temel misyonumuzdur. Lizabet olarak, teknolojinin sunduğu en son yenilikleri takip ederek platformumuzu sürekli geliştirmekteyiz.</p>

<h2>Neden Lizabet?</h2>
<p>Lizabet Casino, sektördeki deneyimi ve güçlü altyapısıyla kullanıcılarına kesintisiz bir hizmet sunmaktadır. Pragmatic Play, Evolution Gaming gibi dünyaca ünlü oyun sağlayıcılarıyla iş birliği yaparak geniş bir oyun kütüphanesi sunuyoruz.</p>
<p>7/24 canlı destek hizmeti, hızlı ödeme işlemleri ve çeşitli bonus fırsatlarıyla kullanıcı memnuniyetini en üst düzeyde tutmayı hedefliyoruz. Güvenlik konusunda SSL şifreleme ve ileri düzey veri koruma protokolleri kullanarak kullanıcı bilgilerinin güvenliğini sağlıyoruz.</p>

<h2>Oyun Çeşitliliği</h2>
<p>Lizabet Casino bünyesinde binlerce slot oyunu, canlı casino masaları, sanal spor bahisleri ve özel turnuvalar yer almaktadır. Sürekli güncellenen oyun kütüphanemiz ile kullanıcılarımıza her zaman yeni ve heyecan verici içerikler sunuyoruz.</p>',
        ],
        [
            'slug' => 'iletisim',
            'title' => 'İletişim',
            'meta_title' => 'İletişim | Lizabet Casino',
            'meta_description' => 'Lizabet Casino iletişim bilgileri. Canlı destek, Telegram, e-posta ve sosyal medya kanallarından bize ulaşın.',
            'sort_order' => 11,
            'content' => '<h2>Bize Ulaşın</h2>
<p>Lizabet Casino olarak kullanıcılarımızla sürekli iletişim halinde olmayı önemsiyoruz. Sorularınız, önerileriniz veya karşılaştığınız herhangi bir sorun için aşağıdaki kanallardan bize ulaşabilirsiniz.</p>

<h2>Canlı Destek</h2>
<p>7/24 canlı destek hizmetimiz ile anında yardım alabilirsiniz. Web sitemizin sağ alt köşesindeki canlı destek butonuna tıklayarak destek ekibimizle iletişime geçebilirsiniz. Ortalama yanıt süremiz 30 saniyenin altındadır.</p>

<h2>Telegram Kanalımız</h2>
<p>Güncel adres değişiklikleri, kampanya duyuruları ve özel bonus fırsatları için resmi Telegram kanalımızı takip edebilirsiniz. Telegram üzerinden de destek ekibimize ulaşmanız mümkündür.</p>

<h2>Sosyal Medya</h2>
<p>Lizabet\'i Instagram, X (Twitter), TikTok ve YouTube kanallarımızdan takip ederek güncel gelişmelerden haberdar olabilirsiniz. Sosyal medya hesaplarımız üzerinden düzenlenen özel etkinlikler ve çekilişlere katılma fırsatı yakalayabilirsiniz.</p>

<h2>Yanıt Süreleri</h2>
<p>Canlı destek üzerinden yapılan başvurular anında yanıtlanmaktadır. E-posta ile iletilen talepler en geç 24 saat içinde cevaplanmaktadır. Hesap doğrulama ve ödeme işlemleri ile ilgili konularda öncelikli destek sağlanmaktadır.</p>',
        ],
        [
            'slug' => 'gizlilik-politikasi',
            'title' => 'Gizlilik Politikası',
            'meta_title' => 'Gizlilik Politikası | Lizabet Casino',
            'meta_description' => 'Lizabet Casino gizlilik politikası. Kişisel verilerin korunması, veri işleme süreçleri ve kullanıcı hakları hakkında detaylı bilgi.',
            'sort_order' => 97,
            'content' => '<h2>Gizlilik Politikası</h2>
<p>Lizabet Casino olarak kullanıcılarımızın kişisel verilerinin korunmasına büyük önem veriyoruz. Bu gizlilik politikası, hangi verilerin toplandığını, nasıl kullanıldığını ve nasıl korunduğunu açıklamaktadır.</p>

<h2>Toplanan Veriler</h2>
<p>Platform üzerinden kayıt olurken ve hizmetlerimizi kullanırken aşağıdaki bilgiler toplanabilmektedir: ad-soyad, e-posta adresi, telefon numarası, doğum tarihi, IP adresi ve cihaz bilgileri. Bu veriler, hesap oluşturma, kimlik doğrulama ve hizmet sunumu amacıyla işlenmektedir.</p>

<h2>Verilerin Kullanımı</h2>
<p>Topladığımız kişisel veriler şu amaçlarla kullanılmaktadır: hesap yönetimi, ödeme işlemlerinin gerçekleştirilmesi, müşteri desteği sağlanması, yasal yükümlülüklerin yerine getirilmesi ve hizmet kalitesinin iyileştirilmesi. Lizabet, kullanıcı verilerini pazarlama amacıyla üçüncü taraflarla paylaşmamaktadır.</p>

<h2>Veri Güvenliği</h2>
<p>Kullanıcı verileri SSL şifreleme teknolojisi ile korunmaktadır. Veritabanlarımız güvenlik duvarları ve erişim kontrol mekanizmaları ile güvence altına alınmıştır. Düzenli güvenlik denetimleri yapılmakta ve potansiyel tehditlere karşı proaktif önlemler alınmaktadır.</p>

<h2>Çerezler</h2>
<p>Web sitemiz, kullanıcı deneyimini iyileştirmek amacıyla çerezler kullanmaktadır. Çerezler, oturum yönetimi, tercih kaydetme ve site performansını analiz etme amacıyla kullanılmaktadır. Tarayıcı ayarlarınızdan çerez tercihlerinizi yönetebilirsiniz.</p>

<h2>Kullanıcı Hakları</h2>
<p>Kullanıcılarımız kişisel verilerine erişim, düzeltme ve silme hakkına sahiptir. Bu haklarınızı kullanmak için müşteri destek ekibimizle iletişime geçebilirsiniz. Veri taşınabilirliği ve işleme itirazı gibi haklar da yasal çerçevede değerlendirilmektedir.</p>',
        ],
        [
            'slug' => 'cerez-politikasi',
            'title' => 'Çerez Politikası',
            'meta_title' => 'Çerez Politikası | Lizabet Casino',
            'meta_description' => 'Lizabet Casino çerez politikası. Web sitemizde kullanılan çerezler, türleri ve yönetimi hakkında bilgi.',
            'sort_order' => 98,
            'content' => '<h2>Çerez Politikası</h2>
<p>Lizabet Casino web sitesi, kullanıcı deneyimini geliştirmek ve hizmet kalitesini artırmak amacıyla çerezler kullanmaktadır. Bu politika, kullanılan çerez türlerini ve yönetim seçeneklerini açıklamaktadır.</p>

<h2>Çerez Nedir?</h2>
<p>Çerezler, web sitelerinin tarayıcınıza kaydettiği küçük metin dosyalarıdır. Bu dosyalar, siteyi tekrar ziyaret ettiğinizde tercihlerinizi hatırlamak, oturum bilgilerinizi korumak ve site performansını analiz etmek için kullanılır.</p>

<h2>Kullanılan Çerez Türleri</h2>
<p><strong>Zorunlu Çerezler:</strong> Web sitesinin düzgün çalışması için gerekli olan çerezlerdir. Oturum yönetimi ve güvenlik işlevleri bu çerezler aracılığıyla sağlanır.</p>
<p><strong>Performans Çerezleri:</strong> Ziyaretçilerin siteyi nasıl kullandığına dair anonim bilgiler toplayan çerezlerdir. Site performansının iyileştirilmesi amacıyla kullanılır.</p>
<p><strong>İşlevsellik Çerezleri:</strong> Dil tercihi, tema seçimi gibi kişiselleştirme ayarlarınızı hatırlayan çerezlerdir.</p>

<h2>Çerez Yönetimi</h2>
<p>Tarayıcı ayarlarınızdan çerezleri kabul etmeyi, reddetmeyi veya çerez gönderildiğinde uyarı almayı tercih edebilirsiniz. Çerezleri devre dışı bırakmanız durumunda bazı site özelliklerinin düzgün çalışmayabileceğini hatırlatırız.</p>

<h2>Üçüncü Taraf Çerezleri</h2>
<p>Sitemizde analiz ve performans ölçümü amacıyla Google Analytics gibi üçüncü taraf hizmetlerinin çerezleri de kullanılabilmektedir. Bu çerezler, ilgili hizmet sağlayıcıların gizlilik politikalarına tabidir.</p>',
        ],
    ],
];

// Generate similar pages for other 3 domains with slight variations
$variations = [
    'lizabetgiris.site' => ['Lizabet', 'giriş ve erişim platformu', 'güncel adres bilgileri, erişim rehberleri ve hesap yönetimi konularında'],
    'lizabahis.site' => ['Lizabet', 'bahis platformu', 'spor bahisleri, canlı bahis ve kampanyalar konusunda'],
    'girislizabet.site' => ['Lizabet', 'bilgilendirme ve inceleme platformu', 'ödeme yöntemleri, kullanıcı deneyimleri ve platform değerlendirmesi konusunda'],
];

foreach ($variations as $domain => $v) {
    $brand = $v[0];
    $desc = $v[1];
    $focus = $v[2];

    $extraPages[$domain] = [
        [
            'slug' => 'hakkimizda',
            'title' => 'Hakkımızda',
            'meta_title' => "Hakkımızda | {$brand}",
            'meta_description' => "{$brand} hakkında bilgi. {$focus} hizmet veren güvenilir platform.",
            'sort_order' => 10,
            'content' => "<h2>{$brand} Hakkında</h2>
<p>{$brand}, çevrimiçi bahis ve casino sektöründe kullanıcılarına güvenilir ve kaliteli hizmet sunmayı hedefleyen bir {$desc}dur. Kullanıcı memnuniyetini ön planda tutarak sürekli gelişen bir platform olarak hizmet vermekteyiz.</p>

<h2>Hedeflerimiz</h2>
<p>Kullanıcılarımıza {$focus} en doğru ve güncel bilgileri sunmak öncelikli hedefimizdir. {$brand} olarak, şeffaf, güvenilir ve kullanıcı odaklı bir hizmet anlayışı benimsiyoruz.</p>

<h2>Neden {$brand}?</h2>
<p>{$brand}, güçlü teknik altyapısı ve deneyimli ekibiyle kullanıcılarına kesintisiz hizmet sunmaktadır. 7/24 canlı destek, hızlı işlem süreleri ve çeşitli bonus fırsatlarıyla sektörde fark yaratmaktayız.</p>
<p>SSL şifreleme ve gelişmiş güvenlik protokolleri ile kullanıcı verilerinin korunmasını sağlıyoruz. Kullanıcılarımızın güvenliği ve memnuniyeti her zaman en önemli önceliğimizdir.</p>",
        ],
        [
            'slug' => 'iletisim',
            'title' => 'İletişim',
            'meta_title' => "İletişim | {$brand}",
            'meta_description' => "{$brand} iletişim bilgileri. Canlı destek, Telegram ve sosyal medya kanallarından bize ulaşın.",
            'sort_order' => 11,
            'content' => "<h2>Bize Ulaşın</h2>
<p>{$brand} olarak kullanıcılarımızla iletişimi önemsiyoruz. Her türlü soru, öneri veya sorun için aşağıdaki kanallardan bize ulaşabilirsiniz.</p>

<h2>Canlı Destek</h2>
<p>7/24 aktif canlı destek hizmetimizle anında yardım alabilirsiniz. Web sitemizin sağ alt köşesindeki canlı destek butonuna tıklayarak destek ekibimize bağlanabilirsiniz.</p>

<h2>Telegram</h2>
<p>Güncel adres bilgileri, kampanya duyuruları ve özel fırsatlar için resmi Telegram kanalımızı takip edebilirsiniz. Telegram üzerinden de destek alabilirsiniz.</p>

<h2>Sosyal Medya</h2>
<p>{$brand}'i Instagram, X (Twitter), TikTok ve YouTube hesaplarımızdan takip ederek güncel gelişmelerden haberdar olabilirsiniz.</p>",
        ],
        [
            'slug' => 'gizlilik-politikasi',
            'title' => 'Gizlilik Politikası',
            'meta_title' => "Gizlilik Politikası | {$brand}",
            'meta_description' => "{$brand} gizlilik politikası. Kişisel verilerin korunması ve kullanıcı hakları.",
            'sort_order' => 97,
            'content' => "<h2>Gizlilik Politikası</h2>
<p>{$brand} olarak kullanıcılarımızın kişisel verilerinin korunmasına büyük önem veriyoruz. Bu politika, toplanan verilerin nasıl işlendiğini ve korunduğunu açıklamaktadır.</p>

<h2>Toplanan Bilgiler</h2>
<p>Hizmetlerimizi kullanırken ad-soyad, e-posta, telefon numarası, IP adresi ve cihaz bilgileri toplanabilmektedir. Bu bilgiler hesap yönetimi, kimlik doğrulama ve hizmet sunumu için kullanılmaktadır.</p>

<h2>Verilerin Korunması</h2>
<p>Tüm kullanıcı verileri SSL şifreleme ile korunmaktadır. {$brand}, veri güvenliği konusunda endüstri standartlarına uygun protokoller uygulamaktadır. Veriler üçüncü taraflarla izinsiz paylaşılmamaktadır.</p>

<h2>Kullanıcı Hakları</h2>
<p>Kişisel verilerinize erişim, düzeltme ve silme hakkına sahipsiniz. Bu haklarınızı kullanmak için destek ekibimizle iletişime geçebilirsiniz.</p>",
        ],
        [
            'slug' => 'cerez-politikasi',
            'title' => 'Çerez Politikası',
            'meta_title' => "Çerez Politikası | {$brand}",
            'meta_description' => "{$brand} çerez politikası. Web sitesinde kullanılan çerezler ve yönetimi hakkında bilgi.",
            'sort_order' => 98,
            'content' => "<h2>Çerez Politikası</h2>
<p>{$brand} web sitesi, kullanıcı deneyimini geliştirmek amacıyla çerezler kullanmaktadır.</p>

<h2>Çerez Türleri</h2>
<p><strong>Zorunlu Çerezler:</strong> Sitenin düzgün çalışması için gereklidir. Oturum yönetimi ve güvenlik işlevleri bu çerezlerle sağlanır.</p>
<p><strong>Performans Çerezleri:</strong> Anonim kullanım istatistikleri toplayarak site performansının iyileştirilmesine yardımcı olur.</p>
<p><strong>İşlevsellik Çerezleri:</strong> Dil tercihi ve kişiselleştirme ayarlarınızı hatırlar.</p>

<h2>Çerez Yönetimi</h2>
<p>Tarayıcı ayarlarınızdan çerezleri kabul etmeyi veya reddetmeyi seçebilirsiniz. Çerezlerin devre dışı bırakılması bazı site özelliklerini etkileyebilir.</p>

<h2>Analitik Çerezler</h2>
<p>Sitemizde Google Analytics gibi hizmetlerin çerezleri kullanılabilmektedir. Bu çerezler ilgili hizmet sağlayıcıların politikalarına tabidir.</p>",
        ],
    ];
}

// Insert extra pages
foreach ($extraPages as $domain => $pages) {
    $site = Site::where('domain', $domain)->firstOrFail();
    Config::set('database.connections.tenant.database', $site->db_name);
    DB::purge('tenant');
    DB::reconnect('tenant');

    echo "\n[PAGES] {$domain}\n";

    foreach ($pages as $page) {
        $exists = DB::connection('tenant')->table('pages')->where('slug', $page['slug'])->exists();
        if ($exists) {
            echo "  SKIP: {$page['slug']} (exists)\n";
            continue;
        }

        DB::connection('tenant')->table('pages')->insert([
            'slug' => $page['slug'],
            'title' => $page['title'],
            'content' => $page['content'],
            'meta_title' => $page['meta_title'],
            'meta_description' => $page['meta_description'],
            'meta_keywords' => null,
            'is_published' => true,
            'sort_order' => $page['sort_order'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "  OK: {$page['slug']}\n";
    }
}

echo "\n=== DONE ===\n";
