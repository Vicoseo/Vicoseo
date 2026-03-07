<?php
// Anasayfaya slider ve promosyon görselleri ekle
// Çalıştır: php artisan tinker < seed_kayacasino_homepage_slider.php

$db = \Illuminate\Support\Facades\DB::connection('tenant');
$site = \App\Models\Site::where('domain', 'kayacasino.top')->first();
if (!$site) { echo "Site not found!\n"; exit; }

\Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $site->db_name);
$db->reconnect();

$page = $db->table('pages')->where('slug', 'anasayfa')->first();
if (!$page) { echo "Anasayfa not found!\n"; exit; }

$content = $page->content;

// 1. SLIDER HTML — scroll-snap carousel
$sliderHtml = '
<div style="position:relative;margin-bottom:28px;">
<div style="overflow-x:auto;scroll-snap-type:x mandatory;display:flex;gap:0;border-radius:12px;-webkit-overflow-scrolling:touch;scrollbar-width:none;" class="promo-slider">
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/SLIDER.jpg" alt="Kaya Casino - Promosyon" loading="eager" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/100ho-geldinbonusu-SLIDER.jpg" alt="Kaya Casino - %100 Hoş Geldin Bonusu" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/50slotyatirim-SLIDER.jpg" alt="Kaya Casino - %50 Slot Yatırım Bonusu" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/30casinodisc-SLIDER.jpg" alt="Kaya Casino - %30 Casino Discount" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/25yatirim-SLIDER.jpg" alt="Kaya Casino - %25 Yatırım Bonusu" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/ekstra-SLIDER.jpg" alt="Kaya Casino - Ekstra Bonus" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
<div style="scroll-snap-align:start;min-width:100%;flex-shrink:0;"><img src="/storage/promotions/kayacasino/haftalikekstra5-SLIDER.jpg" alt="Kaya Casino - Haftalık %5 Ekstra" loading="lazy" style="width:100%;display:block;border-radius:12px;" /></div>
</div>
<div style="text-align:center;margin-top:10px;display:flex;justify-content:center;gap:8px;">
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.9;"></span>
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.4;"></span>
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.4;"></span>
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.4;"></span>
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.4;"></span>
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.4;"></span>
<span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color,#6c5ce7);display:inline-block;opacity:0.4;"></span>
</div>
</div>
';

// 2. PROMOTION CARDS HTML — grid
$promoGridHtml = '
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin:24px 0;">
<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:transform 0.3s;text-decoration:none;">
<img src="/storage/promotions/kayacasino/100ho-geldinbonusu-PROMOTIONS.jpg" alt="Kaya Casino %100 Hoş Geldin Bonusu" loading="lazy" style="width:100%;display:block;" />
</a>
<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:transform 0.3s;text-decoration:none;">
<img src="/storage/promotions/kayacasino/50slotyatirim-PROMOTIONS.jpg" alt="Kaya Casino %50 Slot Yatırım Bonusu" loading="lazy" style="width:100%;display:block;" />
</a>
<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:transform 0.3s;text-decoration:none;">
<img src="/storage/promotions/kayacasino/30casinodisc-PROMOTIONS.jpg" alt="Kaya Casino %30 Casino Discount" loading="lazy" style="width:100%;display:block;" />
</a>
<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:transform 0.3s;text-decoration:none;">
<img src="/storage/promotions/kayacasino/25yatirim-PROMOTIONS.jpg" alt="Kaya Casino %25 Yatırım Bonusu" loading="lazy" style="width:100%;display:block;" />
</a>
<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:transform 0.3s;text-decoration:none;">
<img src="/storage/promotions/kayacasino/ekstra-PROMOTIONS.jpg" alt="Kaya Casino Ekstra Bonus" loading="lazy" style="width:100%;display:block;" />
</a>
<a href="/kayacasino-bonuslari" style="display:block;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:transform 0.3s;text-decoration:none;">
<img src="/storage/promotions/kayacasino/haftalikekstra5-PROMOTIONS.jpg" alt="Kaya Casino Haftalık %5 Ekstra" loading="lazy" style="width:100%;display:block;" />
</a>
</div>
';

// 3. İçeriğe ekle
// Slider: en başa (ilk <h2>'den önce)
$newContent = $sliderHtml . $content;

// Promosyon kartları: "Bonus ve Kampanyalar" bölümünden sonra, "Ödeme Yöntemleri" h2'den önce
$newContent = str_replace(
    '<h2>Ödeme Yöntemleri</h2>',
    $promoGridHtml . '<h2>Ödeme Yöntemleri</h2>',
    $newContent
);

$db->table('pages')->where('id', $page->id)->update([
    'content' => $newContent,
    'updated_at' => now(),
]);

echo "Anasayfa güncellendi!\n";
echo "- Slider: 7 banner eklendi (scroll-snap)\n";
echo "- Promosyon kartları: 6 kampanya görseli eklendi\n";
echo "Content length: " . strlen($newContent) . " bytes\n";
