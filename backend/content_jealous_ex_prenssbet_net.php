<?php

use App\Models\Site;
use App\Models\Post;
use App\Services\TenantManager;

$site = Site::where('domain', 'prenssbet.net')->first();
if (!$site) { echo "Site bulunamadı!\n"; return; }

app(TenantManager::class)->setTenant($site);

$imgBase = '/storage/games/the-jealous-ex';

$article = [
    'slug' => 'betsoft-the-jealous-ex-slot-inceleme-2026',
    'title' => 'Betsoft The Jealous Ex™ Slot İnceleme — Prensbet Erken Erişim 2026',
    'excerpt' => 'Betsoft\'un merakla beklenen The Jealous Ex™ slot oyunu detaylı incelemesi. Erken erişim ayrıcalığı Prensbet\'te!',
    'meta_title' => 'The Jealous Ex™ Slot İnceleme 2026 | Prensbet Erken Erişim | Betsoft',
    'meta_description' => 'Betsoft The Jealous Ex™ slot inceleme 2026. 10x6 genişleyen grid, Anger Level, 10.558x max kazanç. Prensbet erken erişim ayrıcalığı!',
    'content' => '<article>
<h1>Betsoft The Jealous Ex™ Slot İnceleme — Prensbet Erken Erişim 2026</h1>

<p>Betsoft Gaming\'in premium hikaye serisi "THE Series", kıskançlık ve tutkunun tehlikeli dansını konu alan <strong>The Jealous Ex™</strong> ile geri dönüyor. 20 Mart 2026\'daki resmi lansmandan önce bu yüksek profilli oyunu <strong>Prensbet\'te erken erişimle</strong> deneyimleme fırsatını yakalayın.</p>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Male.png" alt="The Jealous Ex erkek karakter sembolü" width="300" loading="lazy" />

<h2>Oyuna Genel Bakış</h2>

<p>The Jealous Ex™, dramatik bir anlatımı son teknoloji slot mekanikleriyle harmanlayan bir video slot deneyimidir. Standart 5x3 yapıdan başlayarak Hold & Win sırasında <strong>10x6 devasa grid</strong>\'e genişleyebilen makara sistemi, sektörde ender rastlanan bir yeniliktir.</p>

<h3>Teknik Özellikler</h3>

<table>
<thead><tr><th>Parametre</th><th>Değer</th></tr></thead>
<tbody>
<tr><td>Geliştirici</td><td>Betsoft Gaming</td></tr>
<tr><td>Seri</td><td>THE Series (Premium Hikaye Slotları)</td></tr>
<tr><td>Volatilite</td><td>Çok Yüksek (Very High)</td></tr>
<tr><td>Grid</td><td>5x3 (base) → 10x6 (Hold & Win)</td></tr>
<tr><td>Kazanma Yolu</td><td>243 Ways to Win</td></tr>
<tr><td>RTP</td><td>%96.05 (standart) / %96.18 (Buy Bonus)</td></tr>
<tr><td>Max Kazanç</td><td>10.558x bahis</td></tr>
<tr><td>Bahis Aralığı</td><td>€0.25 – €35.00</td></tr>
<tr><td>Hit Rate</td><td>%28.63</td></tr>
</tbody>
</table>

<h2>Detaylı Özellik Analizi</h2>

<h3>1. Genişleyen Grid Sistemi (5x3 → 10x6)</h3>

<p>Base game\'de klasik 5 makara 3 satır formatında oynarsınız. Asıl sihir Hold & Win tetiklendiğinde başlar: BONUS sembolleri toplayarak grid\'in kilitli bölgelerini açarsınız. Her bölge açıldığında oyun alanı büyür ve kazanç potansiyeli astronomi rakamlara ulaşır.</p>

<h3>2. Anger Level — Öfke Sistemi</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenGlass.png" alt="Kırık cam - öfke sembolü" width="200" loading="lazy" />

<p>Oyunun ismiyle birebir örtüşen bu mekanik, her BONUS sembolü geldiğinde öfke çubuğunu doldurur. Çubuk dolduğunda iki farklı sonuç tetiklenebilir:</p>

<ul>
<li><strong>Hold & Win Feature:</strong> Genişleyen grid ile büyük kazanç fırsatı</li>
<li><strong>Pick Bonus:</strong> MAJOR, MAXI, SUPER veya GRAND ödüllerinden birini kazanma şansı</li>
</ul>

<p>Bu sistem, BONUS sembollerinin sadece anlık değil <em>kümülatif</em> değer taşımasını sağlar. Her gelen BONUS bir yatırımdır — öfke birikir, patlama kaçınılmazdır.</p>

<h3>3. Triple Wild Mekanizması</h3>

<p>The Jealous Ex™\'te Wild sembolleri üç farklı formda sahneye çıkar:</p>

<img src="' . $imgBase . '/TheJealousEx_Symbol_WildFull.png" alt="The Jealous Ex tam Wild makara" width="250" loading="lazy" />

<table>
<thead><tr><th>Wild Türü</th><th>Açıklama</th><th>Tetikleme</th></tr></thead>
<tbody>
<tr><td>Standart Wild</td><td>Makara 2-5\'te görünür, BONUS hariç tüm sembollerin yerine geçer</td><td>Her spinde</td></tr>
<tr><td>Random Wilds</td><td>3-12 Wild rastgele makaralara serpilir</td><td>Her 110 spinde 1</td></tr>
<tr><td>Nudging Multiplier Wild Reel</td><td>3. makarada kısmi yığın tam Wild makaraya dönüşür + çarpan</td><td>Her 54 spinde 1</td></tr>
</tbody>
</table>

<h3>4. Stacked Mystery Sembolleri</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_MysteryBroken.png" alt="The Jealous Ex Mystery sembolü kırılma" width="200" loading="lazy" />

<p>Her makarada yığılmış Mystery sembolleri yer alabilir. Spin tamamlandığında tümü aynı sembole dönüşerek zincirleme kazanç potansiyeli yaratır. Tam ekran aynı sembol kombinasyonu için kritik bir mekaniktir.</p>

<h3>5. Hold & Win Ödül Yapısı</h3>

<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMini.png" alt="Mini ödül" width="100" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMinor.png" alt="Minor ödül" width="100" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMajor.png" alt="Major ödül" width="100" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMaxi.png" alt="Maxi ödül" width="100" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWSuper.png" alt="Super ödül" width="100" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWGrand.png" alt="Grand ödül" width="100" loading="lazy" />
</div>

<p>Hold & Win sırasında MINI\'den GRAND\'a 6 farklı ödül seviyesi mevcuttur. Grid genişledikçe gelen her yeni BONUS sembolü ödül havuzuna katkı sağlar.</p>

<h2>Tetikleme İstatistikleri</h2>

<table>
<thead><tr><th>Özellik</th><th>Tetikleme Sıklığı</th></tr></thead>
<tbody>
<tr><td>Hold & Win</td><td>Her 139 spinde 1</td></tr>
<tr><td>Nudging Wild Reel</td><td>Her 54 spinde 1</td></tr>
<tr><td>Random Wilds</td><td>Her 110 spinde 1</td></tr>
<tr><td>Second Chance</td><td>Her 168 spinde 1</td></tr>
<tr><td>Anger Level</td><td>Her 258 spinde 1</td></tr>
<tr><td>Pick Bonus</td><td>Her 442 spinde 1</td></tr>
</tbody>
</table>

<h2>Prensbet Erken Erişim Avantajı</h2>

<p>The Jealous Ex™ henüz global pazara açılmadan Prensbet üyelerine özel olarak sunulmaktadır. Bu erken erişim fırsatıyla:</p>

<ul>
<li>Oyunu rakiplerinizden önce tanıyın ve strateji geliştirin</li>
<li>İlk dönem promosyonlarından faydalanın</li>
<li>Betsoft\'un en yeni teknolojisini ilk deneyen topluluğun parçası olun</li>
</ul>

<h2>Sıkça Sorulan Sorular</h2>

<div class="faq">
<h3>The Jealous Ex™ hangi cihazlarda çalışır?</h3>
<p>Masaüstü (min 1280x720) ve mobil (min 960x540) cihazlarda sorunsuz çalışır. HTML5 tabanlıdır, ek yazılım gerektirmez.</p>

<h3>Buy Bonus ile normal tetikleme arasında fark var mı?</h3>
<p>Buy Bonus ile RTP %96.05\'ten %96.18\'e yükselir ve max kazanç faktörü 10.596x\'e çıkar. Mekanik olarak aynı Hold & Win deneyimini yaşarsınız.</p>

<h3>Anger Level her spinde dolar mı?</h3>
<p>Hayır, yalnızca makaralarda BONUS sembolü göründüğünde Anger Level\'a eklenir. Doluluk seviyesine ulaştığında otomatik tetiklenir.</p>

<h3>Oyunun boyutu ne kadar?</h3>
<p>İlk yükleme 15.3-36.2 MB, toplam oyun boyutu 25.8-47.2 MB arasındadır. 5 Mbps bağlantıda 25-58 saniye yükleme süresi beklenir.</p>
</div>

<h2>Sonuç</h2>

<p>The Jealous Ex™, Betsoft\'un slot tasarımındaki vizyonunu en üst seviyeye taşıyan bir yapım. Genişleyen grid, kümülatif Anger Level, üçlü Wild sistemi ve 10.558x max kazanç ile 2026\'nın en heyecan verici slot çıkışlarından biri olmaya aday. <strong>Prensbet erken erişim ayrıcalığıyla bu deneyimi şimdiden yaşayın!</strong></p>
</article>'
];

$existing = Post::where('slug', $article['slug'])->first();
if ($existing) { $existing->delete(); }

Post::create([
    'slug' => $article['slug'],
    'title' => $article['title'],
    'excerpt' => $article['excerpt'],
    'content' => $article['content'],
    'meta_title' => $article['meta_title'],
    'meta_description' => $article['meta_description'],
    'is_published' => true,
    'published_at' => now(),
]);
echo "OK: " . $article['slug'] . " (prenssbet.net)\n";
