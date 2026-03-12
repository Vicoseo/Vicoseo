<?php
/**
 * Prenssbet.org Full SEO Site Seed
 * Usage: php artisan tinker --execute="require 'seed_prenssbet_org.php';"
 */

use Illuminate\Support\Facades\DB;

$now = now();
$imgBase = '/storage/uploads/promotions/prensbet-org';
$linkUrl = 'https://prenssbet.org';
$brand = 'Prensbet';

// ─── 1. UPDATE SITE RECORD ───
DB::connection('mysql')->table('sites')->where('id', 28)->update([
    'logo_url' => '/storage/uploads/site-logos/prensbet-logo-new2.png',
    'favicon_url' => '/storage/uploads/site-logos/prensbet-icon-new.png',
    'meta_title' => 'Prensbet Giriş 2026 | Güncel Adres, Bonus ve Kapsamlı Rehber',
    'meta_description' => 'Prensbet güncel giriş adresi 2026 ✓ 1000TL deneme bonusu, %100 hoş geldin bonusu, canlı casino, spor bahisleri ve slot oyunları. Güvenilir platform.',
    'primary_color' => '#1a1a2e',
    'secondary_color' => '#d4a017',
    'header_bg_color' => '#0d0d1a',
    'updated_at' => $now,
]);
echo "✓ Site record updated\n";

// ─── 2. PROMOTIONS (5 slider + 7 card) ───
DB::connection('mysql')->table('site_promotions')->where('site_id', 28)->delete();

$sliders = [
    [0, 'hosgeldin-bonusu.jpg', '%100 Casino & Spor Hoş Geldin Bonusu'],
    [1, 'geri-iade-bonusu.jpg', 'İlk Yatırıma Özel %100 Geri İade Bonusu'],
    [2, 'aviator-cevrimsiz.jpg', '%15 Çevrimsiz Aviator Yatırım Bonusu'],
    [3, 'slider-pazar-ozel.jpg', 'Pazar Gününe Özel 5X Yap 7X Çek'],
    [4, 'ortaklik-programi.jpg', 'Prensbet Ortaklık Programı'],
];

$cards = [
    [0, '1000tl-deneme-bonusu.jpg', '1000TL Deneme Bonusu'],
    [1, '5000tl-yatirim-bonusu.jpg', '5000TL Üstü Yatırım Bonusu'],
    [2, 'haftalik-kayip-bonusu.jpg', '%30 Haftalık Kayıp Bonusu'],
    [3, 'kripto-freespin.jpg', 'Kripto Ek Hediye Freespin'],
    [4, 'kumbara-puan.jpg', 'Kumbara Puan Sistemi'],
    [5, 'prensbet-vip.jpg', 'Prensbet VIP Club'],
    [6, 'genel-bonus-kurallari.jpg', 'Genel Bonus Kuralları'],
];

foreach ($sliders as [$sort, $img, $title]) {
    DB::connection('mysql')->table('site_promotions')->insert([
        'site_id' => 28, 'image' => "$imgBase/$img", 'title' => $title,
        'link_url' => $linkUrl, 'display_type' => 'slider', 'sort_order' => $sort,
        'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
    ]);
}
foreach ($cards as [$sort, $img, $title]) {
    DB::connection('mysql')->table('site_promotions')->insert([
        'site_id' => 28, 'image' => "$imgBase/$img", 'title' => $title,
        'link_url' => $linkUrl, 'display_type' => 'card', 'sort_order' => $sort,
        'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "✓ 12 promotions created\n";

// ─── 3. SWITCH TO TENANT DB ───
$tenantDb = 'tenant_prenssbet_org';
config(["database.connections.tenant.database" => $tenantDb]);
DB::purge('tenant');
DB::reconnect('tenant');

// ─── 4. CATEGORIES ───
DB::connection('tenant')->table('categories')->truncate();
$categories = [
    ['Güncel Giriş', 'guncel-giris', 'Prensbet Güncel Giriş Adresi 2026', 'Prensbet güncel giriş adresi, yeni link ve erişim rehberi 2026.'],
    ['Bonus', 'bonus', 'Prensbet Bonus ve Kampanyalar', 'Prensbet bonus kampanyaları, hoş geldin bonusu, deneme bonusu ve çevrim şartları.'],
    ['Slot Oyunları', 'slot', 'Prensbet Slot Oyunları Rehberi', 'Prensbet slot oyunları, en çok kazandıran slotlar ve strateji rehberi.'],
    ['Canlı Casino', 'canli-casino', 'Prensbet Canlı Casino Oyunları', 'Prensbet canlı casino, rulet, blackjack ve poker rehberi.'],
    ['Canlı Bahis', 'canli-bahis', 'Prensbet Canlı Bahis Rehberi', 'Prensbet canlı bahis, futbol, basketbol ve kombine kupon rehberi.'],
    ['Güvenilirlik', 'guvenilirlik', 'Prensbet Güvenilir mi?', 'Prensbet güvenilirlik analizi, lisans bilgisi ve kullanıcı yorumları.'],
    ['Üyelik & Kayıt', 'uyelik', 'Prensbet Üyelik ve Kayıt', 'Prensbet üyelik, kayıt ve hesap doğrulama rehberi.'],
    ['Para Yatırma & Çekme', 'para-islemleri', 'Prensbet Para Yatırma', 'Prensbet para yatırma ve çekme yöntemleri, limitler ve süreler.'],
];

$catIds = [];
foreach ($categories as $i => [$name, $slug, $metaTitle, $metaDesc]) {
    $catIds[$slug] = DB::connection('tenant')->table('categories')->insertGetId([
        'name' => $name, 'slug' => $slug, 'meta_title' => $metaTitle,
        'meta_description' => $metaDesc, 'sort_order' => $i,
        'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "✓ 8 categories created\n";

// ─── 5. FOOTER LINKS ───
DB::connection('tenant')->table('footer_links')->truncate();
$footerLinks = [
    [1, 'Prensbet Giriş', 'https://prenssbet.org'],
    [2, 'Prensbet Bonus', '/bonuslar'],
    [3, 'Prensbet Casino', '/casino-oyunlari'],
    [4, 'Prensbet Mobil', '/prensbet-mobil-giris'],
    [5, 'Prensbet Kayıt', '/prensbet-kayit'],
    [6, 'Blog', '/blog'],
];
foreach ($footerLinks as [$sort, $text, $url]) {
    DB::connection('tenant')->table('footer_links')->insert([
        'link_text' => $text, 'link_url' => $url, 'sort_order' => $sort,
        'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "✓ 6 footer links created\n";

// ─── 6. HOMEPAGE (sort_order=0) ───
// Will be added by require
require __DIR__ . '/seed_prenssbet_org_pages.php';

// ─── 7. BLOG POSTS ───
require __DIR__ . '/seed_prenssbet_org_posts.php';

echo "\n✅ Prenssbet.org seed completed!\n";
