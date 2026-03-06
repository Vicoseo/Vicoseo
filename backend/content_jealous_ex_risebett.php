<?php

use App\Models\Site;
use App\Models\Post;
use App\Services\TenantManager;

$site = Site::where('domain', 'risebett.me')->first();
if (!$site) { echo "Site bulunamadı!\n"; return; }

app(TenantManager::class)->setTenant($site);

$imgBase = '/storage/games/the-jealous-ex';

$article = [
    'slug' => 'the-jealous-ex-slot-erken-erisim-2026',
    'title' => 'The Jealous Ex™ Slot Oyunu — Erken Erişim Sadece RiseBet\'te! 2026',
    'excerpt' => 'Betsoft\'un yeni bombası The Jealous Ex™ slot oyunu erken erişimle sadece RiseBet\'te! 10.558x max kazanç, Hold & Win ve çok daha fazlası.',
    'meta_title' => 'The Jealous Ex™ Slot | Erken Erişim RiseBet 2026 | 10.558x Max Kazanç',
    'meta_description' => 'The Jealous Ex™ Betsoft slot oyunu erken erişimle RiseBet\'te! 5x3\'ten 10x6\'ya genişleyen makaralar, Hold & Win, 10.558x max kazanç. Hemen dene!',
    'content' => '<article>
<h1>The Jealous Ex™ Slot Oyunu — Erken Erişim Sadece RiseBet\'te!</h1>

<p><strong>Betsoft Gaming</strong>\'in premium hikaye odaklı slot serisi "THE Series"in en yeni üyesi <strong>The Jealous Ex™</strong>, 20 Mart 2026\'da global lansmanından önce <strong>erken erişim ayrıcalığıyla sadece RiseBet\'te</strong> oynanabilir! Dram, tutku ve büyük kazançların buluştuğu bu çığır açan oyunu ilk deneyenlerden olun.</p>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Female.png" alt="The Jealous Ex slot oyunu ana karakter" width="300" loading="lazy" />

<h2>The Jealous Ex™ Nedir?</h2>

<p>The Jealous Ex™, Betsoft\'un ödüllü THE Series koleksiyonuna eklenen yüksek volatiliteli bir video slot oyunudur. Kıskançlık temalı dramatik bir hikayeyi, son teknoloji oyun mekanikleriyle birleştiren bu yapım, 5 makara ve 3 satırla başlayıp Hold & Win özelliğinde <strong>10 makara ve 6 satıra</strong> kadar genişleyebilir.</p>

<h3>Oyun Kimlik Kartı</h3>

<table>
<thead><tr><th>Özellik</th><th>Detay</th></tr></thead>
<tbody>
<tr><td>Sağlayıcı</td><td>Betsoft Gaming</td></tr>
<tr><td>Oyun Tipi</td><td>Video Slot</td></tr>
<tr><td>Volatilite</td><td>Çok Yüksek</td></tr>
<tr><td>Makara Yapısı</td><td>5x3 → 10x6 (Hold & Win\'da)</td></tr>
<tr><td>Ödeme Yolları</td><td>243 Ways to Win</td></tr>
<tr><td>RTP</td><td>%96.05</td></tr>
<tr><td>Maksimum Kazanç</td><td>10.558x</td></tr>
<tr><td>Min/Max Bahis</td><td>€0.25 / €35.00</td></tr>
<tr><td>Lansman Tarihi</td><td>20 Mart 2026</td></tr>
</tbody>
</table>

<h2>Oyun Özellikleri</h2>

<h3>Hold & Win Özelliği</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_HWGrand.png" alt="The Jealous Ex Grand ödül sembolü" width="200" loading="lazy" />

<p>Oyunun kalbi olan Hold & Win özelliği, 3 respin hakkıyla başlar. BONUS sembolleri toplayarak oyun ekranının kapalı alanlarını açarsınız. Her yeni alan açıldığında kazanç potansiyeliniz katlanarak artar. MINI\'den GRAND\'a kadar ödüller sizi bekliyor!</p>

<ul>
<li><strong>Tetikleme:</strong> Her 139 spinde 1 şans</li>
<li><strong>Başlangıç:</strong> 5x3 makara ile başlar</li>
<li><strong>Genişleme:</strong> BONUS sembolleri ile 10x6\'ya kadar açılır</li>
<li><strong>Ödüller:</strong> MINI, MINOR, MAJOR, MAXI, SUPER ve GRAND</li>
</ul>

<h3>Anger Level (Öfke Seviyesi)</h3>

<p>The Jealous Ex™\'in en yenilikçi özelliği! Ana oyunda her BONUS sembolü göründüğünde, makaraların üstündeki öfke çubuğuna eklenir. Öfke seviyesi yükseldiğinde rastgele olarak <strong>Hold & Win</strong> veya <strong>Pick Bonus</strong> tetiklenir. Drama yükselir, kazanç potansiyeli artar!</p>

<ul>
<li>Tetikleme: Her 258 spinde 1 şans</li>
<li>Hold & Win veya Pick Bonus\'u rastgele tetikler</li>
</ul>

<h3>Pick Bonus</h3>

<p>3 aynı sembolü eşleştirerek büyük ödüllerden birini kazanın! MAJOR, MAXI, SUPER veya GRAND — şansınız ne kadar yüksek?</p>

<h3>Wild Özellikleri</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Wild.png" alt="The Jealous Ex Wild sembolü" width="200" loading="lazy" />

<p>The Jealous Ex™\'te Wild sembolleri birden fazla biçimde karşınıza çıkar:</p>

<ul>
<li><strong>Standart Wild:</strong> Makara 2, 3, 4 ve 5\'te görünür, BONUS dışındaki tüm sembollerin yerine geçer</li>
<li><strong>Random Wilds:</strong> Ana oyunda rastgele 3-12 Wild sembolü makaralara dağılır (her 110 spinde 1)</li>
<li><strong>Nudging Multiplier Wild Reel:</strong> 3. makarada kısmi Wild yığını, tam Wild makara oluşturmak için yukarı veya aşağı kayar (her 54 spinde 1)</li>
</ul>

<img src="' . $imgBase . '/TheJealousEx_Symbol_WildMultiplier.png" alt="The Jealous Ex Wild çarpan özelliği" width="300" loading="lazy" />

<h3>Stacked Mystery Sembolleri</h3>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Mystery.png" alt="The Jealous Ex Mystery sembolü" width="200" loading="lazy" />

<p>Her makarada yığılmış Mystery sembolleri bulunabilir. Spin tamamlandığında tümü aynı rastgele sembole dönüşür — büyük kombo kazançlar için mükemmel!</p>

<h3>Buy Bonus</h3>

<p>Beklemek istemiyor musunuz? Buy Bonus butonu ile Hold & Win özelliğine anında erişin!</p>

<h2>Sembol Galerisi</h2>

<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Female.png" alt="Kadın karakter" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Male.png" alt="Erkek karakter" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenPhone.png" alt="Kırık telefon sembolü" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Wine.png" alt="Şarap sembolü" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Letter.png" alt="Mektup sembolü" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenGlass.png" alt="Kırık cam sembolü" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Voodoo.png" alt="Voodoo bebek sembolü" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Table.png" alt="Masa sembolü" width="120" loading="lazy" />
</div>

<h2>Neden RiseBet\'te Oynamalısınız?</h2>

<ul>
<li><strong>Erken erişim ayrıcalığı:</strong> Global lansmanından önce sadece RiseBet\'te</li>
<li><strong>Yüksek RTP:</strong> %96.05 ile sektör ortalamasının üstünde</li>
<li><strong>Devasa kazanç potansiyeli:</strong> 10.558x maksimum çarpan</li>
<li><strong>Çok yüksek volatilite:</strong> Büyük kazanç arayan oyuncular için ideal</li>
<li><strong>Buy Bonus:</strong> İstediğiniz an Hold & Win\'a erişim</li>
</ul>

<h2>Sıkça Sorulan Sorular</h2>

<div class="faq">
<h3>The Jealous Ex™ ne zaman yayınlanacak?</h3>
<p>Oyunun global lansman tarihi 20 Mart 2026\'dır. Ancak RiseBet kullanıcıları erken erişim ayrıcalığıyla oyunu şimdiden deneyebilir.</p>

<h3>Maksimum kazanç ne kadar?</h3>
<p>Maksimum kazanç çarpanı 10.558x\'tir. €35 maksimum bahisle bu €369.530\'a karşılık gelir.</p>

<h3>Buy Bonus ne kadara mal olur?</h3>
<p>Buy Bonus özelliği, mevcut bahsinizin belirli bir katı olarak fiyatlandırılır. Hold & Win özelliğine anında erişim sağlar ve RTP %96.18\'e yükselir.</p>

<h3>Mobilde oynanabilir mi?</h3>
<p>Evet, The Jealous Ex™ HTML5 altyapısıyla geliştirilmiştir ve tüm mobil cihazlarda sorunsuz çalışır. Minimum önerilen çözünürlük 960x540\'tır.</p>
</div>

<h2>Sonuç</h2>

<p>The Jealous Ex™, Betsoft\'un hikaye anlatımı ve oyun mekaniğindeki ustalığını bir üst seviyeye taşıyan bir başyapıt. 5x3\'ten 10x6\'ya genişleyen makaralar, Anger Level sistemi ve 10.558x max kazançla slot dünyasında yeni bir çağ açıyor. <strong>RiseBet\'in erken erişim ayrıcalığını kaçırmayın!</strong></p>
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
echo "OK: " . $article['slug'] . " (risebett.me)\n";
