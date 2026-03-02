<?php
/**
 * Seed "Giriş Rehberi 2026: Güncel Adres ve Hızlı Erişim Yöntemleri" article for all sites.
 * Each site gets a brand-customized version.
 *
 * Usage: php seed_giris_rehberi_2026.php
 */

$host = '127.0.0.1';
$user = 'cms_user';
$pass = 'Cms@Pr0d2026!xK9';
$mainDb = 'cms_main';

$pdo = new PDO("mysql:host=$host;dbname=$mainDb;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sites = $pdo->query("SELECT id, domain, name, db_name FROM sites WHERE is_active = 1 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
echo "Toplam " . count($sites) . " site bulundu.\n\n";

$now = date('Y-m-d H:i:s');

function getBrandName($domain) {
    $map = [
        'rise-bets.com' => 'Rise Bets', 'casival.me' => 'Casival',
        'ilkbahis.click' => 'İlkbahis', 'ilkbahis.link' => 'İlkbahis',
        'ilkbahisgiri.net' => 'İlkbahis Giriş', 'ilkbahisonline.com' => 'İlkbahis Online',
        'prensbet.me' => 'Prensbet', 'risebett.me' => 'Risebett',
        'rayzbet.net' => 'Rayzbet', 'prensbetgiris.online' => 'Prensbet Giriş',
        'prensbetgiris.site' => 'Prensbet Giriş',
        'prensbetgirisonline.one' => 'Prensbet Giriş Online',
        'prenssbet.com' => 'Prenssbet', 'prenssbet.net' => 'Prenssbet',
        'risebette.com' => 'Risebette', 'risebets.click' => 'Rise Bets',
        'pragmaticlive.click' => 'Pragmatic Live',
        'risespor.com' => 'Risespor',
    ];
    return $map[$domain] ?? ucfirst(explode('.', $domain)[0]);
}

function getSlugPrefix($domain) {
    $map = [
        'rise-bets.com' => 'risebets', 'casival.me' => 'casival',
        'ilkbahis.click' => 'ilkbahis', 'ilkbahis.link' => 'ilkbahis-link',
        'ilkbahisgiri.net' => 'ilkbahisgiri', 'ilkbahisonline.com' => 'ilkbahisonline',
        'prensbet.me' => 'prensbet', 'risebett.me' => 'risebett',
        'rayzbet.net' => 'rayzbet', 'prensbetgiris.online' => 'prensbetgiris',
        'prensbetgiris.site' => 'prensbetgiris-site',
        'prensbetgirisonline.one' => 'prensbetgirisonline',
        'prenssbet.com' => 'prenssbet', 'prenssbet.net' => 'prenssbet-net',
        'risebette.com' => 'risebette', 'risebets.click' => 'risebets-click',
        'pragmaticlive.click' => 'pragmaticlive',
        'risespor.com' => 'risespor',
    ];
    return $map[$domain] ?? strtolower(str_replace(['.', '-'], ['', ''], $domain));
}

/**
 * Get the short brand name (without Giriş/Online suffixes) for natural text
 */
function getShortBrand($domain) {
    $map = [
        'rise-bets.com' => 'Rise Bets', 'casival.me' => 'Casival',
        'ilkbahis.click' => 'İlkbahis', 'ilkbahis.link' => 'İlkbahis',
        'ilkbahisgiri.net' => 'İlkbahis', 'ilkbahisonline.com' => 'İlkbahis',
        'prensbet.me' => 'Prensbet', 'risebett.me' => 'Risebett',
        'rayzbet.net' => 'Rayzbet', 'prensbetgiris.online' => 'Prensbet',
        'prensbetgiris.site' => 'Prensbet',
        'prensbetgirisonline.one' => 'Prensbet',
        'prenssbet.com' => 'Prenssbet', 'prenssbet.net' => 'Prenssbet',
        'risebette.com' => 'Risebette', 'risebets.click' => 'Rise Bets',
        'pragmaticlive.click' => 'Pragmatic Live',
        'risespor.com' => 'Risespor',
    ];
    return $map[$domain] ?? ucfirst(explode('.', $domain)[0]);
}

/**
 * Get domain extension pattern for "example: brandXXXX.ext" references
 */
function getDomainExt($domain) {
    $parts = explode('.', $domain);
    return end($parts);
}

function getDomainBase($domain) {
    $parts = explode('.', $domain);
    return $parts[0];
}

function generateContent($brand, $domain, $prefix, $shortBrand) {
    $s = "https://{$domain}";
    $ext = getDomainExt($domain);
    $base = getDomainBase($domain);
    $baseLower = mb_strtolower($shortBrand, 'UTF-8');

    return <<<HTML
<h1>{$brand} Giriş Rehberi 2026: Güncel Adres ve Hızlı Erişim Yöntemleri</h1>
<p>Bu rehber <strong>bilgilendirme amaçlı hazırlanmıştır</strong>. Türkiye'deki online bahis ve casino sitelerine girişte yerel yasal düzenlemelere dikkat edin, 18 yaş sınırına ve kimlik doğrulama süreçlerine uymanız gerekir. Sorumlu bahis ilkeleri ve güvenli internet kullanımı vazgeçilmezdir.</p>
<p>{$shortBrand}, Türkiye oyuncuları için hem güvenilir hem dinamik bir bahis/casino platformu sunuyor. 2026 yılında da adres değişiklikleri, mobil uyum ve yeni erişim teknikleri ile kullanıcıların güvenli, hızlı ve sorunsuz erişimi ön planda. Buradaki rehberle pratik kayıt ve giriş adımlarından, güvenlik ipuçlarına, bonus fırsatlarına ve görece yeni mobil yöntemlere kadar kapsamlı şekilde bilgi bulacaksınız.</p>
<p>Her adres değişikliğinde sahte sitelere karşı dikkatli olun, erişim ve hesap güvenliğiniz için rehberi favorilere kaydedin ve resmi kaynakları kullanmaya özen gösterin.</p>

<h2>Giriş</h2>
<h3>{$shortBrand} Nedir? (Platformun Kısa Tanıtımı ve Lisans Bilgisi)</h3>
<p>{$shortBrand}, spor bahisleri ve casino oyunlarında, Türkiye'de sıkça tercih edilen online bir platformdur. <strong>Curaçao eGaming</strong> lisansı ile uluslararası düzeyde denetlenir ve kullanıcı güvenliği için idari, teknik ve fiziksel korumalar uygular. Site, eğlenceyi ön planda tutarken kullanıcıya şunları vadeder:</p>
<ul>
<li>30'dan fazla spor dalı ve yüksek oranlarla bahis imkânı</li>
<li>Canlı casino oyunları: Rulet, blackjack, poker, gerçek krupiyeler eşliğinde</li>
<li>2.000'ü aşkın slot oyunu ve yüksek jackpot fırsatları</li>
<li>Free Spin, hoşgeldin ve sadakat gibi farklı bonus/kampanyalar</li>
<li>Banka havalesi, kredi kartı, Papara, kripto para gibi hızlı ve çeşitli ödeme yöntemleri</li>
<li>Mobil cihazlara tam uyum (iOS ve Android tarayıcıları ile ana siteye tam erişim)</li>
<li>7/24 canlı müşteri destek servisi</li>
</ul>

<h3>Erişim Adresi Neden Değişir?</h3>
<p>Türkiye'de faaliyet gösteren online bahis ve casino platformlarında adres değişikliği sıradan bir durumdur. Temel nedenler:</p>
<ul>
<li><strong>Yasal düzenlemeler:</strong> Bilgi Teknolojileri ve İletişim Kurumu (BTK) tarafından uygulanan erişim engelleri</li>
<li><strong>Teknik güncellemeler:</strong> Mobil uyumu artırmak veya hız iyileştirmeleri</li>
<li><strong>Kullanıcı güvenliği:</strong> Eski ya da riske girmiş adreslerin değiştirilip yeni, güvenli bir domain ile devam edilmesi</li>
</ul>
<p>Sonuç: Kullanıcılar, {$shortBrand}'e her zaman en güncel ve güvenli adres üzerinden erişim sağlamalıdır.</p>

<h2>2026'nın Güncel {$shortBrand} Giriş Adresi</h2>
<h3>Güncel Adres Nedir ve Nasıl Doğrulanır?</h3>
<p>2026 yılında {$shortBrand}'e erişmek için her adres güncellemesinde resmî bağlantılar önem kazanıyor. Yukarıda belirtilen erişim nedenlerinin yanı sıra, sürekli engellemelere rağmen, platform kesintisiz hizmet sunabilmek için yeni domainlerle yayınına devam ediyor.</p>
<p><strong>GÜNCEL ADRESE ULAŞMA VE DOĞRULAMA İPUÇLARI:</strong></p>
<ul>
<li><strong>Resmi adres:</strong> {$shortBrand}'in kullandığı son aktif adres genellikle {$domain} vb. uzantılarda olur. Siteye girişte https:// protokolüne ve tarayıcının "güvenli" ibaresine dikkat edin.</li>
<li><strong>Canlı destek:</strong> Ana sayfada menüde "Canlı Destek" 7 gün 24 saat aktifse o site büyük olasılıkla orijinaldir.</li>
<li><strong>Güncel kampanya ve promosyonlar:</strong> Yeni bonus duyuruları ve banner'lar varsa güncel adrestesiniz.</li>
<li><strong>Marka ve logo kontrolleri:</strong> Renkler, logolar ve sloganlar {$shortBrand}'in resmi temasıyla örtüşmeli.</li>
<li><strong>Adres değişimi anlık olarak sosyal medya ve Telegram hesaplarında paylaşılır.</strong></li>
</ul>
<p>Tüm erişim ve doğrulama detayları için <a href="/blog/{$prefix}-guncel-giris-adresi">{$shortBrand} 2026 Güncel Adres ve Hızlı Erişim Rehberi</a> bağlantısını mutlaka inceleyin.</p>

<h3>Adres Değişikliklerinin Temel Sebepleri</h3>
<ul>
<li><strong>BTK engelleri:</strong> Türkiye'de düzenleyici kurumların erişim engelleri nedeniyle, platform zaman zaman adresini yeniler.</li>
<li><strong>Altyapı ve güvenlik ihtiyacı:</strong> Daha hızlı ve güvenli hizmet için sunucu veya altyapı değişikleri yapılır.</li>
<li><strong>Yeni kampanya ve teknik güncellemeler:</strong> Yüksek oyuncu trafiği ve promosyonları kapsayan sezonlarda geçici alan adı çözümleri devreye girer.</li>
</ul>
<p>Tüm bu sebeplerin arkasında kullanıcıların güvenli ve akıcı deneyimi önceliklidir.</p>

<h2>Hızlı ve Kolay Giriş Yöntemleri</h2>
<p>{$shortBrand}'e hem bilgisayar hem mobil cihazlardan ulaşmak için aşağıdaki pratik yöntemleri kullanabilirsiniz. Engellemeler karşısında ekstra teknik bilgisi olmayanlar bile bu adımlarla sorunu hızla çözebilir.</p>

<h3>Mobil ve Masaüstü Cihazlardan Giriş Adımları</h3>
<p>Adım adım {$shortBrand} giriş işlemi:</p>
<ol>
<li>Son güncel {$shortBrand} adresini (örneğin {$domain}) tarayıcıya yazın.</li>
<li>Ana sayfada "Giriş" veya "Kayıt Ol" butonunu bulun.</li>
<li>Kullanıcı adı ve şifreyle giriş yapın.</li>
<li>Güvenlik kodu istenirse (SMS/e-posta ile) gelen kodu ilgili alana girin.</li>
<li>Başarıyla giriş yaptığınızda bahis, casino ve promosyon bölümleri açılır.</li>
</ol>
<p>Detaylı adımlar ve karşılaşabileceğiniz diğer olasılıklar için <a href="/blog/{$prefix}-kayit-rehberi">{$shortBrand} Giriş ve Kayıt Rehberi 2026</a> sayfası faydalı olacaktır.</p>
<p><strong>Ekstra İpuçları:</strong></p>
<ul>
<li>Geçerli adresi favoriler listenize ekleyin.</li>
<li>Her giriş öncesi "https://" protokolünü kontrol edin.</li>
<li>Paralel tarayıcı pencereleriyle giriş denemeyin, tek pencerede ilerleyin.</li>
<li>Sorun yaşarsanız tarayıcı önbelleğini ve çerezleri temizlemeyi unutmayın.</li>
</ul>

<h3>VPN ve DNS Yöntemlerinin Kullanımı</h3>
<p><strong>VPN (Virtual Private Network)</strong> ve <strong>DNS (Domain Name Server) değiştirme</strong> çözümleri, ülkemizdeki geçici engellemelere karşı pratik destek sunar.</p>
<ul>
<li><strong>VPN Kullanımı:</strong> Cihazınızı yurt dışı sunucusuna bağlar, erişim engelini aşar. Ücretli ve ücretsiz VPN'ler var. Resmî kaynakların önerdiği yazılımlar dışında rastgele VPN kullanmamak önemli, güvenlik ve gizlilik riski oluşabilir.</li>
<li><strong>DNS Değiştirme:</strong> Cihazın internet adres defterini (IP çözümlemesi) değiştirerek erişim sağlanır. Özellikle bilgisayar ve Android/iOS cihazlarda DNS adresi değiştirmek oldukça basittir. "Ayarlar – Wi-Fi/İnternet Ayarları – DNS Bölümü"nden yeni değerleri tanımlayabilirsiniz.</li>
<li><strong>Tarayıcı önbelleği/Çerez temizliği:</strong> Özellikle adres engelleri sonrası geçmişte kayıtlı giriş sayfası hata verebilir. Çözüm için ayarlardan "geçmişi ve çerezleri temizle" seçeneğini kullanın ve tekrar deneyin.</li>
</ul>

<h3>Önbellek ve Çerez Temizleme Önerileri</h3>
<p>Güncel adrese rağmen erişim engeli yaşıyorsanız, çoğu zaman eski tarayıcı verileri buna sebep olur. Uygulama:</p>
<ul>
<li>Kullandığınız tarayıcıda "Geçmiş" seçeneğine girin.</li>
<li>Tüm çerezler ve önbelleği temizleyin.</li>
<li>Tarayıcıyı kapatıp tekrar açın ve yeni adresle giriş deneyin.</li>
<li>Hâlâ sonuç alamazsanız farklı bir tarayıcıda yeniden deneyin.</li>
</ul>

<h3>Erişim Yöntemlerinin Avantaj ve Dezavantajları</h3>
<table>
<thead>
<tr><th>Yöntem</th><th>Avantajlar</th><th>Dezavantajlar</th><th>2026 Uyumluluğu</th></tr>
</thead>
<tbody>
<tr><td>Güncel Adres</td><td>Hızlı, kolay, ek program gerekmez</td><td>Zaman zaman engellenebilir</td><td>Yüksek</td></tr>
<tr><td>VPN</td><td>Engellemeleri kolayca aşar</td><td>Bağlantı yavaşlayabilir, güvenlik riski</td><td>Orta (sadece gerekirse)</td></tr>
<tr><td>DNS Değiştirme</td><td>Hızlı geçici çözüm</td><td>Kalıcı çözüm olmayabilir</td><td>Orta</td></tr>
<tr><td>Mobil Tarayıcı</td><td>Her yerden erişim, ekstra indirme gerekmez</td><td>Mobil veri kotası etkilenebilir</td><td>Çok Yüksek</td></tr>
<tr><td>Mirror Siteler</td><td>Engelli dönemde kısa süreli çözüm</td><td>Resmi olmayan kaynaklar risklidir</td><td>Düşük</td></tr>
</tbody>
</table>
<p><strong>Tavsiye:</strong> Mümkün olan her durumda resmi, güncel adres ve tarayıcı üzerinden işlem yapın.</p>

<h3>Sorunlarla Karşılaşırsanız</h3>
<p>Erişim problemlerinin çözümünde izleyebileceğiniz pratik adımlar:</p>
<ul>
<li>Tarayıcıda önbellek ve çerez temizliği yapın.</li>
<li>Alternatif tarayıcı (Chrome, Firefox, Safari gibi) deneyin.</li>
<li>VPN'i aktif hale getirip tekrar siteye ulaşmayı deneyin.</li>
<li>Adresin son sürümünü kullanıp kullanmadığınızı kontrol edin.</li>
<li>Sorun hala devam ederse, <a href="/blog/{$prefix}-guncel-giris-adresi">{$shortBrand} giriş açılmıyor - Çözüm Rehberi 2026</a> üzerinden daha detaylı adımları öğrenebilirsiniz.</li>
</ul>

<h2>Güvenlik İpuçları ve Risklerden Korunma</h2>
<p>Her ne kadar {$shortBrand}, Curaçao lisansı ve güvenlik altyapısıyla kullanıcı verilerini korusa da, bireysel önlemleriniz esas öneme sahiptir. Özellikle sahte adresler, kimlik hırsızlığı ve kişisel veri risklerine karşı proaktif olun.</p>

<h3>Sahte {$shortBrand} Sitelerini Anlama Yöntemleri</h3>
<ul>
<li><strong>Alan adı ve URL</strong>: {$shortBrand}'te fazladan harf, rakam ya da farklı uzantı varsa dikkat edin.</li>
<li><strong>Güvenli bağlantı</strong>: "https://" ve tarayıcının yeşil asma kilit simgesi görünmeli.</li>
<li><strong>Tasarım ve logo</strong>: Resmi siteden farklı, kalitesiz tema veya eksik menüler varsa uzak durun.</li>
<li><strong>Promosyon içerikleri</strong>: Gerçeğe aykırı, aşırı büyük bonus vaatleri risktir.</li>
<li><strong>Canlı destek</strong>: Deneme mesajı yazın. Hızlı ve profesyonel yanıt alamıyorsanız şüphelenin.</li>
</ul>

<h3>Hesap Güvenliği İçin Alınacak Önlemler</h3>
<ul>
<li><strong>Güçlü ve benzersiz şifre yaratın</strong>: Harf, rakam ve sembol kombinasyonu kullanmanız önerilir.</li>
<li><strong>2FA (iki faktörlü doğrulama) aktif edin</strong>: Hesabınızı ikinci bir doğrulama katmanıyla koruyun.</li>
<li><strong>Şüpheli bağlantı/barındırma adreslerine giriş yapmayın.</strong></li>
<li><strong>Parolalarınızı güvenli bir parola yöneticisinde saklayın.</strong></li>
<li><strong>Hesap oturumunuzu yalnızca kendi cihazınızda açık bırakın, halka açık paylaşmayın.</strong></li>
<li><strong>Kişisel bilgilerinizi ve belgelerinizi yalnızca resmi formlar üzerinden paylaşın.</strong></li>
</ul>

<h3>İki Faktörlü Doğrulama (2FA) Hakkında Bilgi</h3>
<p>2FA güvenlik özelliği, {$shortBrand} hesabınızda giriş sırasında ek doğrulama kodu ister. Kurulumu kolaydır:</p>
<ul>
<li>"Ayarlar" veya "Güvenlik" sekmesinden 2FA etkinleştirin.</li>
<li>Kodu alacağınız yöntemi seçin (SMS, mobil uygulama, e-posta).</li>
<li>Kodla onay verin ve işlemi sonlandırın.</li>
</ul>
<p>Bu sayede, şifreniz ele geçirilmiş dahi olsa hesabınız korunacaktır. Daha ayrıntılı güvenlik için <a href="/blog/{$prefix}-lisans-guvenilirlik">{$shortBrand} Hesap Güvenliği Rehberi</a> sayfasını inceleyebilirsiniz.</p>

<h2>Sık Sorulan Sorular (SSS)</h2>
<p><strong>{$shortBrand} güncel adresi nedir?</strong><br>
Güncel {$shortBrand} adresine ulaşmak için resmî kanalları ve <a href="/blog/{$prefix}-guncel-giris-adresi">{$shortBrand} 2026 Güncel Adres ve Hızlı Erişim Rehberi</a> sayfasını kontrol edin. Arama motorunda çıkan eski sonuçlara itibar etmeyin.</p>

<p><strong>Siteye giremiyorum, ne yapmalıyım?</strong><br>
Önce tarayıcı geçmişinizi ve çerezleri temizleyin. Farklı bir tarayıcıya veya VPN/DNS gibi alternatiflere başvurabilirsiniz. Sorun devam ederse <a href="/blog/{$prefix}-guncel-giris-adresi">{$shortBrand} giriş açılmıyor - Çözüm Rehberi 2026</a> size yardımcı olacaktır.</p>

<p><strong>VPN kullanmak zorunda mıyım?</strong><br>
Sadece siteye normal yollarla erişim mümkün olmazsa VPN veya DNS kullanmak gerekebilir. Resmi VPN önerilerini dikkate almak güvenliğiniz açısından önemlidir.</p>

<p><strong>Mobil giriş nasıl yapılır?</strong><br>
Telefon veya tabletinizin tarayıcısından güncel {$shortBrand} adresine gidin. "Giriş Yap" ile hesabınıza ulaşabilirsiniz. Eğer mobilde yavaşlama yaşarsanız tarayıcı önbelleği ve çerezleri silin. Detaylı bilgi için <a href="/blog/{$prefix}-mobil-uygulama">{$shortBrand} mobil giriş rehberi</a> sayfamızı inceleyin.</p>

<p><strong>Hesabım bloke olursa ne olur?</strong><br>
Çoklu şifre hatası, şüpheli hareket veya yanlış giriş nedeniyle geçici bloke olabilirsiniz. 7/24 canlı destek ekibine başvurarak kimlik doğrulamasını tamamlayabilir ve hesabınızı tekrar aktif hale getirebilirsiniz.</p>

<p><strong>Sahte adres nasıl anlaşılır?</strong><br>
Alan adında yazım hatası, eksik/yanlış menüler, özensiz kampanya görselleri ve SSL olmaması en büyük ipuçlarıdır. Resmi güncel logoyu ve canlı desteği sorgulayın.</p>

<p><strong>Adres ne sıklıkla değişir?</strong><br>
Genellikle BTK engelleriyle birlikte ayda birkaç kez, bazen daha sık adres güncellenebilir. Her zaman {$shortBrand}'in sosyal medya/kampanya sayfalarını ve rehber bağlantılarını takip edin.</p>

<p><strong>Bonuslar girişte etkilenir mi?</strong><br>
Adresler değişse de hesabınız ve aktif bonuslar (ör.: Hoşgeldin Bonusu, Free Spin, Kayıp Bonusu) korunur. Ancak, promosyon çakışması ya da diğer şart ihlali olursa bonusunuz iptal edilebilir. Güncel kampanya ve net tüm şartlar <a href="/bonuslar">{$shortBrand} Bonus ve Kampanyalar 2026</a> sayfasında açıklanmıştır.</p>

<p><strong>Müşteri desteği nasıl alınır?</strong><br>
Ana sayfanın alt kısmından veya menüden 7/24 canlı destekle irtibata geçebilir, Telegram ve e-posta üzerinden de sorularınızı özelleştirilmiş destek ekibine iletebilirsiniz. Detaylı bilgi için <a href="/iletisim">{$shortBrand} iletişim sayfası</a>nı ziyaret edin.</p>

<p><strong>2026'da yeni özellikler var mı?</strong><br>
Mobil uyumlulukta güncellemeler, yeni ücretsiz çevrim (free spin) bonusları, artırılmış para yatırma-çekme limitleri ve gelişmiş 2FA ile güvenlik altyapıları 2026 itibarıyla yenilendi.</p>

<h2>Sonuç ve Tavsiyeler</h2>
<p>{$shortBrand}'e güvenli, sorunsuz ve hızlı erişim için en güncel adresleri takip etmek, yalnızca resmi rehber ve duyurulardan giriş yapmak kritik önemdedir. Emniyetiniz için:</p>
<ul>
<li>Son adresi favorilere eklemeyi ve sık kontrol etmeyi unutmayın.</li>
<li>Her adres değişikliğinde, tarayıcı önbelleğini ve çerezleri temizleyin.</li>
<li>Hesap şifrenizi güçlü tutun, mümkünse 2FA ile ek güvenlik oluşturun.</li>
<li>Sadece resmi adres ve kaynaklardan işlem yapın, sahte sitelere asla bilgi iletmeyin.</li>
<li>Sorumlu bahis ve yaş sınırı kurallarına titizlikle riayet edin.</li>
</ul>
<p>Kusursuz bir deneyim için {$shortBrand}'in kendi resmi rehberlerini, kampanya ve erişim duyurularını aktif takip etmeye devam edin. Platforma kaydolmak için <a href="/blog/{$prefix}-kayit-rehberi">{$shortBrand} kayıt rehberi</a> sayfamıza göz atabilirsiniz. Her zaman güncel kalın, güvenliğe öncelik verin ve erişiminizin kesintisiz olmasını sağlayın.</p>
HTML;
}

// ============================================================
// ANA DÖNGÜ
// ============================================================
$total = 0;

foreach ($sites as $site) {
    $db = $site['db_name'];
    $domain = $site['domain'];
    $brand = getBrandName($domain);
    $prefix = getSlugPrefix($domain);
    $shortBrand = getShortBrand($domain);
    $slug = "{$prefix}-giris-rehberi-2026";

    echo "=== {$domain} ({$brand}) ===\n";

    // Check if slug already exists
    $check = $pdo->query("SELECT id FROM {$db}.posts WHERE slug = " . $pdo->quote($slug))->fetch();
    if ($check) {
        echo "  [SKIP] {$slug} zaten mevcut (ID: {$check['id']})\n\n";
        continue;
    }

    $content = generateContent($brand, $domain, $prefix, $shortBrand);
    $title = "{$brand} Giriş Rehberi 2026: Güncel Adres ve Hızlı Erişim Yöntemleri";
    $metaTitle = "{$brand} Giriş Rehberi 2026 | Güncel Adres ve Erişim";
    $metaDesc = "{$brand} giriş rehberi 2026: güncel adres bilgisi, hızlı erişim yöntemleri, VPN ve DNS çözümleri, hesap güvenliği ipuçları ve SSS. Bilgilendirme amaçlı içerik.";
    $excerpt = "{$brand} platformuna güvenli ve hızlı erişim için 2026 güncel adres bilgisi, mobil ve masaüstü giriş adımları, VPN/DNS çözümleri, sahte site tespiti ve hesap güvenliği rehberi.";

    $stmt = $pdo->prepare("INSERT INTO {$db}.posts (slug, title, excerpt, content, meta_title, meta_description, is_published, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)");
    $stmt->execute([
        $slug, $title, $excerpt, $content,
        $metaTitle, $metaDesc,
        $now, $now, $now,
    ]);

    $newId = $pdo->lastInsertId();
    echo "  [OK] {$slug} eklendi (ID: {$newId}, content: " . strlen($content) . " bytes)\n\n";
    $total++;
}

echo "========================================\n";
echo "Toplam {$total} makale eklendi.\n";
echo "Bitti!\n";
