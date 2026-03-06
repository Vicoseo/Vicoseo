<?php

use App\Models\Site;
use App\Models\Post;
use App\Services\TenantManager;

$site = Site::where('domain', 'prensbetgiris.site')->first();
if (!$site) { echo "Site bulunamadı!\n"; return; }

app(TenantManager::class)->setTenant($site);

$imgBase = '/storage/games/the-jealous-ex';

$article = [
    'slug' => 'the-jealous-ex-slot-rehberi-betsoft-2026',
    'title' => 'The Jealous Ex™ Slot Rehberi — Betsoft\'un Çığır Açan Yeni Oyunu 2026',
    'excerpt' => 'The Jealous Ex™ slot oyunu tüm detaylarıyla! Anger Level, Hold & Win, Wild türleri ve kazanma stratejileri. Prensbet erken erişim.',
    'meta_title' => 'The Jealous Ex™ Slot Rehberi 2026 | Betsoft | Prensbet Erken Erişim',
    'meta_description' => 'The Jealous Ex™ Betsoft slot rehberi 2026. Anger Level mekanığı, 5x3→10x6 Hold & Win, 10.558x max kazanç. Prensbet erken erişim!',
    'content' => '<article>
<h1>The Jealous Ex™ Slot Rehberi — Betsoft\'un Çığır Açan Yeni Oyunu 2026</h1>

<p>Betsoft Gaming\'in ödüllü "THE Series" koleksiyonuna eklenen <strong>The Jealous Ex™</strong>, kıskançlığın karanlık dünyasını yüksek volatiliteli bir slot macerasına dönüştürüyor. 20 Mart 2026\'da dünyaya açılacak bu oyun, şimdiden <strong>Prensbet\'te erken erişimle</strong> oynanabilir durumda!</p>

<img src="' . $imgBase . '/TheJealousEx_Symbol_Female.png" alt="The Jealous Ex kadın karakter" width="250" loading="lazy" />

<h2>Oyun Hakkında</h2>

<p>The Jealous Ex™, 243 kazanma yollu bir video slottur. Base game\'de 5x3 formatında oynanan oyun, Hold & Win özelliğinde inanılmaz bir şekilde <strong>10 makara ve 6 satıra</strong> genişleyebilir. Çok yüksek volatilite ile büyük kazanç potansiyeli sunar.</p>

<table>
<thead><tr><th>Bilgi</th><th>Detay</th></tr></thead>
<tbody>
<tr><td>Yapımcı</td><td>Betsoft Gaming</td></tr>
<tr><td>Tür</td><td>Video Slot — Hikaye Serisi</td></tr>
<tr><td>Volatilite</td><td>Çok Yüksek</td></tr>
<tr><td>Makara</td><td>5x3 base / 10x6 Hold & Win</td></tr>
<tr><td>Kazanma Yolları</td><td>243</td></tr>
<tr><td>RTP</td><td>%96.05</td></tr>
<tr><td>Max Kazanç</td><td>10.558x bahis miktarı</td></tr>
<tr><td>Min Bahis</td><td>€0.25</td></tr>
<tr><td>Max Bahis</td><td>€35.00</td></tr>
</tbody>
</table>

<h2>Ana Özellikler Rehberi</h2>

<h3>Hold & Win — Kilitli Alanları Aç</h3>

<p>Hold & Win, The Jealous Ex™\'in merkezinde yer alır. 3 respin ile başlayan bu turda amaç, mümkün olduğunca çok BONUS sembolü toplamaktır. Her BONUS bir ödül taşır ve grid\'in kapalı bölümlerini açar.</p>

<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMini.png" alt="Mini" width="80" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMinor.png" alt="Minor" width="80" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMajor.png" alt="Major" width="80" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWMaxi.png" alt="Maxi" width="80" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWSuper.png" alt="Super" width="80" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_HWGrand.png" alt="Grand" width="80" loading="lazy" />
</div>

<p><strong>Ödül Seviyeleri:</strong> MINI → MINOR → MAJOR → MAXI → SUPER → GRAND. Grid ne kadar çok genişlerse, GRAND ödüle ulaşma şansınız o kadar artar!</p>

<h3>Anger Level — Öfke Birikimi</h3>

<p>Oyunun en özgün mekanığı. Base game boyunca gelen her BONUS sembolü, ekranın üstündeki öfke çubuğunu doldurur. Bu birikimli sistem iki kritik özelliği tetikleyebilir:</p>

<table>
<thead><tr><th>Tetiklenen</th><th>Sonuç</th><th>Sıklık</th></tr></thead>
<tbody>
<tr><td>Hold & Win</td><td>Genişleyen grid ile büyük kazanç turu</td><td>1/258 spin</td></tr>
<tr><td>Pick Bonus</td><td>3 eşleştirme ile MAJOR-GRAND ödül</td><td>1/442 spin</td></tr>
</tbody>
</table>

<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenPhone.png" alt="Kırık telefon" width="150" loading="lazy" />

<h3>Wild Sistemi — Üç Katmanlı</h3>

<p>The Jealous Ex™, slot tarihinin en zengin Wild sistemlerinden birine sahip:</p>

<h4>Standart Wild</h4>
<img src="' . $imgBase . '/TheJealousEx_Symbol_Wild.png" alt="Wild sembolü" width="150" loading="lazy" />
<p>Makara 2, 3, 4 ve 5\'te görünür. BONUS dışında tüm sembollerin yerine geçer. Kazanma yollarını tamamlamada kritik rol oynar.</p>

<h4>Random Wilds (Rastgele Wildlar)</h4>
<p>Her 110 spinde bir tetiklenen bu özellik, 3 ila 12 Wild sembolünü rastgele makaralara yerleştirir. Birden fazla kazanma yolunu aynı anda aktif edebilir.</p>

<h4>Nudging Multiplier Wild Reel</h4>
<img src="' . $imgBase . '/TheJealousEx_Symbol_WildFull.png" alt="Tam Wild makara" width="200" loading="lazy" />
<p>3. makarada özel bir mekanik: kısmi Wild yığını yukarı veya aşağı kayarak tam Wild makaraya dönüşür ve <strong>çarpan bonusu</strong> ekler. Her 54 spinde bir tetiklenir — oldukça sık!</p>

<h3>Stacked Mystery Sembolleri</h3>

<div style="display:flex;gap:12px;align-items:center;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Mystery.png" alt="Mystery" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_MysteryBroken.png" alt="Mystery açılım" width="120" loading="lazy" />
</div>

<p>Yığılmış halde gelen Mystery sembolleri, spin tamamlandıktan sonra hepsi aynı rastgele sembole dönüşür. Birden fazla makarada Mystery geldiğinde tam ekran aynı sembol kombinasyonu mümkün!</p>

<h3>Buy Bonus</h3>
<p>Sabırsız mısınız? Buy Bonus butonu ile Hold & Win\'a anında erişin. Buy Bonus modunda RTP %96.18\'e, max kazanç 10.596x\'e yükselir.</p>

<h2>Kazanma Stratejileri</h2>

<ol>
<li><strong>Bankroll yönetimi:</strong> Çok yüksek volatilite uzun kuru dönemler içerebilir. Toplam bakiyenizin %1\'ini bahis olarak belirleyin</li>
<li><strong>Anger Level\'ı takip edin:</strong> Çubuk dolmaya yakınsa bahisi artırmak değerli olabilir</li>
<li><strong>Demo modunu kullanın:</strong> Gerçek para yatırmadan önce tüm mekanikleri öğrenin</li>
<li><strong>Buy Bonus değerlendirmesi:</strong> RTP farkı (%96.18 vs %96.05) Buy Bonus\'u matematiksel olarak avantajlı kılar</li>
<li><strong>Random Wilds frekansı:</strong> Her 110 spinde 1 — bu oldukça sık tetiklenen değerli bir özellik</li>
</ol>

<h2>Görsel Galeri</h2>

<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;">
<img src="' . $imgBase . '/TheJealousEx_Symbol_Table.png" alt="Devrilmiş masa" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_BrokenGlass.png" alt="Kırık cam" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Wine.png" alt="Şarap" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Letter.png" alt="Mektup" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_Voodoo.png" alt="Voodoo" width="120" loading="lazy" />
<img src="' . $imgBase . '/TheJealousEx_Symbol_WildMultiplier.png" alt="Wild çarpan" width="120" loading="lazy" />
</div>

<h2>Sıkça Sorulan Sorular</h2>

<div class="faq">
<h3>The Jealous Ex™ ne tür bir oyun?</h3>
<p>Betsoft Gaming tarafından geliştirilen, 243 kazanma yollu, çok yüksek volatiliteli bir video slot oyunudur. THE Series premium hikaye koleksiyonunun parçasıdır.</p>

<h3>Erken erişim ne zamana kadar?</h3>
<p>Erken erişim, 20 Mart 2026 resmi lansman tarihine kadar devam eder. Bu tarihten sonra oyun tüm Betsoft platformlarında kullanılabilir olacaktır.</p>

<h3>Minimum ve maksimum bahis ne kadar?</h3>
<p>Minimum bahis €0.25, maksimum bahis €35.00\'dir. Yeni başlayanlar minimum bahisle oyunu keşfedebilir.</p>

<h3>Oyun adil mi?</h3>
<p>Evet. The Jealous Ex™, MGA (Malta Gaming Authority) lisansı altında faaliyet gösteren Betsoft tarafından geliştirilmiştir. RNG sertifikalıdır ve bağımsız denetim kuruluşları tarafından test edilmiştir.</p>
</div>

<h2>Sonuç</h2>

<p>The Jealous Ex™, öfkenin kazanca dönüştüğü, dramanın her spinde yükseldiği benzersiz bir slot macerası. Genişleyen 10x6 grid, birikimli Anger Level, üç katmanlı Wild sistemi ve 10.558x max kazanç — tüm bunlar Betsoft\'un sinematik üretim kalitesiyle birleşiyor. <strong>Prensbet\'in erken erişim ayrıcalığıyla bu eşsiz deneyimi herkesten önce yaşayın!</strong></p>
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
echo "OK: " . $article['slug'] . " (prensbetgiris.site)\n";
