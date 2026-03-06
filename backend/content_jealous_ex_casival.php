<?php

use App\Models\Site;
use App\Models\Post;
use App\Services\TenantManager;

$site = Site::where('domain', 'casival.me')->first();
if (!$site) { echo "Site bulunamadı!\n"; return; }

app(TenantManager::class)->setTenant($site);

$imgBase = '/storage/games/the-jealous-ex';

$article = [
    'slug' => 'the-jealous-ex-yeni-slot-oyunu-casival-2026',
    'title' => 'The Jealous Ex™ — Betsoft\'un Yeni Premium Slot Oyunu Casival\'de! 2026',
    'excerpt' => 'Betsoft\'un THE Series koleksiyonunun yeni üyesi The Jealous Ex™ Casival\'de erken erişimle! Detaylı oyun rehberi ve özellikleri.',
    'meta_title' => 'The Jealous Ex™ Yeni Slot 2026 | Casival Erken Erişim | Betsoft Premium',
    'meta_description' => 'Betsoft The Jealous Ex™ yeni slot oyunu Casival\'de erken erişimle! 243 Ways, 10x6 genişleme, Hold & Win ve 10.558x max kazanç.',
    'content' => '<article>
<h1>The Jealous Ex™ — Betsoft\'un Yeni Premium Slot Oyunu Casival\'de!</h1>

<p>Slot dünyasının en yaratıcı stüdyolarından <strong>Betsoft Gaming</strong>, 2026\'nın en iddialı çıkışını hazırladı: <strong>The Jealous Ex™</strong>. Kıskançlık, öfke ve intikam temalarını sinematik kalitede bir slot deneyimine dönüştüren bu yapım, <strong>Casival\'de erken erişim</strong> ayrıcalığıyla sizleri bekliyor.</p>

<img src="' . $imgBase . '/TheJealousEx_Symbol_WildMultiplier.png" alt="The Jealous Ex Wild çarpan sembolü" width="350" loading="lazy" />

<h2>Hikaye ve Tema</h2>

<p>The Jealous Ex™, Betsoft\'un hikaye odaklı premium slot serisi "THE Series"in en cesur yapımı. Kırık bir ilişkinin ardından kontrolden çıkan duyguları konu alan oyun, her spinde gerilimi artıran mekaniklerle donatılmış. Görsel tasarım, animasyonlar ve ses efektleri ile tam bir sinematik deneyim sunuyor.</p>

<h3>Semboller ve Anlamları</h3>

<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-start;">
<div style="text-align:center;width:130px;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Female.png" alt="Kadın karakter" width="100" loading="lazy" />
<p><strong>Kadın Karakter</strong><br/>Yüksek ödeme</p>
</div>
<div style="text-align:center;width:130px;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Male.png" alt="Erkek karakter" width="100" loading="lazy" />
<p><strong>Erkek Karakter</strong><br/>Yüksek ödeme</p>
</div>
<div style="text-align:center;width:130px;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenPhone.png" alt="Kırık telefon" width="100" loading="lazy" />
<p><strong>Kırık Telefon</strong><br/>Orta ödeme</p>
</div>
<div style="text-align:center;width:130px;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Wine.png" alt="Şarap kadehi" width="100" loading="lazy" />
<p><strong>Şarap Kadehi</strong><br/>Orta ödeme</p>
</div>
<div style="text-align:center;width:130px;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Voodoo.png" alt="Voodoo bebek" width="100" loading="lazy" />
<p><strong>Voodoo Bebek</strong><br/>Orta ödeme</p>
</div>
<div style="text-align:center;width:130px;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Letter.png" alt="Yırtık mektup" width="100" loading="lazy" />
<p><strong>Yırtık Mektup</strong><br/>Düşük ödeme</p>
</div>
</div>

<h2>Oyun Mekanikleri</h2>

<h3>243 Ways to Win</h3>
<p>Geleneksel ödeme çizgileri yerine 243 kazanma yolu sistemi kullanılır. Soldan sağa ardışık makaralarda aynı sembolün herhangi bir pozisyonda gelmesi yeterlidir. Daha fazla kazanma fırsatı, daha akıcı oyun deneyimi.</p>

<h3>Hold & Win — Büyüyen Oyun Alanı</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_HWCash.png" alt="Hold & Win nakit ödül" width="200" loading="lazy" />

<p>The Jealous Ex™\'in en dikkat çekici özelliği, Hold & Win sırasında <strong>oyun alanının 5x3\'ten 10x6\'ya kadar genişlemesidir</strong>. Bu, sektörde ender görülen bir mekaniktir:</p>

<ol>
<li>Hold & Win tetiklenir ve 3 respin hakkı verilir</li>
<li>Her BONUS sembolü ödül taşır (MINI\'den GRAND\'a)</li>
<li>Yeterli BONUS toplayarak grid\'in kilitli bölümlerini açarsınız</li>
<li>Her bölüm açıldığında makara sayısı ve satır sayısı artar</li>
<li>10x6 tam açılımda kazanç potansiyeli zirveye ulaşır</li>
</ol>

<h3>Anger Level — Kümülatif Tetikleme</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenGlass.png" alt="Kırık cam - öfke" width="150" loading="lazy" />

<p>Base game\'de her BONUS sembolü makaraların üstündeki öfke çubuğuna eklenir. Bu çubuk doldukça gerilim artar ve rastgele olarak:</p>
<ul>
<li><strong>Hold & Win tetiklenir</strong> — genişleyen grid deneyimi</li>
<li><strong>Pick Bonus tetiklenir</strong> — 3 eşleştirme ile MAJOR/MAXI/SUPER/GRAND kazanma şansı</li>
</ul>

<h3>Üç Farklı Wild Türü</h3>

<table>
<thead><tr><th>Wild</th><th>Konum</th><th>Sıklık</th><th>Özellik</th></tr></thead>
<tbody>
<tr><td>Standart Wild</td><td>Reel 2-5</td><td>Her spin</td><td>BONUS hariç tüm sembollerin yerine geçer</td></tr>
<tr><td>Random Wilds</td><td>Rastgele</td><td>1/110 spin</td><td>3-12 Wild makaralara dağılır</td></tr>
<tr><td>Nudging Wild Reel</td><td>Reel 3</td><td>1/54 spin</td><td>Kısmi yığın → tam Wild makara + çarpan</td></tr>
</tbody>
</table>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Wild.png" alt="Wild sembolü" width="200" loading="lazy" />

<h3>Mystery Sembolleri</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Mystery.png" alt="Mystery sembolü" width="200" loading="lazy" />

<p>Yığılmış Mystery sembolleri spin tamamlandığında aynı sembole dönüşür. Birden fazla makarada Mystery geldiğinde, hepsi aynı sembole dönüşerek devasa kazanç zincirleri oluşturabilir.</p>

<h2>RTP ve Kazanç Analizi</h2>

<table>
<thead><tr><th>Mod</th><th>RTP</th><th>Max Kazanç</th></tr></thead>
<tbody>
<tr><td>Normal Oyun</td><td>%96.05</td><td>10.558x</td></tr>
<tr><td>Buy Bonus</td><td>%96.18</td><td>10.596x</td></tr>
</tbody>
</table>

<p>%96.05 RTP, sektör ortalaması olan %96\'nın üzerindedir. Çok yüksek volatilite, büyük kazançların nadir ama yüksek gelmesi anlamına gelir — sabırlı oyuncular için ideal.</p>

<h2>Casival Erken Erişim</h2>

<p>The Jealous Ex™, 20 Mart 2026 resmi lansmanından önce <strong>Casival üyelerine özel erken erişim</strong> ile sunulmaktadır. Avantajlar:</p>

<ul>
<li>Oyunu herkesten önce keşfetme ve deneyimleme fırsatı</li>
<li>Erken erişim dönemine özel promosyonlar</li>
<li>Betsoft\'un en yeni teknolojisini Casival\'de deneyimleyin</li>
</ul>

<h2>Sıkça Sorulan Sorular</h2>

<div class="faq">
<h3>The Jealous Ex™ demo modunda oynanabilir mi?</h3>
<p>Evet, Casival\'de çoğu slot oyununu ücretsiz demo modunda oynayabilirsiniz. Gerçek para yatırmadan oyunun mekaniklerini öğrenin.</p>

<h3>Grid tam olarak 10x6\'ya nasıl genişler?</h3>
<p>Hold & Win sırasında BONUS sembolleri toplayarak kilitli grid alanlarını açarsınız. Yeterli BONUS sembolü ile grid kademeli olarak genişler ve 10 makara 6 satıra ulaşabilir.</p>

<h3>Pick Bonus\'ta hangi ödüller var?</h3>
<p>MAJOR, MAXI, SUPER ve GRAND olmak üzere 4 ödül seviyesi vardır. 3 aynı sembolü eşleştirerek ödüllerden birini kazanırsınız. GRAND en büyük ödüldür.</p>

<h3>Oyunun volatilitesi çok yüksek — yeni başlayanlar için uygun mu?</h3>
<p>Çok yüksek volatilite, kazançların daha az sıklıkta ama daha yüksek geleceği anlamına gelir. Yeni başlayanlar minimum bahisle (€0.25) oynayarak oyunu tanıyabilir.</p>
</div>

<h2>Sonuç</h2>

<p>The Jealous Ex™, hikaye derinliği, yenilikçi mekanikler ve sinematik kalite ile 2026\'nın en dikkat çekici slot oyunlarından biri. Genişleyen grid, kümülatif Anger Level ve üçlü Wild sistemi her spini heyecan verici kılıyor. <strong>Casival erken erişim ayrıcalığıyla bu deneyimi kaçırmayın!</strong></p>
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
echo "OK: " . $article['slug'] . " (casival.me)\n";
