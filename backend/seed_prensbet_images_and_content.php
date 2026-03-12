<?php
/**
 * Prensbet All Sites: Add featured images to posts, inline images to homepages,
 * and add "prensbetgiris" article to prenssbet.org
 *
 * Usage: php artisan tinker --execute="require 'seed_prensbet_images_and_content.php';"
 */

use Illuminate\Support\Facades\DB;

$now = now();

// 20 page images for featured_image round-robin
$pageImages = [];
$nums = [1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21];
foreach ($nums as $n) {
    $pageImages[] = "/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-{$n}.jpg";
}

// Also include existing promo images for variety
$promoImages = [
    '/storage/uploads/promotions/promo-100hosgeldin.jpg',
    '/storage/uploads/promotions/promo-1kdeneme.jpg',
    '/storage/uploads/promotions/promo-aviator.jpg',
    '/storage/uploads/promotions/promo-100geriiade.jpg',
    '/storage/uploads/promotions/promo-30haftalik.jpg',
    '/storage/uploads/promotions/promo-kumbarapuan.jpg',
    '/storage/uploads/promotions/promo-prensvip.jpg',
    '/storage/uploads/promotions/promo-kriptoekhediyefs.jpg',
    '/storage/uploads/promotions/promo-genelbonuskurallari.jpg',
    '/storage/uploads/promotions/promo-kripto50.jpg',
];

$allImages = array_merge($pageImages, $promoImages);

// ─── PRENSBET TENANT DATABASES ───
$tenants = [
    'tenant_prensbet_me',
    'tenant_prensbetgiris_online',
    'tenant_prensbetgiris_site',
    'tenant_prensbetgirisonline_one',
    'tenant_prenssbet_com',
    'tenant_prenssbet_net',
    'tenant_prenssbet_org',
];

// ═══════════════════════════════════════
// PART 1: Assign featured_image to posts without one
// ═══════════════════════════════════════
foreach ($tenants as $db) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    $posts = DB::connection('tenant')->table('posts')
        ->where('is_published', 1)
        ->where(function($q) {
            $q->whereNull('featured_image')->orWhere('featured_image', '');
        })
        ->orderBy('id')
        ->pluck('id');

    if ($posts->isEmpty()) {
        echo "  {$db}: 0 posts need images\n";
        continue;
    }

    foreach ($posts as $idx => $postId) {
        $img = $allImages[$idx % count($allImages)];
        DB::connection('tenant')->table('posts')
            ->where('id', $postId)
            ->update(['featured_image' => $img, 'updated_at' => $now]);
    }
    echo "  {$db}: {$posts->count()} posts got featured_image\n";
}
echo "✓ Featured images assigned to all posts\n\n";

// ═══════════════════════════════════════
// PART 2: Add inline images to homepages (6 sites without them)
// ═══════════════════════════════════════
$sitesNeedingHomepageImages = [
    'tenant_prensbet_me',
    'tenant_prensbetgiris_online',
    'tenant_prensbetgiris_site',
    'tenant_prensbetgirisonline_one',
    'tenant_prenssbet_com',
    'tenant_prenssbet_net',
];

// Pick 5 different images for each homepage (different per site)
$homepageImageSets = [
    [1, 4, 7, 10, 13],   // prensbet.me
    [2, 6, 9, 12, 15],   // prensbetgiris.online
    [3, 8, 11, 14, 17],  // prensbetgiris.site
    [4, 9, 13, 16, 19],  // prensbetgirisonline.one
    [1, 7, 11, 15, 20],  // prenssbet.com
    [2, 8, 12, 16, 21],  // prenssbet.net
];

foreach ($sitesNeedingHomepageImages as $i => $db) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    $homepage = DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->first();

    if (!$homepage) {
        echo "  {$db}: no homepage found\n";
        continue;
    }

    $content = $homepage->content;

    // Check if already has images
    if (substr_count($content, '<img') >= 3) {
        echo "  {$db}: homepage already has images\n";
        continue;
    }

    // Build image HTML blocks to insert
    $imgNums = $homepageImageSets[$i];
    $imageBlocks = [];
    $titles = [
        'Prensbet Hoş Geldin Bonusu',
        'Prensbet Casino Oyunları',
        'Prensbet Canlı Bahis',
        'Prensbet Bonus Kampanyaları',
        'Prensbet VIP Ayrıcalıklar',
    ];

    foreach ($imgNums as $j => $num) {
        $src = "/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-{$num}.jpg";
        $alt = $titles[$j];
        $imageBlocks[] = '<img src="' . $src . '" alt="' . $alt . '" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">';
    }

    // Find H2 tags in content and insert images after every 2nd H2
    $h2Count = 0;
    $imgIdx = 0;
    $newContent = preg_replace_callback('/<\/h2>/i', function($match) use (&$h2Count, &$imgIdx, $imageBlocks) {
        $h2Count++;
        // Insert image after H2 #2, #4, #6, #8, #10
        if ($h2Count % 2 === 0 && $imgIdx < count($imageBlocks)) {
            $img = $imageBlocks[$imgIdx];
            $imgIdx++;
            return $match[0] . "\n" . $img;
        }
        return $match[0];
    }, $content);

    DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->update(['content' => $newContent, 'updated_at' => $now]);

    echo "  {$db}: {$imgIdx} images added to homepage\n";
}
echo "✓ Homepage inline images added\n\n";

// ═══════════════════════════════════════
// PART 3: Add "Prensbet Giriş" article to prenssbet.org
// ═══════════════════════════════════════
config(["database.connections.tenant.database" => "tenant_prenssbet_org"]);
DB::purge('tenant');
DB::reconnect('tenant');

$existingPost = DB::connection('tenant')->table('posts')
    ->where('slug', 'prensbetgiris-guncel-adres-2026')
    ->first();

if (!$existingPost) {
    // Get category id for guncel-giris
    $catId = DB::connection('tenant')->table('categories')
        ->where('slug', 'guncel-giris')
        ->value('id');

    DB::connection('tenant')->table('posts')->insert([
        'category_id' => $catId,
        'slug' => 'prensbetgiris-guncel-adres-2026',
        'title' => 'Prensbetgiris Güncel Giriş Adresi 2026 – Yeni Link ve Erişim',
        'excerpt' => 'Prensbetgiris güncel giriş adresi 2026, yeni link ve kesintisiz erişim rehberi.',
        'featured_image' => '/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-1.jpg',
        'meta_title' => 'Prensbetgiris Güncel Giriş 2026 | Yeni Adres ve Erişim Rehberi',
        'meta_description' => 'Prensbetgiris güncel giriş adresi 2026 ✓ Yeni link, DNS ayarları, VPN rehberi ve güvenli erişim yöntemleri.',
        'is_published' => 1,
        'published_at' => $now,
        'created_at' => $now,
        'updated_at' => $now,
        'content' => <<<'HTML'
<h2>Prensbetgiris Güncel Giriş Adresi 2026</h2>
<p>Prensbetgiris, Prensbet platformunun güncel giriş noktası olarak 2026 yılında da kullanıcılarına kesintisiz hizmet sunmaya devam etmektedir. Erişim engellerine karşı düzenli olarak alan adı güncellemesi yapan platform, en güncel adres bilgisini bu sayfada paylaşmaktadır.</p>

<img src="/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-6.jpg" alt="Prensbetgiris Güncel Giriş 2026" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">

<h2>Güncel Adres Bilgisi</h2>
<p>Prensbet'in şu anki güncel giriş adresi <strong>prenssbet.org</strong> olarak belirlenmiştir. BTK kararlarıyla zaman zaman alan adlarına erişim engellenebilmektedir. Bu durumda platform, kullanıcılarına kesintisiz hizmet sunabilmek adına yeni bir alan adı üzerinden yayın yapmaya devam eder. En güncel adres bilgisine Telegram kanalımız ve resmi sosyal medya hesaplarımızdan ulaşabilirsiniz.</p>

<h3>Giriş Yapma Adımları</h3>
<ol>
<li>Güncel Prensbetgiris adresine gidin: <strong>prenssbet.org</strong></li>
<li>Ana sayfada "Giriş Yap" butonuna tıklayın</li>
<li>Kullanıcı adınızı veya e-posta adresinizi girin</li>
<li>Şifrenizi yazıp giriş yapın</li>
<li>İki faktörlü doğrulama aktifse onay kodunu girin</li>
</ol>

<img src="/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-10.jpg" alt="Prensbet Giriş Adımları" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">

<h2>Erişim Sorunları ve Çözüm Yöntemleri</h2>
<p>Prensbetgiris adresine erişimde sorun yaşıyorsanız aşağıdaki yöntemleri deneyebilirsiniz:</p>

<h3>DNS Değişikliği</h3>
<p>İnternet servis sağlayıcınızın DNS ayarlarını değiştirerek erişim engellerini aşabilirsiniz. Google DNS (8.8.8.8 ve 8.8.4.4) veya Cloudflare DNS (1.1.1.1 ve 1.0.0.1) kullanmanız önerilir. Windows, macOS, Android ve iOS cihazlarınızda ağ ayarlarından DNS sunucu adreslerini değiştirebilirsiniz.</p>

<h3>VPN ile Erişim</h3>
<p>Güvenilir bir VPN uygulaması kullanarak Prensbetgiris'e erişim sağlayabilirsiniz. NordVPN, ExpressVPN, Surfshark veya ProtonVPN gibi köklü VPN hizmetleri hem güvenlik hem de hız açısından öne çıkmaktadır. Ücretsiz VPN'ler genellikle hız ve güvenlik açısından yetersiz kalabilir.</p>

<h3>Tarayıcı Önbellek Temizleme</h3>
<p>Tarayıcınızın cache ve çerez verilerini temizleyerek eski DNS kayıtlarının güncellenmesini sağlayabilirsiniz. Alternatif olarak farklı bir tarayıcı (Chrome, Firefox, Brave, Opera) deneyebilirsiniz.</p>

<img src="/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-14.jpg" alt="Prensbet VPN ve DNS Rehberi" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">

<h2>Prensbetgiris Güvenli Giriş Tavsiyeleri</h2>
<ul>
<li><strong>Resmi Adres:</strong> Yalnızca resmi kanallardan paylaşılan giriş adreslerini kullanın</li>
<li><strong>SSL Kontrolü:</strong> Adres çubuğunda kilit simgesinin olduğundan emin olun</li>
<li><strong>Güçlü Şifre:</strong> En az 8 karakter, büyük/küçük harf ve rakam içeren şifre kullanın</li>
<li><strong>2FA:</strong> İki faktörlü doğrulamayı aktif ederek hesap güvenliğinizi artırın</li>
<li><strong>Sahte Siteler:</strong> Prensbet adını kullanan sahte sitelere dikkat edin, yalnızca resmi adresleri kullanın</li>
</ul>

<h2>Prensbetgiris Mobil Erişim</h2>
<p>Prensbetgiris adresine mobil cihazlarınızdan da sorunsuz erişim sağlayabilirsiniz. Platform, responsive tasarımı sayesinde Android ve iOS cihazlarda tam uyumlu çalışmaktadır. Mobil tarayıcınızdan güncel adrese giderek "Ana Ekrana Ekle" seçeneği ile uygulama benzeri bir deneyim elde edebilirsiniz.</p>

<h2>Adres Değişikliği Bildirimleri</h2>
<p>Prensbetgiris adres değişikliklerinden anında haberdar olmak için şu yöntemleri kullanabilirsiniz:</p>
<ul>
<li><strong>Telegram Kanalı:</strong> En hızlı ve güvenilir bilgi kaynağı</li>
<li><strong>E-posta:</strong> Kayıtlı e-posta adresinize gönderilen bildirimler</li>
<li><strong>Sosyal Medya:</strong> Resmi hesaplardan yapılan duyurular</li>
<li><strong>Bu Sayfa:</strong> Her zaman güncel adres bilgisi burada yayınlanmaktadır</li>
</ul>

<img src="/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-18.jpg" alt="Prensbetgiris Telegram ve Bildirimler" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">

<h2>Prensbet 2026 Avantajları</h2>
<p>Prensbetgiris üzerinden platforma eriştiğinizde aşağıdaki avantajlardan yararlanabilirsiniz:</p>
<ul>
<li>1000TL deneme bonusu ile risksiz başlangıç</li>
<li>%100 hoş geldin bonusu ile ilk yatırımınızı katlayın</li>
<li>5000+ casino oyunu ve 30+ spor dalında bahis</li>
<li>Hızlı para yatırma ve çekme işlemleri</li>
<li>7/24 Türkçe canlı destek hizmeti</li>
<li>Mobil uyumlu modern tasarım</li>
</ul>

<h2>Sıkça Sorulan Sorular</h2>
<h3>Prensbetgiris adresi neden değişiyor?</h3>
<p>BTK kararlarıyla bazı alan adlarına erişim engellenebilmektedir. Platform, kullanıcılarına kesintisiz hizmet sunabilmek için yeni alan adı üzerinden yayın yapmaya devam eder.</p>

<h3>Prensbetgiris güvenilir mi?</h3>
<p>Evet, Prensbet Curaçao eGaming lisansına sahip güvenilir bir platformdur. SSL şifreleme ve 2FA ile kullanıcı güvenliği sağlanmaktadır.</p>

<h3>Hesabımı nasıl kurtarırım?</h3>
<p>Yeni adres üzerinden giriş yapmayı deneyin. Şifrenizi unuttuysanız "Şifremi Unuttum" bağlantısını kullanın. Sorun devam ederse canlı destek hattına başvurun.</p>
HTML
    ]);
    echo "✓ Prensbetgiris article added to prenssbet.org\n\n";
} else {
    echo "  prensbetgiris article already exists\n\n";
}

echo "✅ All prensbet sites updated!\n";
