<?php
// Anasayfa eksik bölümleri geri ekle + promo grid
// Regex v2 "Ödeme Yöntemleri" - "Popüler Oyunlar" arası silmiş, geri koy

$db = \Illuminate\Support\Facades\DB::connection('tenant');
$site = \App\Models\Site::where('domain', 'kayacasino.top')->first();
if (!$site) { echo "Site not found!\n"; exit; }

\Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $site->db_name);
$db->reconnect();

$page = $db->table('pages')->where('slug', 'anasayfa')->first();
if (!$page) { echo "Anasayfa not found!\n"; exit; }

$content = $page->content;

// Eksik bölümler (Bonus ve Kampanyalar'dan sonra, SSS'den önce eklenmeli)
$missingSections = '

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

<h2>Ödeme Yöntemleri</h2>

<p>Papara, kripto para, banka havalesi ve havale yöntemleriyle hızlı ve güvenli para yatırma/çekme işlemleri gerçekleştirebilirsiniz. Minimum yatırım limitleri ve çekim süreleri ödeme yöntemine göre değişiklik göstermektedir.</p>

<h2>Mobil Erişim</h2>

<p>Kaya Casino mobil uyumlu tasarımı sayesinde akıllı telefonunuz veya tabletiniz üzerinden tüm oyunlara erişebilirsiniz. Android APK desteği ve iOS tarayıcı uyumu ile her yerden oynama imkanı sunulmaktadır.</p>

<h2>Güvenlik ve Lisans</h2>

<p>Platform, 256-bit SSL şifreleme teknolojisi ile korunmaktadır. Kullanıcı verileri güvenli sunucularda saklanır ve üçüncü taraflarla paylaşılmaz. Güvenilirlik hakkında detaylı bilgi için <a href="/kayacasino-guvenilir-mi" title="Kayacasino Güvenilir Mi">güvenilirlik incelememizi</a> okuyabilirsiniz.</p>

<h2>Kayıt Nasıl Yapılır?</h2>

<ol>
<li>Kaya Casino ana sayfasında "Kayıt Ol" butonuna tıklayın</li>
<li>Ad, soyad, e-posta ve telefon bilgilerinizi girin</li>
<li>Güçlü bir şifre belirleyin</li>
<li>Kullanım koşullarını onaylayın</li>
<li>E-posta veya SMS ile hesabınızı doğrulayın</li>
<li>İlk yatırımınızı yaparak bonus avantajından yararlanın</li>
</ol>

<h2>Popüler Oyunlar</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin:20px 0;">
<div style="text-align:center;"><img src="/storage/games/kayacasino/kaya_gates_of_olympus_daily_slot.jpg" alt="Gates of Olympus - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" /><p style="margin:6px 0;font-size:14px;font-weight:600;">Gates of Olympus</p></div>
<div style="text-align:center;"><img src="/storage/games/kayacasino/kaya_sweet_bonanza_daily_slot.jpg" alt="Sweet Bonanza - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" /><p style="margin:6px 0;font-size:14px;font-weight:600;">Sweet Bonanza</p></div>
<div style="text-align:center;"><img src="/storage/games/kayacasino/kaya_gates_of_olympus_1000_daily_slot.jpg" alt="Gates of Olympus 1000 - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" /><p style="margin:6px 0;font-size:14px;font-weight:600;">Gates of Olympus 1000</p></div>
<div style="text-align:center;"><img src="/storage/games/kayacasino/kaya_sweet_bonanza_1000_daily_slot.jpg" alt="Sweet Bonanza 1000 - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" /><p style="margin:6px 0;font-size:14px;font-weight:600;">Sweet Bonanza 1000</p></div>
<div style="text-align:center;"><img src="/storage/games/kayacasino/kaya_gates_of_hades_daily_slot.jpg" alt="Gates of Hades - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" /><p style="margin:6px 0;font-size:14px;font-weight:600;">Gates of Hades</p></div>
<div style="text-align:center;"><img src="/storage/games/kayacasino/kaya_sweet_baklava_daily_slot.jpg" alt="Sweet Baklava - Kaya Casino" loading="lazy" style="width:100%;border-radius:8px;" /><p style="margin:6px 0;font-size:14px;font-weight:600;">Sweet Baklava</p></div>
</div>
';

// "Sık Sorulan Sorular" h2'den hemen önce ekle
$insertBefore = '<h2>Sık Sorulan Sorular</h2>';

if (strpos($content, $insertBefore) !== false) {
    $content = str_replace($insertBefore, $missingSections . "\n" . $insertBefore, $content);
    echo "Eksik bölümler + promo grid SSS'den önce eklendi.\n";
} else {
    // SSS başlığı bulunamazsa sonuna ekle
    $content .= $missingSections;
    echo "SSS başlığı bulunamadı, sona eklendi.\n";
}

$db->table('pages')->where('id', $page->id)->update([
    'content' => $content,
    'updated_at' => now(),
]);

echo "Content length: " . strlen($content) . " bytes\n";

// Doğrulama
$headings = [];
preg_match_all('/<h2[^>]*>([^<]+)<\/h2>/', $content, $matches);
echo "H2 başlıkları:\n";
foreach ($matches[1] as $h) {
    echo "  - $h\n";
}
