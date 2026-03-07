<?php

/**
 * Kaya Casino SEO Seed Script
 *
 * Creates the site, sets up emerald theme, content_identity, categories,
 * footer links, social links, and content schedules.
 *
 * Usage:
 *   php artisan tinker --execute="$(tail -n +3 seed_kayacasino_seo.php)"
 */

use App\Models\Site;
use App\Models\Category;
use App\Models\FooterLink;
use App\Models\Post;
use App\Models\ContentSchedule;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

// ─── SITE DEFINITION ───

$domain = 'kayacasino.top';
$siteName = 'Kaya Casino';
$brandSlug = 'kaya-casino';

// Check if site already exists
$existingSite = Site::where('domain', $domain)->first();

if ($existingSite) {
    echo "Site already exists: {$domain} (id: {$existingSite->id})\n";
    echo "Updating settings...\n";
    $site = $existingSite;
} else {
    echo "Creating new site: {$domain}\n";

    $dbName = 'tenant_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($domain));

    $site = Site::create([
        'domain' => $domain,
        'name' => $siteName,
        'db_name' => $dbName,
        'is_active' => true,
        'primary_color' => '#3B82F6',
        'secondary_color' => '#D946EF',
        'header_bg_color' => '#0B1426',
        'noindex_mode' => false,
    ]);

    // Provision tenant database
    $tenantManager = app(TenantManager::class);
    $tenantManager->createTenantDatabase($site);

    echo "  ✓ Site created (id: {$site->id}, db: {$dbName})\n";
    echo "  ✓ Tenant database provisioned\n";
}

// ─── EMERALD THEME CUSTOM CSS ───

$customCss = <<<'CSS'
/* ===== KAYA CASINO — BLUE-PURPLE THEME ===== */

body {
  background: #0B1426 !important;
  color: #CBD5E1 !important;
}

/* CTA Bar */
.action-bar {
  background: linear-gradient(135deg, #0B1426 0%, #1E1B4B 50%, #0B1426 100%) !important;
  border-bottom-color: #D946EF !important;
}

@keyframes ctaPulseKaya {
  0%, 100% {
    box-shadow: 0 0 8px rgba(217, 70, 239, 0.6), 0 0 20px rgba(217, 70, 239, 0.3);
  }
  50% {
    box-shadow: 0 0 16px rgba(217, 70, 239, 0.9), 0 0 40px rgba(59, 130, 246, 0.5), 0 0 60px rgba(217, 70, 239, 0.2);
  }
}

.action-btn {
  background: linear-gradient(135deg, #D946EF, #A855F7, #7C3AED) !important;
  color: #fff !important;
  animation: ctaPulseKaya 2s ease-in-out infinite !important;
}

.action-btn:hover {
  box-shadow: 0 0 24px rgba(217, 70, 239, 1), 0 0 50px rgba(168, 85, 247, 0.7), 0 0 80px rgba(124, 58, 237, 0.3) !important;
}

/* Top Offers */
.top-offers-section {
  background: #0F172A !important;
  border-bottom-color: #1E293B !important;
}

.top-offers-marquee::before {
  background: linear-gradient(to right, #0F172A, transparent) !important;
}

.top-offers-marquee::after {
  background: linear-gradient(to left, #0F172A, transparent) !important;
}

.offer-card {
  background: #1E293B !important;
}

.offer-card:hover {
  background: #334155 !important;
}

/* Partners/Sponsors */
.partners-section {
  background: #0F172A !important;
  border-bottom-color: #1E293B !important;
}

.partners-cat-btn.active {
  background: linear-gradient(135deg, #3B82F6, #A855F7) !important;
  border-color: #3B82F6 !important;
  color: #fff !important;
}

.partner-card {
  background: #1E293B !important;
}

.partner-card:hover {
  background: #334155 !important;
}

.partner-card__promo {
  color: #A78BFA !important;
}

.partner-card__rating {
  color: #60A5FA !important;
}

.partners-contact {
  background: rgba(59, 130, 246, 0.1) !important;
}

.partners-contact a {
  color: #60A5FA !important;
}

/* Feature Cards */
.feature-cards-section {
  background: #0F172A !important;
  border-top-color: #1E293B !important;
}

.offer-grid-card {
  background: #1E293B !important;
  border-color: rgba(59, 130, 246, 0.1) !important;
}

.offer-grid-card:hover {
  background: #334155 !important;
}

.feature-cards-contact {
  background: rgba(59, 130, 246, 0.08) !important;
}

.feature-cards-contact a {
  color: #60A5FA !important;
}

/* Recent Posts */
.recent-posts-title {
  color: #E2E8F0 !important;
}

.recent-post-card {
  background: rgba(30, 41, 59, 0.85) !important;
  border-color: rgba(59, 130, 246, 0.12) !important;
}

.recent-post-card:hover {
  background: rgba(51, 65, 85, 0.95) !important;
}

.recent-post-card:hover .recent-post-card__title {
  color: #60A5FA !important;
}

.recent-posts-more-link {
  border-color: #A855F7 !important;
  color: #A855F7 !important;
}

.recent-posts-more-link:hover {
  background: linear-gradient(135deg, #A855F7, #D946EF) !important;
  border-color: #D946EF !important;
  color: #fff !important;
}

/* Footer */
.site-footer {
  background: #0F172A !important;
}

/* Service Cards */
.service-card {
  border-color: rgba(59, 130, 246, 0.12) !important;
}

.service-card:hover {
  border-color: #3B82F6 !important;
  background: rgba(59, 130, 246, 0.08) !important;
}

/* Contact Form */
.contact-form {
  border-color: rgba(59, 130, 246, 0.15) !important;
}

.contact-form-field input:focus,
.contact-form-field select:focus,
.contact-form-field textarea:focus {
  border-color: #3B82F6 !important;
}

.contact-form-field select option {
  background: #0F172A !important;
}

/* Info boxes in content */
.page-content .info-box {
  background: rgba(59, 130, 246, 0.08);
  border-left: 4px solid #3B82F6;
  padding: 16px;
  border-radius: 6px;
  margin: 16px 0;
}
CSS;

// ─── CONTENT IDENTITY ───

$contentIdentity = [
    'tone' => 'lüks ve sofistike',
    'audience' => 'premium casino deneyimi arayan oyuncular',
    'slogan' => 'Zümrüt kalitesinde casino deneyimi',
    'keywords' => ['premium', 'casino', 'canlı casino', 'slot', 'güvenilir', 'VIP deneyim'],
];

$contentPrompt = 'Sen Kaya Casino editörüsün. Lüks, sofistike ve profesyonel bir dil kullan. Hedef kitlen premium casino deneyimi arayan seçici oyuncular. Casino stratejileri, VIP programları, yüksek kaliteli oyun deneyimleri ve güvenilir platform avantajlarını detaylıca anlat. "Kaya Casino kalitesiyle" veya "Kaya Casino VIP deneyiminde" gibi marka referansları kullan. Domain: {{domain}}';

// ─── UPDATE SITE SETTINGS ───

$site->update([
    'primary_color' => '#3B82F6',
    'secondary_color' => '#D946EF',
    'header_bg_color' => '#0B1426',
    'logo_url' => '/storage/logos/kayacasino/logo.png',
    'favicon_url' => '/storage/favicons/kayacasino/favicon.ico',
    'content_identity' => $contentIdentity,
    'content_prompt_template' => $contentPrompt,
    'meta_title' => 'Kaya Casino 2026 – Premium Casino Deneyimi ve Güncel Giriş',
    'meta_description' => 'Kaya Casino ile zümrüt kalitesinde casino deneyimi. Canlı casino, slot oyunları, VIP bonuslar ve güvenli giriş rehberi.',
    'social_links' => [
        'telegram' => 'https://t.me/kayacasino',
        'instagram' => 'https://instagram.com/kayacasino',
        'x' => 'https://x.com/kayacasino',
    ],
    'custom_css' => $customCss,
    'noindex_mode' => false,
]);

echo "  ✓ Updated site settings (emerald theme, content_identity, social_links)\n";

// ─── SWITCH TO TENANT DB ───

$tenantDb = $site->db_name;
Config::set('database.connections.tenant.database', $tenantDb);
DB::connection('tenant')->reconnect();

// ─── CATEGORIES ───

$categories = [
    'erisim' => [
        'name' => 'Erişim ve Giriş',
        'description' => 'Platform erişimi, giriş yöntemleri ve güncel adres bilgileri',
        'meta_title' => 'Kaya Casino Giriş – Güncel Adres ve Erişim Rehberi',
        'meta_description' => 'Kaya Casino güncel giriş adresleri, erişim sorunları çözümleri, DNS ayarları ve VPN rehberleri.',
        'content' => '<h1>Kaya Casino Erişim ve Giriş Rehberleri</h1><p>Kaya Casino platformuna güvenli ve hızlı erişim sağlamak, kesintisiz bir deneyim için büyük önem taşımaktadır. Bu kategori altında, platforma erişim konusunda ihtiyaç duyacağınız tüm teknik bilgileri ve çözüm rehberlerini bulabilirsiniz.</p><h2>Güncel Giriş Adresi Takibi</h2><p>Online platformların alan adları zaman zaman değişiklik gösterebilmektedir. Kaya Casino güncel giriş adresine ulaşmak için resmi Telegram kanalını ve sosyal medya hesaplarını takip etmeniz önerilir. Güncel adres bilgilerine her zaman güvenilir kaynaklardan ulaşmanız, hesap güvenliğiniz açısından kritik öneme sahiptir.</p><h2>DNS Ayarları ile Erişim</h2><p>Erişim sorunlarının büyük çoğunluğu DNS ayarlarının güncellenmesiyle çözülebilmektedir. Google DNS (8.8.8.8 / 8.8.4.4) veya Cloudflare DNS (1.1.1.1 / 1.0.0.1) gibi alternatif DNS sunucuları kullanarak Kaya Casino platformuna sorunsuz erişim sağlayabilirsiniz. DNS değişikliği işlemi tüm cihazlarda birkaç dakika içinde tamamlanabilir.</p><h2>VPN ile Güvenli Bağlantı</h2><p>VPN uygulamaları, hem erişim engellerini aşmak hem de bağlantı güvenliğinizi artırmak için etkili bir yöntemdir. NordVPN, ExpressVPN veya Surfshark gibi güvenilir VPN servislerini kullanarak Kaya Casino platformuna şifreli ve anonim bir bağlantı kurabilirsiniz.</p><h2>Tarayıcı Uyumluluk Rehberi</h2><p>Chrome, Firefox, Safari ve Edge tarayıcılarının güncel sürümleri, Kaya Casino ile tam uyumlu çalışmaktadır. Tarayıcı önbelleğinin düzenli temizlenmesi ve çerez ayarlarının doğru yapılandırılması, sorunsuz bir deneyim için önemlidir.</p><h2>Yaygın Erişim Sorunları ve Çözümleri</h2><p>Bağlantı zaman aşımı, sayfa yüklenmeme hatası veya yavaş bağlantı gibi sorunlarla karşılaştığınızda, öncelikle internet bağlantınızı kontrol edin. Ardından DNS ayarlarınızı güncelleyin veya farklı bir tarayıcı deneyin. Sorun devam ederse VPN kullanımı en etkili çözüm yöntemidir.</p>',
        'sort_order' => 1,
    ],
    'bonus' => [
        'name' => 'Bonus ve Kampanyalar',
        'description' => 'VIP bonuslar, hoş geldin fırsatları ve özel kampanya detayları',
        'meta_title' => 'Kaya Casino Bonus – VIP Kampanyalar ve Fırsatlar',
        'meta_description' => 'Kaya Casino VIP hoş geldin bonusu, kayıp bonusu, free spin kampanyaları ve özel turnuva ödülleri.',
        'content' => '<h1>Kaya Casino Bonus ve Kampanya Rehberi</h1><p>Kaya Casino, premium kullanıcılarına yönelik kapsamlı bonus programıyla öne çıkmaktadır. Hoş geldin paketinden VIP sadakat programlarına kadar her seviyedeki oyuncuya özel fırsatlar sunulmaktadır.</p><h2>VIP Hoş Geldin Paketi</h2><p>Kaya Casino hoş geldin paketi, yeni üyelere ayrıcalıklı bir başlangıç sunmaktadır. İlk yatırıma özel yüksek oranlı bonus, free spin hediyesi ve düşük çevrim şartları ile oyuncular avantajlı bir şekilde platforma adım atmaktadır. Paket detayları ve güncel koşullar promosyonlar sayfasından takip edilebilir.</p><h2>Çevrim Şartları ve Bonus Koşulları</h2><p>Her bonusun kendine özgü çevrim gereksinimleri bulunmaktadır. Kaya Casino olarak adil çevrim şartları sunmaya özen gösterilmektedir. Slot oyunları genellikle %100 çevrim katkısı sağlarken, masa oyunları daha düşük oranlarda katkıda bulunabilir. Bonus almadan önce koşulları incelemeniz tavsiye edilir.</p><h2>Haftalık ve Aylık Kampanyalar</h2><p>Düzenli oyuncular için haftalık yatırım bonusları, aylık kayıp iadesi ve özel turnuva fırsatları sunulmaktadır. Bu kampanyalar belirli dönemlerde değişiklik gösterebilir, bu nedenle promosyonlar sayfasını düzenli takip etmeniz önerilir.</p><h2>VIP Sadakat Programı</h2><p>Kaya Casino VIP programı, düzenli oyuncuları ödüllendiren çok katmanlı bir yapıya sahiptir. Oynadıkça seviye atlayan üyeler, kişiye özel bonuslar, hızlı çekim avantajı, özel masa limitleri ve VIP müşteri temsilcisi gibi ayrıcalıklardan yararlanmaktadır.</p><h2>Arkadaş Davet Bonusu</h2><p>Arkadaşlarınızı Kaya Casino ailesine davet ederek bonus kazanabilirsiniz. Davet ettiğiniz kişinin ilk yatırımından sonra hem siz hem de arkadaşınız özel bonus avantajından yararlanırsınız.</p>',
        'sort_order' => 2,
    ],
    'mobil' => [
        'name' => 'Mobil Erişim',
        'description' => 'Mobil giriş, uygulama indirme ve mobil casino deneyimi',
        'meta_title' => 'Kaya Casino Mobil – Uygulama ve Mobil Giriş Rehberi',
        'meta_description' => 'Kaya Casino mobil giriş, Android ve iOS uygulama kurulumu, mobil casino deneyimi ve optimizasyon ipuçları.',
        'content' => '<h1>Kaya Casino Mobil Erişim Rehberi</h1><p>Kaya Casino mobil platformu, masaüstü deneyimine eş değer kalitede bir casino deneyimi sunmaktadır. Akıllı telefonunuz veya tabletiniz üzerinden tüm oyunlara, bonuslara ve hesap işlemlerine kolayca erişebilirsiniz.</p><h2>Mobil Tarayıcıdan Giriş</h2><p>Herhangi bir uygulama indirmeden, mobil tarayıcınız üzerinden Kaya Casino platformuna erişebilirsiniz. Responsive tasarımı sayesinde tüm içerikler mobil ekrana uyumlu şekilde görüntülenmektedir. Chrome, Safari veya Firefox mobil tarayıcılarının güncel sürümleri tam uyumluluk sağlar.</p><h2>Android APK Kurulumu</h2><p>Android cihazlar için optimize edilmiş Kaya Casino uygulamasını resmi siteden indirerek kurabilirsiniz. Cihaz ayarlarından bilinmeyen kaynakların kurulumuna izin verdikten sonra APK dosyasını çalıştırmanız yeterlidir. Uygulama, daha hızlı yükleme süreleri ve anlık bildirim desteği sunmaktadır.</p><h2>iOS Kullanıcıları İçin Rehber</h2><p>iPhone ve iPad kullanıcıları Safari tarayıcısı üzerinden Kaya Casino platformuna erişebilir. Ana ekrana kısayol ekleyerek uygulama benzeri bir deneyim elde edebilirsiniz. Safari üzerinden paylaş menüsünden "Ana Ekrana Ekle" seçeneğini kullanmanız yeterlidir.</p><h2>Mobil Casino Oyun Kalitesi</h2><p>Kaya Casino mobil platformu, HD kalitesinde canlı casino yayınları, sorunsuz slot animasyonları ve hızlı bahis yerleştirme imkanı sunmaktadır. Mobil optimizasyonlu arayüz sayesinde dokunmatik ekranlarla rahat kullanım mümkündür.</p><h2>Mobil Güvenlik ve Performans</h2><p>Mobil cihazınızda güvenli casino deneyimi için ekran kilidi kullanmak, otomatik giriş bilgilerini güvenli yönetmek ve güvenilir ağlara bağlanmak önemlidir. Düzenli önbellek temizliği ve uygulama güncellemeleri performansı artırır.</p>',
        'sort_order' => 3,
    ],
    'odeme' => [
        'name' => 'Ödeme Yöntemleri',
        'description' => 'Para yatırma, çekme yöntemleri ve ödeme rehberleri',
        'meta_title' => 'Kaya Casino Ödeme – Para Yatırma ve Çekme Rehberi',
        'meta_description' => 'Kaya Casino para yatırma ve çekme yöntemleri. Papara, kripto, banka havalesi ile hızlı işlem rehberi.',
        'content' => '<h1>Kaya Casino Ödeme Yöntemleri Rehberi</h1><p>Kaya Casino, kullanıcılarına hızlı, güvenli ve çeşitli ödeme seçenekleri sunmaktadır. Para yatırma ve çekme işlemlerinde şeffaf süreçler ve rekabetçi limitler ile premium bir finansal deneyim sağlanmaktadır.</p><h2>Papara ile Hızlı İşlemler</h2><p>Papara, Kaya Casino kullanıcıları arasında en popüler ödeme yöntemlerinden biridir. Anlık yatırım ve hızlı çekim imkanı sunması, kullanıcı deneyimini üst seviyeye taşımaktadır. Hesap eşleme işlemi tamamlandıktan sonra saniyeler içinde yatırım yapılabilmektedir.</p><h2>Kripto Para ile Gizlilik</h2><p>Bitcoin, USDT ve Ethereum ile yatırım yapmak, gizlilik ve hız avantajı sağlamaktadır. Kaya Casino kripto para transferlerini hızlı şekilde işleme almaktadır. Özellikle yüksek tutarlı işlemler için kripto para tercih edilebilir.</p><h2>Banka Havalesi ve EFT</h2><p>Geleneksel banka transferi yöntemleri de Kaya Casino platformunda desteklenmektedir. EFT ve havale işlemleri ile güvenli para transferi gerçekleştirebilirsiniz. İşlem süreleri banka ve saat dilimine göre değişiklik gösterebilir.</p><h2>Çekim Süreleri ve Limitler</h2><p>Kaya Casino çekim talepleri, VIP seviyenize bağlı olarak öncelikli işleme alınmaktadır. Standart çekim süreleri 1-12 saat arasında değişirken, VIP üyeler için bu süre önemli ölçüde kısalmaktadır. Minimum ve maksimum limitler ödeme yöntemine göre farklılık gösterir.</p><h2>Güvenli Ödeme Altyapısı</h2><p>Tüm finansal işlemler SSL şifreleme ile korunmaktadır. Kaya Casino, PCI DSS uyumlu ödeme altyapısı kullanarak kullanıcı bilgilerinin güvenliğini en üst düzeyde sağlamaktadır.</p>',
        'sort_order' => 4,
    ],
    'oyun' => [
        'name' => 'Oyun Rehberleri',
        'description' => 'Casino oyunları, slot stratejileri ve canlı casino ipuçları',
        'meta_title' => 'Kaya Casino Oyunlar – Casino ve Slot Rehberi',
        'meta_description' => 'Kaya Casino oyun rehberleri. Slot incelemeleri, canlı casino stratejileri, masa oyunları ipuçları ve RTP analizleri.',
        'content' => '<h1>Kaya Casino Oyun Rehberleri</h1><p>Kaya Casino, dünya standartlarında oyun sağlayıcılarıyla çalışarak premium bir casino deneyimi sunmaktadır. Yüzlerce slot oyunu, canlı masa deneyimleri ve özel oyun formatlarıyla her zevke hitap eden bir içerik yelpazesi mevcuttur.</p><h2>Slot Oyunları ve RTP Karşılaştırması</h2><p>Kaya Casino slot koleksiyonu, Pragmatic Play, NetEnt, Microgaming ve Play n GO gibi önde gelen sağlayıcıların en popüler oyunlarını barındırmaktadır. Her slotun RTP oranı, volatilite seviyesi ve bonus özellikleri detaylıca incelenmiştir. Yüksek RTP li oyunlar, uzun vadede daha avantajlı geri ödeme potansiyeli sunmaktadır.</p><h2>Canlı Casino Masaları</h2><p>HD kalitesinde canlı yayınlarla sunulan rulet, blackjack, baccarat ve poker masaları, gerçek casino atmosferini evinize taşımaktadır. Evolution Gaming ve Pragmatic Play Live gibi premium sağlayıcıların masalarında profesyonel krupiyeler eşliğinde oynayabilirsiniz.</p><h2>Masa Oyunları Stratejileri</h2><p>Blackjack temel strateji tablosu, baccarat betting sistemleri ve poker el sıralaması gibi konularda detaylı rehberlerimizi inceleyerek oyun becerilerinizi geliştirebilirsiniz. Strateji bilgisi, özellikle masa oyunlarında kazanma olasılığınızı önemli ölçüde artırabilir.</p><h2>Game Show ve Özel Oyunlar</h2><p>Crazy Time, Monopoly Live, Dream Catcher ve Lightning Dice gibi game show formatındaki oyunlar, eğlence ve kazancı birleştiren popüler seçeneklerdir. Bu oyunlar özellikle interaktif yapıları ve yüksek çarpan potansiyelleriyle dikkat çekmektedir.</p><h2>Sorumlu Oyun Prensipleri</h2><p>Kaya Casino, sorumlu oyun ilkelerine büyük önem vermektedir. Bütçe belirleme, oturum süresi sınırlama ve kendi kendini dışlama gibi araçlar kullanılarak sağlıklı bir oyun deneyimi desteklenmektedir.</p>',
        'sort_order' => 5,
    ],
    'guvenlik' => [
        'name' => 'Güvenlik',
        'description' => 'Hesap güvenliği, lisans bilgileri ve platform güvenilirliği',
        'meta_title' => 'Kaya Casino Güvenlik – Hesap Koruma ve Lisans Bilgileri',
        'meta_description' => 'Kaya Casino güvenlik rehberi. 2FA kurulumu, hesap koruma, lisans bilgileri ve güvenilirlik analizi.',
        'content' => '<h1>Kaya Casino Güvenlik Rehberi</h1><p>Kaya Casino, kullanıcı güvenliğini en öncelikli konu olarak ele almaktadır. Gelişmiş şifreleme teknolojileri, çok katmanlı doğrulama sistemleri ve uluslararası lisanslar ile güvenilir bir platform deneyimi sunulmaktadır.</p><h2>Hesap Güvenliği Temelleri</h2><p>Güçlü ve benzersiz bir şifre kullanmak, hesap güvenliğinizin ilk savunma hattıdır. Minimum 12 karakter uzunluğunda, büyük-küçük harf, rakam ve özel karakter içeren şifreler önerilmektedir. Aynı şifreyi birden fazla platformda kullanmaktan kaçının.</p><h2>İki Faktörlü Doğrulama (2FA)</h2><p>Kaya Casino, Google Authenticator ve SMS tabanlı iki faktörlü doğrulama desteği sunmaktadır. 2FA aktivasyonu, hesabınıza yetkisiz erişim riskini büyük ölçüde azaltmaktadır. Hesap ayarlarından 2FA kurulumunu birkaç dakika içinde tamamlayabilirsiniz.</p><h2>Lisans ve Regülasyon</h2><p>Kaya Casino, uluslararası oyun otoritelerinin belirlediği standartlara uygun şekilde faaliyet göstermektedir. Düzenli denetimler ve adil oyun testleri ile platform güvenilirliği sürekli olarak doğrulanmaktadır. RNG sertifikaları, oyun sonuçlarının tamamen rastgele olduğunu garanti etmektedir.</p><h2>SSL Şifreleme ve Veri Koruma</h2><p>Tüm veri transferleri 256-bit SSL şifreleme ile korunmaktadır. Kişisel bilgileriniz ve finansal verileriniz, endüstri standardı güvenlik protokolleri ile işlenmekte ve saklanmaktadır. KVKK uyumluluğu çerçevesinde veri gizliliğiniz garanti altındadır.</p><h2>Dolandırıcılık Önleme</h2><p>Sahte siteler ve phishing saldırılarına karşı dikkatli olunmalıdır. Her zaman URL çubuğundaki alan adını kontrol edin ve yalnızca resmi kaynaklar üzerinden giriş yapın. Şüpheli durumlarda derhal müşteri hizmetleriyle iletişime geçmeniz önerilir.</p>',
        'sort_order' => 6,
    ],
    'genel' => [
        'name' => 'Genel Bilgiler',
        'description' => 'Platform incelemeleri, VIP rehberler ve güncel gelişmeler',
        'meta_title' => 'Kaya Casino Genel Bilgiler – Platform Rehberi',
        'meta_description' => 'Kaya Casino platform rehberi. Kayıt süreci, VIP avantajları, müşteri hizmetleri ve kullanıcı deneyimleri.',
        'content' => '<h1>Kaya Casino Genel Bilgiler ve Platform Rehberi</h1><p>Kaya Casino hakkında kapsamlı bilgiler, kullanıcı rehberleri ve platform değerlendirmeleri bu kategori altında yer almaktadır. Yeni başlayan üyelerden deneyimli oyunculara kadar herkes için faydalı içerikler sunulmaktadır.</p><h2>Platform Genel Değerlendirmesi</h2><p>Kaya Casino, modern arayüzü, geniş oyun çeşitliliği ve premium müşteri hizmetleri ile sektörde öne çıkan bir platformdur. Kullanıcı dostu tasarımı ve hızlı yükleme süreleri ile konforlu bir deneyim sunmaktadır.</p><h2>Kayıt ve İlk Adımlar</h2><p>Kaya Casino üyeliği birkaç dakika içinde tamamlanabilen basit bir süreçtir. Kayıt formunu doldurduktan sonra hesap doğrulama işlemi ile üyeliğiniz aktif hale gelmektedir. İlk yatırımınızla birlikte hoş geldin bonusundan yararlanabilirsiniz.</p><h2>VIP Avantajları</h2><p>VIP üyeler; kişiye özel bonuslar, hızlı çekim, yüksek limit ve özel müşteri temsilcisi gibi ayrıcalıklardan yararlanmaktadır. VIP seviyesi, düzenli oyun aktivitenize bağlı olarak otomatik olarak yükselmektedir.</p><h2>Müşteri Hizmetleri</h2><p>7/24 canlı destek, e-posta ve Telegram üzerinden müşteri hizmetlerine ulaşabilirsiniz. Profesyonel destek ekibi, sorularınızı hızlı ve etkili şekilde yanıtlamaktadır.</p><h2>Kullanıcı Görüşleri</h2><p>Gerçek kullanıcı deneyimleri ve geri bildirimleri, Kaya Casino hakkında objektif bir değerlendirme yapmanızı sağlamaktadır. Platform sürekli olarak kullanıcı geri bildirimlerini değerlendirmekte ve hizmet kalitesini artırmaktadır.</p>',
        'sort_order' => 7,
    ],
];

// Category → Post slug patterns
$categorySlugPatterns = [
    'erisim' => ['giris', 'adres', 'vpn', 'dns', 'erisim', 'ayna', 'mirror'],
    'bonus' => ['bonus', 'kampanya', 'promosyon', 'free-spin', 'kayip', 'cashback', 'davet', 'yatirim-bonusu'],
    'mobil' => ['mobil', 'android', 'ios', 'apk', 'uygulama'],
    'odeme' => ['papara', 'kripto', 'banka', 'havale', 'yatirim', 'cekim', 'odeme', 'para'],
    'oyun' => ['casino', 'slot', 'bahis', 'canli', 'poker', 'rulet', 'blackjack', 'sanal'],
    'guvenlik' => ['guvenlik', 'guvenilir', 'lisans', '2fa', 'dogrulama', 'sifre'],
    'genel' => ['uyelik', 'kayit', 'musteri', 'yorum', 'avantaj', 'dezavantaj', 'inceleme'],
];

$categoryMap = [];
foreach ($categories as $slug => $catData) {
    $existing = Category::on('tenant')->where('slug', $slug)->first();
    if ($existing) {
        $existing->update([
            'name' => $catData['name'],
            'description' => $catData['description'],
            'content' => $catData['content'],
            'meta_title' => $catData['meta_title'],
            'meta_description' => $catData['meta_description'],
            'sort_order' => $catData['sort_order'],
            'is_active' => true,
        ]);
        $categoryMap[$slug] = $existing->id;
    } else {
        $cat = Category::on('tenant')->create([
            'slug' => $slug,
            'name' => $catData['name'],
            'description' => $catData['description'],
            'content' => $catData['content'],
            'meta_title' => $catData['meta_title'],
            'meta_description' => $catData['meta_description'],
            'sort_order' => $catData['sort_order'],
            'is_active' => true,
        ]);
        $categoryMap[$slug] = $cat->id;
    }
}
echo "  ✓ Created/updated " . count($categories) . " categories\n";

// Assign categories to existing posts
$posts = Post::on('tenant')->whereNull('category_id')->get();
$assigned = 0;
foreach ($posts as $post) {
    $postSlug = $post->slug;
    $matchedCategory = null;
    foreach ($categorySlugPatterns as $catSlug => $patterns) {
        foreach ($patterns as $pattern) {
            if (Str::contains($postSlug, $pattern)) {
                $matchedCategory = $catSlug;
                break 2;
            }
        }
    }
    if ($matchedCategory && isset($categoryMap[$matchedCategory])) {
        $post->update(['category_id' => $categoryMap[$matchedCategory]]);
        $assigned++;
    } elseif (isset($categoryMap['genel'])) {
        $post->update(['category_id' => $categoryMap['genel']]);
        $assigned++;
    }
}
echo "  ✓ Assigned categories to {$assigned} posts\n";

// ─── FOOTER LINKS ───

$footerLinks = [
    ['link_text' => 'Kaya Casino Giriş', 'link_url' => '/', 'sort_order' => 1],
    ['link_text' => 'Casino Oyunları', 'link_url' => '/kaya-casino-casino', 'sort_order' => 2],
    ['link_text' => 'Canlı Casino', 'link_url' => '/kaya-casino-canli-casino', 'sort_order' => 3],
    ['link_text' => 'Bonus', 'link_url' => '/kaya-casino-bonus', 'sort_order' => 4],
    ['link_text' => 'Kayıt Ol', 'link_url' => '/kaya-casino-kayit', 'sort_order' => 5],
    ['link_text' => 'Blog', 'link_url' => '/blog', 'sort_order' => 6],
];

$existingFooterCount = FooterLink::on('tenant')->count();
if ($existingFooterCount === 0) {
    foreach ($footerLinks as $link) {
        FooterLink::on('tenant')->create([
            'link_text' => $link['link_text'],
            'link_url' => $link['link_url'],
            'sort_order' => $link['sort_order'],
            'is_active' => true,
        ]);
    }
    echo "  ✓ Created " . count($footerLinks) . " footer links\n";
} else {
    echo "  ⊘ Footer links already exist ({$existingFooterCount}), skipping\n";
}

// ─── CONTENT SCHEDULE ───

$existingSchedule = ContentSchedule::where('site_id', $site->id)->first();
if ($existingSchedule) {
    $existingSchedule->update([
        'content_type' => 'daily_post',
        'frequency' => 'custom',
        'interval_hours' => 72,
        'is_active' => true,
    ]);
    echo "  ✓ Updated content schedule\n";
} else {
    ContentSchedule::create([
        'site_id' => $site->id,
        'content_type' => 'daily_post',
        'frequency' => 'custom',
        'run_at' => '09:00',
        'interval_hours' => 72,
        'is_active' => true,
        'next_run_at' => now()->addHours(72),
    ]);
    echo "  ✓ Created content schedule (every 72h)\n";
}

echo "\n=== Kaya Casino Seed Complete ===\n";
echo "\nNext steps:\n";
echo "  1. Set up domain DNS: php artisan domain:setup kayacasino.top (or via admin panel)\n";
echo "  2. Generate content: php artisan content:generate-missing --site=kayacasino.top\n";
echo "  3. Add logo: Upload logo via admin panel → Sites → kayacasino.top\n";
echo "  4. Rebuild frontend: npm run build && pm2 restart cms-frontend\n";
