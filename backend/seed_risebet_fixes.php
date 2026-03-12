<?php
/**
 * RiseBet All Sites Fix: featured images, homepage images, footer links,
 * risespor categories, meta_description, header colors, duplicate page cleanup
 *
 * Usage: php artisan tinker --execute="require 'seed_risebet_fixes.php';"
 */

use Illuminate\Support\Facades\DB;

$now = now();

// ═══════════════════════════════════════
// PART 1: Fix site records in cms_main
// ═══════════════════════════════════════

// risespor.com meta_description
DB::connection('mysql')->table('sites')->where('id', 18)->update([
    'meta_title' => 'RiseBet Spor Güncel Giriş 2026 | Canlı Bahis, Casino ve Bonus',
    'meta_description' => 'RiseBet Spor güncel giriş adresi 2026 ✓ Canlı bahis, spor bahisleri, casino oyunları, hoş geldin bonusu. Güvenilir lisanslı platform.',
    'updated_at' => $now,
]);
echo "✓ risespor.com meta_description fixed\n";

// header_bg_color for all rise sites
DB::connection('mysql')->table('sites')->where('id', 1)->update([
    'header_bg_color' => '#0d1b2a', 'updated_at' => $now,
]);
DB::connection('mysql')->table('sites')->whereIn('id', [8, 15, 16, 18])->update([
    'primary_color' => '#e63946',
    'secondary_color' => '#1d3557',
    'header_bg_color' => '#0d1b2a',
    'updated_at' => $now,
]);
echo "✓ Brand colors and header_bg_color set for all rise sites\n";

// ═══════════════════════════════════════
// PART 2: Featured images for all rise sites
// ═══════════════════════════════════════

$pageImages = [];
$nums = [1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21];
foreach ($nums as $n) {
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

$riseTenants = [
    'tenant_1',
    'tenant_risebett_me',
    'tenant_risebette_com',
    'tenant_risebets_click',
    'tenant_risespor_com',
];

foreach ($riseTenants as $db) {
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
echo "✓ Featured images assigned\n\n";

// ═══════════════════════════════════════
// PART 3: Homepage inline images (all 5 sites)
// ═══════════════════════════════════════

$homepageImageSets = [
    [1, 6, 11, 16, 20],   // rise-bets.com
    [2, 7, 12, 17, 21],   // risebett.me
    [3, 8, 13, 18, 4],    // risebette.com
    [4, 9, 14, 19, 1],    // risebets.click
    [6, 10, 15, 20, 3],   // risespor.com
];

$imgTitles = [
    'RiseBet Bonus Kampanyaları',
    'RiseBet Casino Oyunları',
    'RiseBet Canlı Bahis',
    'RiseBet Güvenli Ödeme',
    'RiseBet VIP Ayrıcalıklar',
];

foreach ($riseTenants as $i => $db) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    $homepage = DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->first();

    if (!$homepage) {
        echo "  {$db}: no homepage\n";
        continue;
    }

    if (substr_count($homepage->content, '<img') >= 3) {
        echo "  {$db}: homepage already has images\n";
        continue;
    }

    $imgNums = $homepageImageSets[$i];
    $imageBlocks = [];
    foreach ($imgNums as $j => $num) {
        $src = "/storage/uploads/promotions/prensbet-sayfa/prensbet-sayfa-{$num}.jpg";
        $alt = $imgTitles[$j];
        $imageBlocks[] = '<img src="' . $src . '" alt="' . $alt . '" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px;margin:16px 0">';
    }

    $h2Count = 0;
    $imgIdx = 0;
    $newContent = preg_replace_callback('/<\/h2>/i', function($match) use (&$h2Count, &$imgIdx, $imageBlocks) {
        $h2Count++;
        if ($h2Count % 2 === 0 && $imgIdx < count($imageBlocks)) {
            $img = $imageBlocks[$imgIdx];
            $imgIdx++;
            return $match[0] . "\n" . $img;
        }
        return $match[0];
    }, $homepage->content);

    DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->update(['content' => $newContent, 'updated_at' => $now]);

    echo "  {$db}: {$imgIdx} images added to homepage\n";
}
echo "✓ Homepage inline images added\n\n";

// ═══════════════════════════════════════
// PART 4: Footer links (4 sites missing them)
// ═══════════════════════════════════════

$footerSites = [
    'tenant_risebett_me' => 'risebett.me',
    'tenant_risebette_com' => 'risebette.com',
    'tenant_risebets_click' => 'risebets.click',
    'tenant_risespor_com' => 'risespor.com',
];

$footerLinks = [
    [0, 'RiseBet Giriş', '__DOMAIN__'],
    [1, 'Hakkımızda', '/hakkimizda'],
    [2, 'Bonuslar', '/bonuslar'],
    [3, 'Casino Oyunları', '/casino-oyunlari'],
    [4, 'İletişim', '/iletisim'],
    [5, 'Gizlilik Politikası', '/gizlilik-politikasi'],
    [6, 'Blog', '/blog'],
];

foreach ($footerSites as $db => $domain) {
    config(["database.connections.tenant.database" => $db]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    $existing = DB::connection('tenant')->table('footer_links')->count();
    if ($existing > 0) {
        echo "  {$db}: already has {$existing} footer links\n";
        continue;
    }

    foreach ($footerLinks as [$sort, $text, $url]) {
        $linkUrl = ($url === '__DOMAIN__') ? "https://{$domain}" : $url;
        DB::connection('tenant')->table('footer_links')->insert([
            'link_text' => $text, 'link_url' => $linkUrl, 'sort_order' => $sort,
            'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }
    echo "  {$db}: 7 footer links created\n";
}
echo "✓ Footer links created\n\n";

// ═══════════════════════════════════════
// PART 5: risespor.com categories
// ═══════════════════════════════════════

config(["database.connections.tenant.database" => "tenant_risespor_com"]);
DB::purge('tenant');
DB::reconnect('tenant');

$existingCats = DB::connection('tenant')->table('categories')->count();
if ($existingCats === 0) {
    $categories = [
        ['RiseBet Güncel Giriş', 'guncel-giris', 'RiseBet Güncel Giriş Adresi 2026', 'RiseBet güncel giriş adresi, yeni link ve erişim rehberi.'],
        ['RiseBet Bonus', 'bonus', 'RiseBet Bonus ve Kampanyalar', 'RiseBet bonus kampanyaları, hoş geldin bonusu ve çevrim şartları.'],
        ['RiseBet Slot Oyunları', 'slot', 'RiseBet Slot Oyunları Rehberi', 'RiseBet slot oyunları, en çok kazandıran slotlar ve rehber.'],
        ['RiseBet Canlı Casino', 'canli-casino', 'RiseBet Canlı Casino Oyunları', 'RiseBet canlı casino, rulet, blackjack ve poker rehberi.'],
        ['RiseBet Canlı Bahis', 'canli-bahis', 'RiseBet Canlı Bahis Rehberi', 'RiseBet canlı bahis, futbol, basketbol ve kombine kupon.'],
        ['RiseBet Güvenilir mi', 'guvenilirlik', 'RiseBet Güvenilir mi?', 'RiseBet güvenilirlik analizi, lisans ve kullanıcı yorumları.'],
        ['RiseBet Üyelik & Kayıt', 'uyelik', 'RiseBet Üyelik ve Kayıt', 'RiseBet üyelik, kayıt ve hesap doğrulama rehberi.'],
        ['RiseBet Para Yatırma & Çekme', 'para-islemleri', 'RiseBet Para Yatırma', 'RiseBet para yatırma ve çekme yöntemleri, limitler.'],
    ];

    foreach ($categories as $i => [$name, $slug, $metaTitle, $metaDesc]) {
        DB::connection('tenant')->table('categories')->insert([
            'name' => $name, 'slug' => $slug, 'meta_title' => $metaTitle,
            'meta_description' => $metaDesc, 'sort_order' => $i,
            'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }
    echo "✓ risespor.com: 8 categories created\n";

    // Auto-assign categories to posts based on slug/title keywords
    $catMap = DB::connection('tenant')->table('categories')->pluck('id', 'slug')->toArray();

    $keywordMap = [
        'guncel-giris' => ['giris', 'adres', 'erişim', 'vpn', 'dns', 'guncel'],
        'bonus' => ['bonus', 'kampanya', 'promosyon', 'free-spin', 'freespin', 'hosgeldin'],
        'slot' => ['slot', 'sweet-bonanza', 'gates-of-olympus', 'pragmatic', 'megaways', 'dog-house'],
        'canli-casino' => ['casino', 'rulet', 'blackjack', 'baccarat', 'poker', 'krupiye', 'lightning'],
        'canli-bahis' => ['bahis', 'futbol', 'basketbol', 'kombine', 'kupon', 'spor', 'iddaa', 'mac'],
        'guvenilirlik' => ['guvenilir', 'lisans', 'guvenlik', 'ssl'],
        'uyelik' => ['kayit', 'uyelik', 'uye', 'hesap', 'dogrulama'],
        'para-islemleri' => ['para', 'yatirim', 'cekim', 'papara', 'kripto', 'havale', 'odeme'],
    ];

    $posts = DB::connection('tenant')->table('posts')
        ->whereNull('category_id')
        ->where('is_published', 1)
        ->get(['id', 'slug', 'title']);

    $assigned = 0;
    foreach ($posts as $post) {
        $searchText = strtolower($post->slug . ' ' . $post->title);
        $bestCat = null;

        foreach ($keywordMap as $catSlug => $keywords) {
            foreach ($keywords as $kw) {
                if (strpos($searchText, $kw) !== false) {
                    $bestCat = $catMap[$catSlug] ?? null;
                    break 2;
                }
            }
        }

        if ($bestCat) {
            DB::connection('tenant')->table('posts')
                ->where('id', $post->id)
                ->update(['category_id' => $bestCat, 'updated_at' => $now]);
            $assigned++;
        }
    }
    echo "  risespor.com: {$assigned} posts assigned to categories\n";
} else {
    echo "  risespor.com: already has {$existingCats} categories\n";
}
echo "\n";

// ═══════════════════════════════════════
// PART 6: Delete duplicate ana-sayfa from rise-bets.com
// ═══════════════════════════════════════

config(["database.connections.tenant.database" => "tenant_1"]);
DB::purge('tenant');
DB::reconnect('tenant');

$deleted = DB::connection('tenant')->table('pages')
    ->where('slug', 'ana-sayfa')
    ->delete();

if ($deleted) {
    echo "✓ rise-bets.com: duplicate 'ana-sayfa' page deleted\n";
} else {
    echo "  rise-bets.com: no duplicate found\n";
}

echo "\n✅ All RiseBet site fixes completed!\n";
