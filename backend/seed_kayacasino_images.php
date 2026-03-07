<?php

/**
 * Kaya Casino — Images & Offers Seed
 * Adds promotions, top_offers, earnings (slot images), and embeds TIB images in content
 *
 * Usage:
 *   php artisan tinker --execute="$(tail -n +3 seed_kayacasino_images.php)"
 */

use App\Models\Site;
use App\Models\SitePromotion;
use App\Models\SiteEarning;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

$site = Site::where('domain', 'kayacasino.top')->firstOrFail();
$siteId = $site->id;

echo "=== Inserting images for kayacasino.top (site_id: {$siteId}) ===\n";

// ─── 1. SITE PROMOTIONS (slider images) ───

$promotions = [
    [
        'image' => '/storage/promotions/kayacasino/100ho-geldinbonusu-SLIDER.jpg',
        'title' => '%100 Hoş Geldin Bonusu',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'slider',
        'sort_order' => 1,
    ],
    [
        'image' => '/storage/promotions/kayacasino/25yatirim-SLIDER.jpg',
        'title' => '%25 Yatırım Bonusu',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'slider',
        'sort_order' => 2,
    ],
    [
        'image' => '/storage/promotions/kayacasino/30casinodisc-SLIDER.jpg',
        'title' => '%30 Casino Discount',
        'link_url' => '/kayacasino-casino-oyunlari',
        'display_type' => 'slider',
        'sort_order' => 3,
    ],
    [
        'image' => '/storage/promotions/kayacasino/50slotyatirim-SLIDER.jpg',
        'title' => '%50 Slot Yatırım Bonusu',
        'link_url' => '/kayacasino-slot-oyunlari',
        'display_type' => 'slider',
        'sort_order' => 4,
    ],
    [
        'image' => '/storage/promotions/kayacasino/ekstra-SLIDER.jpg',
        'title' => 'Ekstra Bonus Fırsatı',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'slider',
        'sort_order' => 5,
    ],
    [
        'image' => '/storage/promotions/kayacasino/haftalikekstra5-SLIDER.jpg',
        'title' => 'Haftalık %5 Ekstra Bonus',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'slider',
        'sort_order' => 6,
    ],
    [
        'image' => '/storage/promotions/kayacasino/SLIDER.jpg',
        'title' => 'Özel Kampanya',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'slider',
        'sort_order' => 7,
    ],
];

// Also add PROMOTIONS variants as card type
$promotionCards = [
    [
        'image' => '/storage/promotions/kayacasino/100ho-geldinbonusu-PROMOTIONS.jpg',
        'title' => '%100 Hoş Geldin Bonusu',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'card',
        'sort_order' => 1,
    ],
    [
        'image' => '/storage/promotions/kayacasino/25yatirim-PROMOTIONS.jpg',
        'title' => '%25 Yatırım Bonusu',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'card',
        'sort_order' => 2,
    ],
    [
        'image' => '/storage/promotions/kayacasino/30casinodisc-PROMOTIONS.jpg',
        'title' => '%30 Casino Discount',
        'link_url' => '/kayacasino-casino-oyunlari',
        'display_type' => 'card',
        'sort_order' => 3,
    ],
    [
        'image' => '/storage/promotions/kayacasino/50slotyatirim-PROMOTIONS.jpg',
        'title' => '%50 Slot Yatırım Bonusu',
        'link_url' => '/kayacasino-slot-oyunlari',
        'display_type' => 'card',
        'sort_order' => 4,
    ],
    [
        'image' => '/storage/promotions/kayacasino/ekstra-PROMOTIONS.jpg',
        'title' => 'Ekstra Bonus Fırsatı',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'card',
        'sort_order' => 5,
    ],
    [
        'image' => '/storage/promotions/kayacasino/haftalikekstra5-PROMOTIONS.jpg',
        'title' => 'Haftalık %5 Ekstra Bonus',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'card',
        'sort_order' => 6,
    ],
    [
        'image' => '/storage/promotions/kayacasino/PROMOTIONS.jpg',
        'title' => 'Özel Kampanya',
        'link_url' => '/kayacasino-bonuslari',
        'display_type' => 'card',
        'sort_order' => 7,
    ],
];

// Clear existing promotions for this site
SitePromotion::where('site_id', $siteId)->delete();

foreach (array_merge($promotions, $promotionCards) as $promo) {
    SitePromotion::create(array_merge($promo, [
        'site_id' => $siteId,
        'is_active' => true,
    ]));
}
echo "  ✓ Created " . count($promotions) . " slider + " . count($promotionCards) . " card promotions\n";

// ─── 2. SITE EARNINGS (slot game images as kazanç görselleri) ───

$slotGames = [
    ['image' => '/storage/games/kayacasino/kaya_gates_of_olympus_daily_slot.jpg', 'title' => 'Gates of Olympus', 'content' => 'Günlük slot kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_gates_of_olympus_1000_daily_slot.jpg', 'title' => 'Gates of Olympus 1000', 'content' => 'Büyük çarpan kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_gates_of_hades_daily_slot.jpg', 'title' => 'Gates of Hades', 'content' => 'Hades slot kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_gates_of_olympus_dice_daily_slot.jpg', 'title' => 'Gates of Olympus Dice', 'content' => 'Dice versiyon kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_gates_of_olympus_super_scatter_daily_slot.jpg', 'title' => 'Gates of Olympus Super Scatter', 'content' => 'Super Scatter kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_gates_of_olympus_xmas_1000_daily_slot.jpg', 'title' => 'Gates of Olympus Xmas 1000', 'content' => 'Noel özel kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_bonanza_daily_slot.jpg', 'title' => 'Sweet Bonanza', 'content' => 'Sweet Bonanza günlük kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_bonanza_1000_daily_slot.jpg', 'title' => 'Sweet Bonanza 1000', 'content' => '1000x büyük kazançlar'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_bonanza_super_scatter_daily_slot.jpg', 'title' => 'Sweet Bonanza Super Scatter', 'content' => 'Scatter bonus kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_baklava_daily_slot.jpg', 'title' => 'Sweet Baklava', 'content' => 'Baklava slot kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_burst_daily_slot.jpg', 'title' => 'Sweet Burst', 'content' => 'Burst modu kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_kingdom_daily_slot.jpg', 'title' => 'Sweet Kingdom', 'content' => 'Kingdom serisi kazançları'],
    ['image' => '/storage/games/kayacasino/kaya_sweet_rush_bonanza_daily_slot.jpg', 'title' => 'Sweet Rush Bonanza', 'content' => 'Rush Bonanza kazançları'],
];

SiteEarning::where('site_id', $siteId)->delete();

foreach ($slotGames as $i => $game) {
    SiteEarning::create([
        'site_id' => $siteId,
        'image' => $game['image'],
        'video_url' => null,
        'title' => $game['title'],
        'content' => $game['content'],
        'sort_order' => $i + 1,
        'is_active' => true,
    ]);
}
echo "  ✓ Created " . count($slotGames) . " earnings (slot images)\n";

// ─── 3. TOP OFFERS (tenant DB) ───

Config::set('database.connections.tenant.database', $site->db_name);
DB::connection('tenant')->reconnect();

$topOffers = [
    [
        'slug' => 'hos-geldin-bonusu',
        'logo_url' => '/storage/promotions/kayacasino/100ho-geldinbonusu-SOCIAL.jpg',
        'bonus_text' => '%100 Hoş Geldin Bonusu',
        'cta_text' => 'Hemen Katıl',
        'target_url' => '/kayacasino-bonuslari',
        'sort_order' => 1,
    ],
    [
        'slug' => 'yatirim-bonusu',
        'logo_url' => '/storage/promotions/kayacasino/25yatirim-SOCIAL.jpg',
        'bonus_text' => '%25 Yatırım Bonusu',
        'cta_text' => 'Yatırım Yap',
        'target_url' => '/kayacasino-para-yatirma',
        'sort_order' => 2,
    ],
    [
        'slug' => 'casino-discount',
        'logo_url' => '/storage/promotions/kayacasino/30casinodisc-SOCIAL.jpg',
        'bonus_text' => '%30 Casino Discount',
        'cta_text' => 'Oyna',
        'target_url' => '/kayacasino-casino-oyunlari',
        'sort_order' => 3,
    ],
    [
        'slug' => 'slot-yatirim-bonusu',
        'logo_url' => '/storage/promotions/kayacasino/50slotyatirim-SOCIAL.jpg',
        'bonus_text' => '%50 Slot Yatırım Bonusu',
        'cta_text' => 'Slot Oyna',
        'target_url' => '/kayacasino-slot-oyunlari',
        'sort_order' => 4,
    ],
    [
        'slug' => 'ekstra-bonus',
        'logo_url' => '/storage/promotions/kayacasino/ekstra-SOCIAL.jpg',
        'bonus_text' => 'Ekstra Bonus Fırsatı',
        'cta_text' => 'Detaylar',
        'target_url' => '/kayacasino-bonuslari',
        'sort_order' => 5,
    ],
    [
        'slug' => 'haftalik-ekstra',
        'logo_url' => '/storage/promotions/kayacasino/haftalikekstra5-SOCIAL.jpg',
        'bonus_text' => 'Haftalık %5 Ekstra',
        'cta_text' => 'Fırsatı Yakala',
        'target_url' => '/kayacasino-bonuslari',
        'sort_order' => 6,
    ],
];

DB::connection('tenant')->table('top_offers')->truncate();

foreach ($topOffers as $offer) {
    DB::connection('tenant')->table('top_offers')->insert(array_merge($offer, [
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]));
}
echo "  ✓ Created " . count($topOffers) . " top offers\n";

// ─── 4. ENABLE SPONSORS & OFFER DISPLAY ───

$site->update([
    'show_sponsors' => true,
]);
echo "  ✓ Enabled show_sponsors\n";

// ─── 5. EMBED TIB IMAGES IN ANASAYFA CONTENT ───

$anasayfa = Page::on('tenant')->where('slug', 'anasayfa')->first();
if ($anasayfa) {
    $tibGallery = '<h2>Son Kazananlar</h2>' . "\n";
    $tibGallery .= '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin:20px 0;">' . "\n";
    for ($i = 426; $i <= 440; $i++) {
        $tibGallery .= '<img src="/storage/images/kayacasino/kaya_single_event_' . $i . '.jpg" alt="Kaya Casino Kazanan ' . ($i - 425) . '" loading="lazy" style="width:100%;border-radius:8px;" />' . "\n";
    }
    $tibGallery .= '</div>';

    // Append before closing FAQ or at end
    $content = $anasayfa->content;
    if (strpos($content, '<h2>Sık Sorulan Sorular</h2>') !== false) {
        $content = str_replace('<h2>Sık Sorulan Sorular</h2>', $tibGallery . "\n\n" . '<h2>Sık Sorulan Sorular</h2>', $content);
    } else {
        $content .= "\n\n" . $tibGallery;
    }
    $anasayfa->update(['content' => $content]);
    echo "  ✓ Embedded 15 TIB images in anasayfa\n";
}

// ─── 6. EMBED SLOT IMAGES IN SLOT SAYFASI ───

$slotPage = Page::on('tenant')->where('slug', 'kayacasino-slot-oyunlari')->first();
if ($slotPage) {
    $slotGallery = '<h2>Popüler Slot Oyunları</h2>' . "\n";
    $slotGallery .= '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin:20px 0;">' . "\n";
    $slotNames = [
        'Gates of Olympus', 'Gates of Olympus 1000', 'Gates of Hades',
        'Gates of Olympus Dice', 'Gates of Olympus Super Scatter', 'Gates of Olympus Xmas 1000',
        'Sweet Bonanza', 'Sweet Bonanza 1000', 'Sweet Bonanza Super Scatter',
        'Sweet Baklava', 'Sweet Burst', 'Sweet Kingdom', 'Sweet Rush Bonanza',
    ];
    $slotFiles = [
        'kaya_gates_of_olympus_daily_slot.jpg', 'kaya_gates_of_olympus_1000_daily_slot.jpg',
        'kaya_gates_of_hades_daily_slot.jpg', 'kaya_gates_of_olympus_dice_daily_slot.jpg',
        'kaya_gates_of_olympus_super_scatter_daily_slot.jpg', 'kaya_gates_of_olympus_xmas_1000_daily_slot.jpg',
        'kaya_sweet_bonanza_daily_slot.jpg', 'kaya_sweet_bonanza_1000_daily_slot.jpg',
        'kaya_sweet_bonanza_super_scatter_daily_slot.jpg', 'kaya_sweet_baklava_daily_slot.jpg',
        'kaya_sweet_burst_daily_slot.jpg', 'kaya_sweet_kingdom_daily_slot.jpg',
        'kaya_sweet_rush_bonanza_daily_slot.jpg',
    ];
    foreach ($slotFiles as $j => $file) {
        $name = $slotNames[$j] ?? 'Slot Oyunu';
        $slotGallery .= '<div style="text-align:center;">';
        $slotGallery .= '<img src="/storage/games/kayacasino/' . $file . '" alt="' . $name . ' - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" />';
        $slotGallery .= '<p style="margin:6px 0;font-size:14px;font-weight:600;">' . $name . '</p>';
        $slotGallery .= '</div>' . "\n";
    }
    $slotGallery .= '</div>';

    $content = $slotPage->content;
    if (strpos($content, '<h2>Sık Sorulan Sorular</h2>') !== false) {
        $content = str_replace('<h2>Sık Sorulan Sorular</h2>', $slotGallery . "\n\n" . '<h2>Sık Sorulan Sorular</h2>', $content);
    } else {
        $content .= "\n\n" . $slotGallery;
    }
    $slotPage->update(['content' => $content]);
    echo "  ✓ Embedded 13 slot images in slot page\n";
}

// ─── 7. EMBED PROMO IMAGES IN BONUS SAYFASI ───

$bonusPage = Page::on('tenant')->where('slug', 'kayacasino-bonuslari')->first();
if ($bonusPage) {
    $promoGallery = '<h2>Güncel Promosyonlar</h2>' . "\n";
    $promoGallery .= '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin:20px 0;">' . "\n";
    $promoItems = [
        ['file' => '100ho-geldinbonusu-PROMOTIONS.jpg', 'title' => '%100 Hoş Geldin Bonusu'],
        ['file' => '25yatirim-PROMOTIONS.jpg', 'title' => '%25 Yatırım Bonusu'],
        ['file' => '30casinodisc-PROMOTIONS.jpg', 'title' => '%30 Casino Discount'],
        ['file' => '50slotyatirim-PROMOTIONS.jpg', 'title' => '%50 Slot Yatırım Bonusu'],
        ['file' => 'ekstra-PROMOTIONS.jpg', 'title' => 'Ekstra Bonus Fırsatı'],
        ['file' => 'haftalikekstra5-PROMOTIONS.jpg', 'title' => 'Haftalık %5 Ekstra'],
        ['file' => 'PROMOTIONS.jpg', 'title' => 'Özel Kampanya'],
    ];
    foreach ($promoItems as $p) {
        $promoGallery .= '<div style="text-align:center;">';
        $promoGallery .= '<img src="/storage/promotions/kayacasino/' . $p['file'] . '" alt="' . $p['title'] . ' - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" />';
        $promoGallery .= '<p style="margin:8px 0;font-size:15px;font-weight:600;">' . $p['title'] . '</p>';
        $promoGallery .= '</div>' . "\n";
    }
    $promoGallery .= '</div>';

    $content = $bonusPage->content;
    if (strpos($content, '<h2>Sık Sorulan Sorular</h2>') !== false) {
        $content = str_replace('<h2>Sık Sorulan Sorular</h2>', $promoGallery . "\n\n" . '<h2>Sık Sorulan Sorular</h2>', $content);
    } else {
        $content .= "\n\n" . $promoGallery;
    }
    $bonusPage->update(['content' => $content]);
    echo "  ✓ Embedded 7 promo images in bonus page\n";
}

// ─── 8. EMBED SLOT IMAGES IN CASINO PAGE ───

$casinoPage = Page::on('tenant')->where('slug', 'kayacasino-casino-oyunlari')->first();
if ($casinoPage) {
    $casinoGallery = '<h2>Öne Çıkan Oyunlar</h2>' . "\n";
    $casinoGallery .= '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin:20px 0;">' . "\n";
    // Use first 6 slot images for casino page
    $casinoSlots = array_slice($slotFiles, 0, 6);
    $casinoNames = array_slice($slotNames, 0, 6);
    foreach ($casinoSlots as $j => $file) {
        $name = $casinoNames[$j] ?? 'Oyun';
        $casinoGallery .= '<div style="text-align:center;">';
        $casinoGallery .= '<img src="/storage/games/kayacasino/' . $file . '" alt="' . $name . ' - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" />';
        $casinoGallery .= '<p style="margin:6px 0;font-size:14px;font-weight:600;">' . $name . '</p>';
        $casinoGallery .= '</div>' . "\n";
    }
    $casinoGallery .= '</div>';

    $content = $casinoPage->content;
    if (strpos($content, '<h2>Sık Sorulan Sorular</h2>') !== false) {
        $content = str_replace('<h2>Sık Sorulan Sorular</h2>', $casinoGallery . "\n\n" . '<h2>Sık Sorulan Sorular</h2>', $content);
    } else {
        $content .= "\n\n" . $casinoGallery;
    }
    $casinoPage->update(['content' => $content]);
    echo "  ✓ Embedded 6 slot images in casino page\n";
}

echo "\n=== ALL IMAGES DONE ===\n";
