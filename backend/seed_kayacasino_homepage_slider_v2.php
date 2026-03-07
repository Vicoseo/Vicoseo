<?php
// Anasayfa slider v2 — site temasına uyumlu
// Çalıştır: php -r "require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); require 'seed_kayacasino_homepage_slider_v2.php';"

$db = \Illuminate\Support\Facades\DB::connection('tenant');
$site = \App\Models\Site::where('domain', 'kayacasino.top')->first();
if (!$site) { echo "Site not found!\n"; exit; }

\Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $site->db_name);
$db->reconnect();

$page = $db->table('pages')->where('slug', 'anasayfa')->first();
if (!$page) { echo "Anasayfa not found!\n"; exit; }

$content = $page->content;

// Eski slider HTML'i kaldır (v1)
$content = preg_replace(
    '/<div style="position:relative;margin-bottom:28px;">.*?<\/div>\s*<\/div>\s*<\/div>/s',
    '',
    $content
);

// Eski promosyon grid'i kaldır (v1)
$content = preg_replace(
    '/<div style="display:grid;grid-template-columns:repeat\(auto-fill,minmax\(300px,1fr\)\).*?<\/div>\s*<\/div>/s',
    '',
    $content,
    1
);

$content = trim($content);

// ─── YENİ SLIDER HTML ───
$sliderHtml = <<<'SLIDER'
<div style="background:linear-gradient(135deg,#0B1426 0%,#131B2E 50%,#0F1629 100%);border:1px solid rgba(59,130,246,0.15);border-radius:16px;padding:24px;margin-bottom:32px;box-shadow:0 8px 32px rgba(0,0,0,0.3),inset 0 1px 0 rgba(255,255,255,0.05);">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
<h2 style="font-size:20px;font-weight:700;color:#fff;margin:0;display:flex;align-items:center;gap:10px;">
<span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;background:linear-gradient(135deg,#3B82F6,#D946EF);border-radius:10px;font-size:18px;">&#127381;</span>
Güncel Kampanyalar
</h2>
<a href="/kayacasino-bonuslari" style="font-size:13px;color:#3B82F6;text-decoration:none;font-weight:600;padding:6px 14px;border:1px solid rgba(59,130,246,0.3);border-radius:8px;transition:all 0.2s;">Tümünü Gör &rarr;</a>
</div>

<div style="position:relative;border-radius:12px;overflow:hidden;">
<div style="overflow-x:auto;scroll-snap-type:x mandatory;display:flex;gap:0;-webkit-overflow-scrolling:touch;scrollbar-width:none;border-radius:12px;" class="promo-slider">
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/SLIDER.jpg" alt="Kaya Casino - Özel Promosyon" loading="eager" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/100ho-geldinbonusu-SLIDER.jpg" alt="Kaya Casino - %100 Hoş Geldin Bonusu" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/50slotyatirim-SLIDER.jpg" alt="Kaya Casino - %50 Slot Yatırım Bonusu" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/30casinodisc-SLIDER.jpg" alt="Kaya Casino - %30 Casino Discount" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/25yatirim-SLIDER.jpg" alt="Kaya Casino - %25 Yatırım Bonusu" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/ekstra-SLIDER.jpg" alt="Kaya Casino - Ekstra Bonus" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/haftalikekstra5-SLIDER.jpg" alt="Kaya Casino - Haftalık %5 Ekstra Bonus" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
</div>
</div>

<div style="display:flex;justify-content:center;gap:8px;margin-top:14px;">
<span style="width:24px;height:4px;border-radius:2px;background:linear-gradient(90deg,#3B82F6,#D946EF);display:inline-block;"></span>
<span style="width:12px;height:4px;border-radius:2px;background:rgba(255,255,255,0.2);display:inline-block;"></span>
<span style="width:12px;height:4px;border-radius:2px;background:rgba(255,255,255,0.2);display:inline-block;"></span>
<span style="width:12px;height:4px;border-radius:2px;background:rgba(255,255,255,0.2);display:inline-block;"></span>
<span style="width:12px;height:4px;border-radius:2px;background:rgba(255,255,255,0.2);display:inline-block;"></span>
<span style="width:12px;height:4px;border-radius:2px;background:rgba(255,255,255,0.2);display:inline-block;"></span>
<span style="width:12px;height:4px;border-radius:2px;background:rgba(255,255,255,0.2);display:inline-block;"></span>
</div>

</div>
SLIDER;

// ─── YENİ PROMOSYON KARTLARI HTML ───
$promoGridHtml = <<<'PROMO'
<div style="background:linear-gradient(135deg,#0B1426 0%,#131B2E 50%,#0F1629 100%);border:1px solid rgba(217,70,239,0.15);border-radius:16px;padding:24px;margin:32px 0;box-shadow:0 8px 32px rgba(0,0,0,0.3),inset 0 1px 0 rgba(255,255,255,0.05);">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
<h2 style="font-size:20px;font-weight:700;color:#fff;margin:0;display:flex;align-items:center;gap:10px;">
<span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;background:linear-gradient(135deg,#D946EF,#3B82F6);border-radius:10px;font-size:18px;">&#127873;</span>
Bonus Kampanyaları
</h2>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">

<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;border:1px solid rgba(59,130,246,0.12);transition:all 0.3s;text-decoration:none;background:#16213E;">
<img src="/storage/promotions/kayacasino/100ho-geldinbonusu-PROMOTIONS.jpg" alt="%100 Hoş Geldin Bonusu" loading="lazy" style="width:100%;display:block;" />
<div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
<span style="font-size:13px;font-weight:600;color:#e0e0e0;">%100 Hoş Geldin Bonusu</span>
<span style="font-size:11px;font-weight:700;color:#fff;background:linear-gradient(135deg,#3B82F6,#D946EF);padding:5px 12px;border-radius:6px;">DETAY</span>
</div>
</a>

<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;border:1px solid rgba(59,130,246,0.12);transition:all 0.3s;text-decoration:none;background:#16213E;">
<img src="/storage/promotions/kayacasino/50slotyatirim-PROMOTIONS.jpg" alt="%50 Slot Yatırım Bonusu" loading="lazy" style="width:100%;display:block;" />
<div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
<span style="font-size:13px;font-weight:600;color:#e0e0e0;">%50 Slot Yatırım Bonusu</span>
<span style="font-size:11px;font-weight:700;color:#fff;background:linear-gradient(135deg,#3B82F6,#D946EF);padding:5px 12px;border-radius:6px;">DETAY</span>
</div>
</a>

<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;border:1px solid rgba(59,130,246,0.12);transition:all 0.3s;text-decoration:none;background:#16213E;">
<img src="/storage/promotions/kayacasino/30casinodisc-PROMOTIONS.jpg" alt="%30 Casino Discount" loading="lazy" style="width:100%;display:block;" />
<div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
<span style="font-size:13px;font-weight:600;color:#e0e0e0;">%30 Casino Discount</span>
<span style="font-size:11px;font-weight:700;color:#fff;background:linear-gradient(135deg,#3B82F6,#D946EF);padding:5px 12px;border-radius:6px;">DETAY</span>
</div>
</a>

<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;border:1px solid rgba(59,130,246,0.12);transition:all 0.3s;text-decoration:none;background:#16213E;">
<img src="/storage/promotions/kayacasino/25yatirim-PROMOTIONS.jpg" alt="%25 Yatırım Bonusu" loading="lazy" style="width:100%;display:block;" />
<div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
<span style="font-size:13px;font-weight:600;color:#e0e0e0;">%25 Yatırım Bonusu</span>
<span style="font-size:11px;font-weight:700;color:#fff;background:linear-gradient(135deg,#3B82F6,#D946EF);padding:5px 12px;border-radius:6px;">DETAY</span>
</div>
</a>

<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;border:1px solid rgba(59,130,246,0.12);transition:all 0.3s;text-decoration:none;background:#16213E;">
<img src="/storage/promotions/kayacasino/ekstra-PROMOTIONS.jpg" alt="Ekstra Bonus" loading="lazy" style="width:100%;display:block;" />
<div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
<span style="font-size:13px;font-weight:600;color:#e0e0e0;">Ekstra Bonus</span>
<span style="font-size:11px;font-weight:700;color:#fff;background:linear-gradient(135deg,#3B82F6,#D946EF);padding:5px 12px;border-radius:6px;">DETAY</span>
</div>
</a>

<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;border:1px solid rgba(59,130,246,0.12);transition:all 0.3s;text-decoration:none;background:#16213E;">
<img src="/storage/promotions/kayacasino/haftalikekstra5-PROMOTIONS.jpg" alt="Haftalık %5 Ekstra" loading="lazy" style="width:100%;display:block;" />
<div style="padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
<span style="font-size:13px;font-weight:600;color:#e0e0e0;">Haftalık %5 Ekstra</span>
<span style="font-size:11px;font-weight:700;color:#fff;background:linear-gradient(135deg,#3B82F6,#D946EF);padding:5px 12px;border-radius:6px;">DETAY</span>
</div>
</a>

</div>
</div>
PROMO;

// İçeriğe ekle
// Slider: en başa
$newContent = $sliderHtml . "\n\n" . $content;

// Promosyon kartları: "Ödeme Yöntemleri" h2'den önce
$newContent = str_replace(
    '<h2>Ödeme Yöntemleri</h2>',
    $promoGridHtml . "\n\n" . '<h2>Ödeme Yöntemleri</h2>',
    $newContent
);

$db->table('pages')->where('id', $page->id)->update([
    'content' => $newContent,
    'updated_at' => now(),
]);

echo "Anasayfa v2 güncellendi!\n";
echo "- Slider: dark container, gradient border, başlık, dot indicator\n";
echo "- Promosyon kartları: dark card, gradient buton, başlık\n";
echo "Content length: " . strlen($newContent) . " bytes\n";
