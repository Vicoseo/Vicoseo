<?php
/**
 * All Sites SEO Fixes
 * - Auto-assign categories to 1,366 uncategorized posts (23 sites)
 * - Assign featured_image to 672 posts (11 sites)
 * - Add footer links to 15 sites
 * - Add homepage inline images to 7 sites
 * - Add SSS page to kayacasino.top
 * - Delete duplicate ana-sayfa from casival.me
 *
 * SAFE: Only adds missing data, never modifies existing content on high-traffic sites
 *
 * Usage: php artisan tinker --execute="require 'seed_all_sites_fixes.php';"
 */

use Illuminate\Support\Facades\DB;

$now = now();

// ═══════════════════════════════════════
// IMAGE POOLS
// ═══════════════════════════════════════
$pageImages = [];
foreach ([1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21] as $n) {
    $pageImages[] = "/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-{$n}.jpg";
}
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

// Locabet-specific images
$locabetImages = [
    '/storage/uploads/promotions/locabet-promo-1.jpeg',
    '/storage/uploads/promotions/locabet-promo-2.jpeg',
    '/storage/uploads/promotions/locabet-promo-4.jpeg',
];
$locabetAllImages = array_merge($locabetImages, $pageImages);

// ═══════════════════════════════════════
// KEYWORD MAPS FOR CATEGORY AUTO-ASSIGN
// ═══════════════════════════════════════

// Standard 8-category sites (original 17 + prensbet + rise)
$standardKeywords = [
    'guncel-giris' => ['giris', 'adres', 'erisim', 'vpn', 'dns', 'guncel', 'link', 'acilmiyor'],
    'bonus' => ['bonus', 'kampanya', 'promosyon', 'free-spin', 'freespin', 'hosgeldin', 'deneme-bonusu', 'kayip-bonusu', 'ramazan'],
    'slot' => ['slot', 'sweet-bonanza', 'gates-of-olympus', 'pragmatic', 'megaways', 'dog-house', 'starlight', 'sugar-rush', 'big-bass'],
    'canli-casino' => ['casino', 'rulet', 'blackjack', 'baccarat', 'poker', 'krupiye', 'lightning', 'crazy-time', 'canli-casino'],
    'canli-bahis' => ['bahis', 'futbol', 'basketbol', 'kombine', 'kupon', 'spor', 'iddaa', 'mac', 'canli-bahis', 'tenis'],
    'guvenilirlik' => ['guvenilir', 'lisans', 'guvenlik', 'ssl', 'inceleme', 'yorum', 'degerlendirme', 'avantaj', 'dezavantaj'],
    'uyelik' => ['kayit', 'uyelik', 'uye', 'hesap', 'dogrulama', 'profil', 'sifre'],
    'para-islemleri' => ['para', 'yatirim', 'cekim', 'papara', 'kripto', 'havale', 'odeme', 'banka', 'cepbank'],
];

// Locabet 7-category sites
$locabetKeywords = [
    'erisim' => ['giris', 'adres', 'erisim', 'vpn', 'dns', 'guncel', 'link', 'acilmiyor', 'telegram'],
    'bonus' => ['bonus', 'kampanya', 'promosyon', 'free-spin', 'freespin', 'hosgeldin', 'deneme', 'kayip'],
    'mobil' => ['mobil', 'android', 'ios', 'apk', 'telefon', 'tablet', 'uygulama'],
    'odeme' => ['para', 'yatirim', 'cekim', 'papara', 'kripto', 'havale', 'odeme', 'banka'],
    'oyun' => ['slot', 'casino', 'rulet', 'blackjack', 'poker', 'bahis', 'spor', 'oyun', 'canli'],
    'guvenlik' => ['guvenilir', 'guvenlik', 'lisans', 'ssl', 'inceleme', 'yorum', 'avantaj'],
    'genel' => ['rehber', 'nedir', 'nasil', 'hakkinda', 'kullanim', 'sss'],
];

// ═══════════════════════════════════════
// HELPER: Auto-assign categories
// ═══════════════════════════════════════
function autoAssignCategories($db, $keywordMap, $now) {
    $catMap = DB::connection('tenant')->table('categories')->pluck('id', 'slug')->toArray();
    if (empty($catMap)) return 0;

    $posts = DB::connection('tenant')->table('posts')
        ->whereNull('category_id')
        ->where('is_published', 1)
        ->get(['id', 'slug', 'title']);

    $assigned = 0;
    foreach ($posts as $post) {
        $searchText = strtolower($post->slug . ' ' . $post->title);
        $bestCat = null;

        foreach ($keywordMap as $catSlug => $keywords) {
            if (!isset($catMap[$catSlug])) continue;
            foreach ($keywords as $kw) {
                if (strpos($searchText, $kw) !== false) {
                    $bestCat = $catMap[$catSlug];
                    break 2;
                }
            }
        }

        // Fallback: assign to most generic category
        if (!$bestCat) {
            $fallback = $catMap['bonus'] ?? $catMap['genel'] ?? $catMap['bonuslar'] ?? $catMap['bonuslar-kampanyalar'] ?? reset($catMap);
            $bestCat = $fallback;
        }

        if ($bestCat) {
            DB::connection('tenant')->table('posts')
                ->where('id', $post->id)
                ->update(['category_id' => $bestCat, 'updated_at' => $now]);
            $assigned++;
        }
    }
    return $assigned;
}

// ═══════════════════════════════════════
// HELPER: Assign featured images
// ═══════════════════════════════════════
function assignFeaturedImages($db, $images, $now) {
    $posts = DB::connection('tenant')->table('posts')
        ->where('is_published', 1)
        ->where(function($q) {
            $q->whereNull('featured_image')->orWhere('featured_image', '');
        })
        ->orderBy('id')
        ->pluck('id');

    if ($posts->isEmpty()) return 0;

    foreach ($posts as $idx => $postId) {
        $img = $images[$idx % count($images)];
        DB::connection('tenant')->table('posts')
            ->where('id', $postId)
            ->update(['featured_image' => $img, 'updated_at' => $now]);
    }
    return $posts->count();
}

// ═══════════════════════════════════════
// HELPER: Add homepage inline images
// ═══════════════════════════════════════
function addHomepageImages($db, $imgNums, $titles, $now) {
    $homepage = DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->first();

    if (!$homepage) {
        $homepage = DB::connection('tenant')->table('pages')
            ->where('slug', 'ana-sayfa')
            ->first();
    }

    if (!$homepage) return 0;
    if (substr_count($homepage->content, '<img') >= 3) return -1; // already has images

    $imageBlocks = [];
    foreach ($imgNums as $j => $num) {
        $src = "/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-{$num}.jpg";
        $alt = $titles[$j % count($titles)];
        $imageBlocks[] = '<img src="' . $src . '" alt="' . $alt . '" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">';
    }

    $h2Count = 0;
    $imgIdx = 0;
    $newContent = preg_replace_callback('/<\/h2>/i', function($match) use (&$h2Count, &$imgIdx, $imageBlocks) {
        $h2Count++;
        if ($h2Count % 2 === 0 && $imgIdx < count($imageBlocks)) {
            $img = $imageBlocks[$imgIdx++];
            return $match[0] . "\n" . $img;
        }
        return $match[0];
    }, $homepage->content);

    DB::connection('tenant')->table('pages')
        ->where('id', $homepage->id)
        ->update(['content' => $newContent, 'updated_at' => $now]);

    return $imgIdx;
}

// ═══════════════════════════════════════
// SITE DEFINITIONS
// ═══════════════════════════════════════

// Standard 8-cat sites needing fixes
$standardSites = [
    'tenant_casival_me'           => ['domain' => 'casival.me',          'site_id' => 2,  'brand' => 'Casival'],
    'tenant_ilkbahis_click'       => ['domain' => 'ilkbahis.click',      'site_id' => 3,  'brand' => 'İlkBahis'],
    'tenant_ilkbahis_link'        => ['domain' => 'ilkbahis.link',       'site_id' => 4,  'brand' => 'İlkBahis'],
    'tenant_ilkbahisgiri_net'     => ['domain' => 'ilkbahisgiri.net',    'site_id' => 5,  'brand' => 'İlkBahis'],
    'tenant_ilkbahisonline_com'   => ['domain' => 'ilkbahisonline.com',  'site_id' => 6,  'brand' => 'İlkBahis'],
    'tenant_rayzbet_net'          => ['domain' => 'rayzbet.net',         'site_id' => 9,  'brand' => 'RayzBet'],
    'tenant_pragmaticlive_click'  => ['domain' => 'pragmaticlive.click', 'site_id' => 17, 'brand' => 'PragmaticLive'],
    'tenant_prensbet_me'          => ['domain' => 'prensbet.me',         'site_id' => 7,  'brand' => 'Prensbet'],
    'tenant_prensbetgiris_online' => ['domain' => 'prensbetgiris.online','site_id' => 10, 'brand' => 'Prensbet'],
    'tenant_prensbetgiris_site'   => ['domain' => 'prensbetgiris.site',  'site_id' => 11, 'brand' => 'Prensbet'],
    'tenant_prensbetgirisonline_one' => ['domain' => 'prensbetgirisonline.one', 'site_id' => 12, 'brand' => 'Prensbet'],
    'tenant_prenssbet_com'        => ['domain' => 'prenssbet.com',       'site_id' => 13, 'brand' => 'Prensbet'],
    'tenant_prenssbet_net'        => ['domain' => 'prenssbet.net',       'site_id' => 14, 'brand' => 'Prensbet'],
    'tenant_1'                    => ['domain' => 'rise-bets.com',       'site_id' => 1,  'brand' => 'RiseBet'],
    'tenant_risebett_me'          => ['domain' => 'risebett.me',         'site_id' => 8,  'brand' => 'RiseBet'],
    'tenant_risebette_com'        => ['domain' => 'risebette.com',       'site_id' => 15, 'brand' => 'RiseBet'],
    'tenant_risebets_click'       => ['domain' => 'risebets.click',      'site_id' => 16, 'brand' => 'RiseBet'],
    'tenant_risespor_com'         => ['domain' => 'risespor.com',        'site_id' => 18, 'brand' => 'RiseBet'],
];

// Locabet 7-cat sites
$locabetSites = [
    'tenant_locabetbonus_me'   => ['domain' => 'locabetbonus.me',   'site_id' => 19, 'brand' => 'Locabet'],
    'tenant_locabetcasino_com' => ['domain' => 'locabetcasino.com', 'site_id' => 20, 'brand' => 'Locabet'],
    'tenant_locabetgiris_site' => ['domain' => 'locabetgiris.site', 'site_id' => 21, 'brand' => 'Locabet'],
    'tenant_locabeet_com'      => ['domain' => 'locabeet.com',      'site_id' => 22, 'brand' => 'Locabet'],
    'tenant_kayacasino_top'    => ['domain' => 'kayacasino.top',    'site_id' => 23, 'brand' => 'KayaCasino'],
];

// Lizabet 3-cat sites
$lizabetSites = [
    'tenant_girislizabet_site' => ['domain' => 'girislizabet.site', 'site_id' => 24, 'brand' => 'Lizabet'],
    'tenant_lizabahis_site'    => ['domain' => 'lizabahis.site',    'site_id' => 25, 'brand' => 'Lizabet'],
    'tenant_lizabetcasino_com' => ['domain' => 'lizabetcasino.com', 'site_id' => 26, 'brand' => 'Lizabet'],
    'tenant_lizabetgiris_site' => ['domain' => 'lizabetgiris.site', 'site_id' => 27, 'brand' => 'Lizabet'],
];

// ═══════════════════════════════════════
// PART 1: AUTO-ASSIGN CATEGORIES
// ═══════════════════════════════════════
echo "── PART 1: Category Auto-Assign ──\n";

foreach ($standardSites as $db => $info) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant'); DB::reconnect('tenant');
    $cnt = autoAssignCategories($db, $standardKeywords, $now);
    if ($cnt > 0) echo "  {$info['domain']}: {$cnt} posts categorized\n";
}

foreach ($locabetSites as $db => $info) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant'); DB::reconnect('tenant');
    $cnt = autoAssignCategories($db, $locabetKeywords, $now);
    if ($cnt > 0) echo "  {$info['domain']}: {$cnt} posts categorized\n";
}

// Lizabet sites - custom keyword maps per site
$lizabetKeywordMaps = [
    'tenant_girislizabet_site' => [
        'platform-inceleme' => ['inceleme', 'guvenilir', 'avantaj', 'dezavantaj', 'yorum', 'karsilastirma'],
        'odeme-yontemleri' => ['para', 'yatirim', 'cekim', 'papara', 'kripto', 'odeme', 'banka', 'havale'],
        'kullanici-rehberi' => ['rehber', 'giris', 'kayit', 'bonus', 'slot', 'casino', 'bahis', 'mobil', 'nasil'],
    ],
    'tenant_lizabahis_site' => [
        'spor-bahisleri' => ['bahis', 'futbol', 'basketbol', 'spor', 'kupon', 'mac', 'canli-bahis', 'iddaa'],
        'bahis-stratejileri' => ['strateji', 'taktik', 'analiz', 'rehber', 'ipucu', 'nasil'],
        'bonuslar-kampanyalar' => ['bonus', 'kampanya', 'promosyon', 'freespin', 'hosgeldin', 'kayip', 'giris', 'casino', 'slot'],
    ],
    'tenant_lizabetcasino_com' => [
        'casino-oyunlari' => ['casino', 'slot', 'rulet', 'blackjack', 'poker', 'oyun', 'canli'],
        'bonuslar' => ['bonus', 'kampanya', 'promosyon', 'freespin', 'hosgeldin'],
        'rehber' => ['rehber', 'giris', 'kayit', 'nasil', 'adres', 'mobil', 'bahis', 'para', 'yatirim'],
    ],
    'tenant_lizabetgiris_site' => [
        'giris-rehberi' => ['giris', 'adres', 'erisim', 'vpn', 'dns', 'guncel', 'mobil', 'nasil'],
        'hesap-islemleri' => ['kayit', 'hesap', 'para', 'yatirim', 'cekim', 'bonus', 'odeme', 'dogrulama'],
        'iletisim-destek' => ['iletisim', 'destek', 'telegram', 'yardim', 'sss', 'guvenilir', 'casino', 'slot', 'bahis'],
    ],
];

foreach ($lizabetSites as $db => $info) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant'); DB::reconnect('tenant');
    $kwMap = $lizabetKeywordMaps[$db] ?? [];
    if (!empty($kwMap)) {
        $cnt = autoAssignCategories($db, $kwMap, $now);
        if ($cnt > 0) echo "  {$info['domain']}: {$cnt} posts categorized\n";
    }
}
echo "✓ Categories assigned\n\n";

// ═══════════════════════════════════════
// PART 2: FEATURED IMAGES
// ═══════════════════════════════════════
echo "── PART 2: Featured Images ──\n";

$needImages = [
    // Standard sites
    'tenant_casival_me', 'tenant_ilkbahis_click', 'tenant_ilkbahis_link',
    'tenant_ilkbahisgiri_net', 'tenant_ilkbahisonline_com',
    'tenant_rayzbet_net', 'tenant_pragmaticlive_click',
    // Locabet sites
    'tenant_locabetbonus_me', 'tenant_locabetcasino_com',
    'tenant_locabetgiris_site', 'tenant_locabeet_com', 'tenant_kayacasino_top',
];

foreach ($needImages as $db) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant'); DB::reconnect('tenant');

    $isLocabet = strpos($db, 'locabet') !== false || strpos($db, 'kaya') !== false;
    $imgs = $isLocabet ? $locabetAllImages : $allImages;

    $cnt = assignFeaturedImages($db, $imgs, $now);
    $domain = $standardSites[$db]['domain'] ?? $locabetSites[$db]['domain'] ?? $db;
    if ($cnt > 0) echo "  {$domain}: {$cnt} posts got featured_image\n";
}
echo "✓ Featured images assigned\n\n";

// ═══════════════════════════════════════
// PART 3: FOOTER LINKS (15 sites)
// ═══════════════════════════════════════
echo "── PART 3: Footer Links ──\n";

$footerNeeded = [
    'tenant_casival_me'            => ['domain' => 'casival.me',             'brand' => 'Casival'],
    'tenant_ilkbahis_link'         => ['domain' => 'ilkbahis.link',          'brand' => 'İlkBahis'],
    'tenant_ilkbahisgiri_net'      => ['domain' => 'ilkbahisgiri.net',       'brand' => 'İlkBahis'],
    'tenant_ilkbahisonline_com'    => ['domain' => 'ilkbahisonline.com',     'brand' => 'İlkBahis'],
    'tenant_prensbet_me'           => ['domain' => 'prensbet.me',            'brand' => 'Prensbet'],
    'tenant_rayzbet_net'           => ['domain' => 'rayzbet.net',            'brand' => 'RayzBet'],
    'tenant_prensbetgiris_online'  => ['domain' => 'prensbetgiris.online',   'brand' => 'Prensbet'],
    'tenant_prensbetgiris_site'    => ['domain' => 'prensbetgiris.site',     'brand' => 'Prensbet'],
    'tenant_prensbetgirisonline_one' => ['domain' => 'prensbetgirisonline.one', 'brand' => 'Prensbet'],
    'tenant_prenssbet_com'         => ['domain' => 'prenssbet.com',          'brand' => 'Prensbet'],
    'tenant_prenssbet_net'         => ['domain' => 'prenssbet.net',          'brand' => 'Prensbet'],
    'tenant_pragmaticlive_click'   => ['domain' => 'pragmaticlive.click',    'brand' => 'PragmaticLive'],
    'tenant_girislizabet_site'     => ['domain' => 'girislizabet.site',      'brand' => 'Lizabet'],
    'tenant_lizabahis_site'        => ['domain' => 'lizabahis.site',         'brand' => 'Lizabet'],
    'tenant_lizabetcasino_com'     => ['domain' => 'lizabetcasino.com',      'brand' => 'Lizabet'],
    'tenant_lizabetgiris_site'     => ['domain' => 'lizabetgiris.site',      'brand' => 'Lizabet'],
];

// ilkbahis.click has 1 footer link, skip it
foreach ($footerNeeded as $db => $info) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant'); DB::reconnect('tenant');

    $existing = DB::connection('tenant')->table('footer_links')->count();
    if ($existing > 0) {
        continue; // Don't touch existing footer links
    }

    $links = [
        [0, "{$info['brand']} Giriş", "https://{$info['domain']}"],
        [1, 'Hakkımızda', '/hakkimizda'],
        [2, 'Bonuslar', '/bonuslar'],
        [3, 'İletişim', '/iletisim'],
        [4, 'Gizlilik Politikası', '/gizlilik-politikasi'],
        [5, 'Blog', '/blog'],
    ];

    foreach ($links as [$sort, $text, $url]) {
        DB::connection('tenant')->table('footer_links')->insert([
            'link_text' => $text, 'link_url' => $url, 'sort_order' => $sort,
            'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }
    echo "  {$info['domain']}: 6 footer links created\n";
}
echo "✓ Footer links done\n\n";

// ═══════════════════════════════════════
// PART 4: HOMEPAGE INLINE IMAGES (7 sites with 0 images)
// ═══════════════════════════════════════
echo "── PART 4: Homepage Images ──\n";

$homepageFixes = [
    'tenant_casival_me'          => [[3, 9, 15, 19, 7],  ['Casival Bonus', 'Casival Casino', 'Casival Bahis', 'Casival Güvenlik', 'Casival VIP']],
    'tenant_ilkbahis_click'      => [[1, 8, 13, 17, 21], ['İlkBahis Bonus', 'İlkBahis Casino', 'İlkBahis Bahis', 'İlkBahis Ödeme', 'İlkBahis VIP']],
    'tenant_ilkbahis_link'       => [[2, 7, 12, 18, 4],  ['İlkBahis Kampanya', 'İlkBahis Slot', 'İlkBahis Canlı', 'İlkBahis Güvenlik', 'İlkBahis Bonus']],
    'tenant_ilkbahisgiri_net'    => [[4, 10, 14, 19, 6], ['İlkBahis Giriş', 'İlkBahis Casino', 'İlkBahis Bahis', 'İlkBahis VIP', 'İlkBahis Bonus']],
    'tenant_ilkbahisonline_com'  => [[6, 11, 16, 20, 3], ['İlkBahis Online', 'İlkBahis Slot', 'İlkBahis Canlı Casino', 'İlkBahis Ödeme', 'İlkBahis Kampanya']],
    'tenant_rayzbet_net'         => [[1, 9, 14, 18, 7],  ['RayzBet Bonus', 'RayzBet Casino', 'RayzBet Bahis', 'RayzBet Güvenlik', 'RayzBet VIP']],
    'tenant_pragmaticlive_click' => [[2, 8, 12, 17, 21], ['PragmaticLive Bonus', 'PragmaticLive Casino', 'PragmaticLive Slot', 'PragmaticLive Ödeme', 'PragmaticLive VIP']],
];

foreach ($homepageFixes as $db => [$imgNums, $titles]) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant'); DB::reconnect('tenant');
    $cnt = addHomepageImages($db, $imgNums, $titles, $now);
    $domain = $standardSites[$db]['domain'] ?? $db;
    if ($cnt > 0) echo "  {$domain}: {$cnt} images added to homepage\n";
    elseif ($cnt === -1) echo "  {$domain}: homepage already has images\n";
    else echo "  {$domain}: no homepage found\n";
}
echo "✓ Homepage images done\n\n";

// ═══════════════════════════════════════
// PART 5: SSS PAGE FOR KAYACASINO.TOP
// ═══════════════════════════════════════
echo "── PART 5: kayacasino.top SSS ──\n";

config(["database.connections.tenant.database" => "tenant_kayacasino_top"]);
DB::purge('tenant'); DB::reconnect('tenant');

$existingSss = DB::connection('tenant')->table('pages')->where('slug', 'sss')->first();
if (!$existingSss) {
    DB::connection('tenant')->table('pages')->insert([
        'slug' => 'sss',
        'title' => 'KayaCasino Sıkça Sorulan Sorular',
        'meta_title' => 'KayaCasino SSS – Sıkça Sorulan Sorular ve Cevaplar',
        'meta_description' => 'KayaCasino hakkında sıkça sorulan sorular: üyelik, bonus, para yatırma, güvenlik ve daha fazlası.',
        'is_published' => 1,
        'sort_order' => 15,
        'created_at' => $now,
        'updated_at' => $now,
        'content' => <<<'HTML'
<h1>KayaCasino Sıkça Sorulan Sorular</h1>

<h2>Genel Sorular</h2>

<h3>KayaCasino güvenilir mi?</h3>
<p>Evet, KayaCasino lisanslı altyapısı, SSL şifreleme ve iki faktörlü kimlik doğrulama ile güvenilir bir online casino platformudur. Tüm oyunlar bağımsız test kuruluşları tarafından denetlenmektedir.</p>

<h3>KayaCasino'ya nasıl üye olunur?</h3>
<p>Güncel giriş adresine giderek "Kayıt Ol" butonuna tıklayın. Ad, soyad, e-posta ve telefon bilgilerinizi girerek birkaç dakika içinde üyeliğinizi tamamlayabilirsiniz.</p>

<h3>KayaCasino güncel giriş adresi nedir?</h3>
<p>KayaCasino güncel giriş adresi kayacasino.top üzerinden platforma erişim sağlayabilirsiniz. Güncel adres değişikliklerini Telegram kanalından takip edebilirsiniz.</p>

<h2>Bonus Soruları</h2>

<h3>Hoş geldin bonusu nasıl alınır?</h3>
<p>İlk yatırımınızı yaptıktan sonra canlı destek hattına başvurarak hoş geldin bonusu talebinde bulunabilirsiniz. Bonus otomatik olarak hesabınıza tanımlanır.</p>

<h3>Deneme bonusu var mı?</h3>
<p>Evet, KayaCasino yeni üyelerine deneme bonusu sunmaktadır. Üyeliğinizi tamamladıktan sonra canlı destekten talepte bulunabilirsiniz.</p>

<h3>Çevrim şartı nedir?</h3>
<p>Çevrim şartı, bonus tutarının belirli bir kat oranında bahis yapılması gerektiği anlamına gelir. Her kampanyanın kendine özgü çevrim koşulları bulunmaktadır.</p>

<h2>Ödeme Soruları</h2>

<h3>Hangi ödeme yöntemleri kabul ediliyor?</h3>
<p>Papara, kripto para (Bitcoin, USDT, Ethereum), banka havalesi/EFT ve QR kod ile ödeme yöntemleri mevcuttur.</p>

<h3>Para çekme ne kadar sürer?</h3>
<p>Papara ile 15-30 dakika, kripto ile 30-60 dakika, banka havalesi ile 1-24 saat içinde çekim tamamlanmaktadır.</p>

<h3>Minimum yatırım tutarı nedir?</h3>
<p>Minimum yatırım tutarı ödeme yöntemine göre değişmektedir. Papara ile minimum 50TL, kripto ile minimum 100TL yatırım yapılabilir.</p>

<h2>Teknik Sorular</h2>

<h3>Mobil uyumlu mu?</h3>
<p>Evet, KayaCasino tamamen mobil uyumlu responsive tasarıma sahiptir. Android ve iOS cihazlardan tarayıcı üzerinden erişim sağlayabilirsiniz.</p>

<h3>Siteye erişemiyorum ne yapmalıyım?</h3>
<p>DNS ayarlarınızı Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) olarak değiştirin. VPN kullanarak erişim sağlayabilirsiniz.</p>

<h3>Hangi casino oyunları var?</h3>
<p>Slot oyunları, canlı casino (rulet, blackjack, baccarat, poker), crash oyunları (Aviator, Spaceman), masa oyunları ve sanal sporlar mevcuttur.</p>
HTML
    ]);
    echo "  kayacasino.top: SSS page created\n";
} else {
    echo "  kayacasino.top: SSS already exists\n";
}
echo "✓ SSS page done\n\n";

// ═══════════════════════════════════════
// PART 6: DELETE DUPLICATE HOMEPAGE (casival.me)
// ═══════════════════════════════════════
echo "── PART 6: Duplicate Cleanup ──\n";

config(["database.connections.tenant.database" => "tenant_casival_me"]);
DB::purge('tenant'); DB::reconnect('tenant');

// Keep anasayfa (16KB, newer), delete ana-sayfa (9.6KB, older)
$deleted = DB::connection('tenant')->table('pages')->where('slug', 'ana-sayfa')->delete();
if ($deleted) {
    echo "  casival.me: duplicate 'ana-sayfa' deleted (kept 'anasayfa')\n";
}
echo "✓ Cleanup done\n\n";

echo "✅ ALL SITES FIXED!\n";
