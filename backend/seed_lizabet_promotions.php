<?php

/**
 * Lizabet Sites — Promotion Seed (card only, no slider)
 * 4 sites × 8 promotions each = 32 total
 *
 * Usage:
 *   cd /var/www/multi-tenant-cms/backend
 *   php artisan tinker --execute="require 'seed_lizabet_promotions.php';"
 */

use App\Models\Site;
use App\Models\SitePromotion;

$imgBase = '/storage/promotions/lizabet';

// All 18 promo images with Turkish titles
$allPromos = [
    '50-hosgeldin'         => '%50 Hoşgeldin Bonusu',
    '500tl-deneme'         => '500TL Deneme Bonusu',
    '15-cevrimsiz'         => '%15 Çevrimsiz Yatırım Bonusu',
    '20-kombine'           => '%20 Kombine Bonusu',
    '20-spor-kayip'        => '%20 Spor Bahisleri Kayıp Bonusu',
    '5-haftalik-jest'      => '%5 Haftalık Jest Bonusu',
    '1m-anlik-cekim'       => '1 Milyon Anlık Çekim',
    '20-casino-discount'   => '%20 Casino Discount',
    '20-kripto-yatirim'    => '%20 Kripto Yatırım Bonusu',
    '25-gece-kusu'         => '%25 Gece Kuşu Kayıp Bonusu',
    '25-ortaklik'          => '%25 Ortaklık Programı',
    '30-kripto-kayip'      => '%30 Kripto Kayıp Bonusu',
    '50-kripto-slot'       => '%50 Kripto Slot Bonusu',
    'ertesi-gun-freespin'  => 'Ertesi Gün Freespin Bonusu',
    'liza-sans-carki'      => 'Liza Şans Çarkı',
    'cifte-sans'           => 'Çifte Şans Bonusu',
    'paylas-kazan'         => 'Paylaş Kazan',
    'sadakat'              => 'Sadakat Programı',
];

// Each site gets a different set of 8 promotions (all as card display_type)
$siteSets = [
    'girislizabet.site' => [
        '50-hosgeldin', '500tl-deneme', '1m-anlik-cekim', '15-cevrimsiz',
        '5-haftalik-jest', 'cifte-sans', 'liza-sans-carki', 'paylas-kazan',
    ],
    'lizabahis.site' => [
        '50-hosgeldin', '20-kombine', '20-spor-kayip', '25-gece-kusu',
        '500tl-deneme', '20-kripto-yatirim', 'cifte-sans', 'sadakat',
    ],
    'lizabetcasino.com' => [
        '50-hosgeldin', '20-casino-discount', '50-kripto-slot', 'ertesi-gun-freespin',
        'liza-sans-carki', '500tl-deneme', '30-kripto-kayip', 'sadakat',
    ],
    'lizabetgiris.site' => [
        '50-hosgeldin', '500tl-deneme', '15-cevrimsiz', '20-kripto-yatirim',
        '30-kripto-kayip', '50-kripto-slot', '25-ortaklik', 'sadakat',
    ],
];

foreach ($siteSets as $domain => $slugs) {
    $site = Site::where('domain', $domain)->first();
    if (!$site) {
        echo "SKIP: Site not found: {$domain}\n";
        continue;
    }

    // Clear existing promotions
    SitePromotion::where('site_id', $site->id)->delete();
    echo "=== {$domain} (ID: {$site->id}) ===\n";

    foreach ($slugs as $order => $slug) {
        $title = $allPromos[$slug] ?? $slug;
        SitePromotion::create([
            'site_id'      => $site->id,
            'image'        => "{$imgBase}/{$slug}-PROMOTION.jpg",
            'title'        => $title,
            'link_url'     => null,
            'display_type' => 'card',
            'sort_order'   => $order + 1,
            'is_active'    => true,
        ]);
        echo "  [{$order}] {$title}\n";
    }

    echo "  -> {$domain}: " . count($slugs) . " promotions inserted\n\n";
}

echo "Done! All Lizabet promotions seeded.\n";
