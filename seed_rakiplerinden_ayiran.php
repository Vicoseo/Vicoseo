<?php
/**
 * Seed "Rakiplerinden Ayıran Özellikler" article for all 17+ sites.
 * Each site gets a brand-customized version of this tarafsız rehber article.
 *
 * Usage: php seed_rakiplerinden_ayiran.php
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
 * Generate the Turkish accusative suffix for brand name (-'i, -'ı, -'u, -'ü, -'yi, -'yı, etc.)
 * e.g. Prensbet → Prensbet'i, Casival → Casival'ı, İlkbahis → İlkbahis'i
 */
function getBrandAccusative($brand) {
    $lower = mb_strtolower($brand, 'UTF-8');
    $lastChar = mb_substr($lower, -1, 1, 'UTF-8');
    $vowels = ['a','e','ı','i','o','ö','u','ü'];
    $backVowels = ['a','ı','o','u'];
    $frontVowels = ['e','i','ö','ü'];

    // Find last vowel
    $lastVowel = '';
    for ($i = mb_strlen($lower, 'UTF-8') - 1; $i >= 0; $i--) {
        $ch = mb_substr($lower, $i, 1, 'UTF-8');
        if (in_array($ch, $vowels)) {
            $lastVowel = $ch;
            break;
        }
    }

    $endsWithVowel = in_array($lastChar, $vowels);

    if (in_array($lastVowel, ['a', 'ı'])) {
        $suffix = $endsWithVowel ? "'yı" : "'ı";
    } elseif (in_array($lastVowel, ['e', 'i'])) {
        $suffix = $endsWithVowel ? "'yi" : "'i";
    } elseif (in_array($lastVowel, ['o', 'u'])) {
        $suffix = $endsWithVowel ? "'yu" : "'u";
    } elseif (in_array($lastVowel, ['ö', 'ü'])) {
        $suffix = $endsWithVowel ? "'yü" : "'ü";
    } else {
        $suffix = "'i"; // default
    }

    return $brand . $suffix;
}

/**
 * Generate the article slug based on brand
 */
function getArticleSlug($prefix) {
    return "{$prefix}-rakiplerinden-ayiran-ozellikler";
}

/**
 * Generate the full article content for a given brand/domain
 */
function generateArticleContent($brand, $domain, $prefix) {
    $s = "https://{$domain}";
    $brandAcc = getBrandAccusative($brand);
    $brandLower = mb_strtolower($brand, 'UTF-8');

    return <<<HTML
<p>Online bahis ve casino platformlarını değerlendirirken kullanıcıların çoğu benzer başlıklara odaklanır: <strong>güvenli erişim</strong>, <strong>oyun çeşitliliği</strong>, <strong>ödeme süreçleri</strong> ve <strong>destek kalitesi</strong>. {$brand} de bu alanda adı geçen platformlardan biridir.</p>

<div style="background:rgba(255,180,0,.10);border:1px solid rgba(255,180,0,.25);border-radius:12px;padding:14px;margin:14px 0">
<strong>Önemli uyarı:</strong> Bu içerik <strong>bilgilendirme amaçlıdır</strong>. Çevrimiçi bahis/casino işlemleri finansal kayıp riski içerir ve bulunduğunuz ülkenin mevzuatına tabi olabilir. İşlem yapmadan önce <strong>yerel yasal yükümlülüklerinizi</strong> inceleyin ve <strong>resmi kaynaklardan doğrulama</strong> yapın.
</div>

<h2>{$brand} Nedir ve Neden Farklı Görülüyor?</h2>
<p>{$brand}, spor bahisleri ve casino oyunları sunan bir platform olarak konumlanır. Kullanıcıların değerlendirme yaparken bakabileceği temel kriterler genellikle şunlardır:</p>
<ul>
<li><strong>Yerel kullanım alışkanlıklarına uyum:</strong> Türkçe arayüz ve destek, yerel ödeme yöntemlerinin bulunması.</li>
<li><strong>Oyun ve bahis çeşitliliği:</strong> Spor bahisleri, canlı bahis ve casino oyunlarının kapsamı.</li>
<li><strong>Erişim sürekliliği:</strong> Güncel adres bilgisinin düzenli paylaşılması ve teknik bilgilendirme.</li>
<li><strong>Şeffaf bilgilendirme:</strong> Kampanya şartlarının ve işlem adımlarının anlaşılır sunulması.</li>
</ul>

<div style="background:rgba(124,198,255,.10);border:1px solid rgba(124,198,255,.25);border-radius:12px;padding:14px;margin:14px 0">
<strong>Not:</strong> Her kullanıcının önceliği farklıdır. Kimi mobil deneyime, kimi ödeme hızına, kimi de oyun sağlayıcılarına daha fazla önem verir. Bu nedenle değerlendirme yaparken kendi kriterlerinizi netleştirmek faydalıdır.
</div>

<h2>Kullanıcı Deneyimi ve Arayüz</h2>
<p>Platform seçiminde arayüzün sade olması, sayfaların hızlı açılması ve mobil uyumluluk çoğu kullanıcı için belirleyicidir. {$brand} tarafında da kullanıcılar genellikle <strong>mobil erişim</strong> ve <strong>kolay gezinme</strong> konularını öne çıkarır.</p>
<ol>
<li><strong>Mobil ve masaüstü kullanım:</strong> Responsive tasarım, farklı ekranlarda daha tutarlı bir deneyim sağlayabilir. (<a href="/blog/{$prefix}-mobil-uygulama">{$brand} mobil giriş rehberi</a>)</li>
<li><strong>Erişim sorunlarında temel adımlar:</strong> Önbellek temizliği, farklı tarayıcı deneme, DNS/bağlantı kontrolü gibi basit adımlar çoğu zaman işe yarayabilir. (<a href="/blog/{$prefix}-guncel-giris-adresi">Giriş sorunları çözüm rehberi</a>)</li>
<li><strong>Güncel bağlantıyı doğrulama:</strong> Adres değişikliği yaşanabilen dönemlerde yalnızca resmi kanallardan kontrol önerilir.</li>
</ol>

<h2>Bahis Seçenekleri ve Spor Kapsamı</h2>
<p>Spor bahisleri tarafında kullanıcılar genellikle branş çeşitliliği, canlı bahis imkânı ve istatistik ekranlarının yeterliliğine bakar. Aşağıdaki tablo, kullanıcıların değerlendirmede kullanabileceği genel bir çerçeve sunar:</p>

<table>
<thead>
<tr><th>Spor Dalı</th><th>Bahis Türleri</th><th>Canlı Takip</th><th>Not</th></tr>
</thead>
<tbody>
<tr><td>Futbol</td><td>Maç önü / canlı</td><td>Genellikle mevcut</td><td>Lig ve piyasa çeşitliliği kullanıcıya göre değişebilir</td></tr>
<tr><td>Basketbol</td><td>Maç önü / canlı</td><td>Genellikle mevcut</td><td>Canlı bahislerde oranlar hızlı değişebilir</td></tr>
<tr><td>Tenis</td><td>Maç önü / canlı</td><td>Genellikle mevcut</td><td>Set bazlı piyasalar önemlidir</td></tr>
<tr><td>E-spor</td><td>Maç önü / canlı</td><td>Duruma göre</td><td>Etkinlik dönemlerinde kapsam artabilir</td></tr>
</tbody>
</table>

<div style="background:rgba(255,180,0,.10);border:1px solid rgba(255,180,0,.25);border-radius:12px;padding:14px;margin:14px 0">
<strong>Hatırlatma:</strong> Canlı bahis, hızlı karar gerektirdiği için risk seviyesi artabilir. Bütçe yönetimi ve limit belirlemek önemlidir.
</div>

<h2>Casino Oyunları ve Canlı Oyunlar</h2>
<p>Casino bölümünde kullanıcıların en çok baktığı kriterler: slot çeşitliliği, canlı masa oyunlarının kalitesi ve sağlayıcı seçenekleridir. {$brand} platformunda içerik zaman içinde değişebileceği için en doğrusu site içi "casino" bölümünden güncel listeyi kontrol etmektir.</p>
<ul>
<li>Slot oyunları ve sağlayıcı çeşitliliği</li>
<li>Canlı rulet/blackjack gibi masa oyunları</li>
<li>Dönemsel turnuva veya etkinlik sayfaları</li>
</ul>

<div style="background:rgba(124,198,255,.10);border:1px solid rgba(124,198,255,.25);border-radius:12px;padding:14px;margin:14px 0">
<strong>İpucu:</strong> Oyun seçerken RTP/volatilite gibi teknik kavramları öğrenmek, beklentinizi yönetmenize yardımcı olabilir.
</div>

<h2>Para Yatırma ve Çekme İşlemleri</h2>
<p>Ödeme süreçleri, kullanıcı memnuniyetini doğrudan etkileyen konuların başında gelir. {$brand} üzerinde değerlendirme yaparken şu noktaları kontrol etmek faydalıdır:</p>

<table>
<thead>
<tr><th>Kontrol Listesi</th><th>Neden Önemli?</th></tr>
</thead>
<tbody>
<tr><td>Minimum / maksimum işlem limitleri</td><td>Planlanan bütçeye uygunluk</td></tr>
<tr><td>Hesap doğrulama gereklilikleri</td><td>Çekim süreçlerini hızlandırabilir</td></tr>
<tr><td>İşlem süreleri</td><td>Yoğunluk dönemlerinde değişebilir</td></tr>
<tr><td>Bonus-kampanya uyumu</td><td>Ödeme yöntemine göre kısıt olabilir</td></tr>
</tbody>
</table>

<p>Detaylı limit ve süreç açıklamaları için: <a href="/blog/{$prefix}-para-yatirma">{$brand} yatırım ve çekim limitleri rehberi</a></p>

<h2>Bonuslar ve Kampanyalarda Dikkat Edilecekler</h2>
<p>Kampanyalar kullanıcılar için cazip görünse de her promosyonun kendine özgü şartları vardır. {$brand} üzerinde işlem yapmadan önce aşağıdaki maddeleri kontrol etmek, yanlış anlaşılmaları azaltır:</p>
<ul>
<li><strong>Çevrim şartı:</strong> Bonusun çekilebilir hale gelmesi için gereken işlem hacmi.</li>
<li><strong>Minimum oran / oyun kısıtı:</strong> Bazı kampanyalar belirli oran veya oyun türleriyle sınırlı olabilir.</li>
<li><strong>Süre:</strong> Kampanyanın aktif olduğu tarih aralığı.</li>
<li><strong>Ödeme yöntemi uyumu:</strong> Bazı bonuslar yalnızca belirli yöntemlerle geçerli olabilir.</li>
</ul>

<div style="background:rgba(255,180,0,.10);border:1px solid rgba(255,180,0,.25);border-radius:12px;padding:14px;margin:14px 0">
<strong>Öneri:</strong> Kampanya koşullarını okumadan işlem yapmayın. Anlamadığınız bir madde varsa canlı destekten yazılı açıklama isteyin. Güncel kampanya metinleri zamanla değişebilir.
</div>

<p>Kampanya sayfaları için: <a href="/bonuslar">{$brand} bonus ve kampanyalar</a></p>

<h2>Müşteri Desteği</h2>
<p>Canlı destek, özellikle erişim ve ödeme konularında kullanıcıların en çok başvurduğu kanaldır. {$brand} destekle iletişime geçerken şu bilgileri hazır bulundurmak süreci hızlandırabilir:</p>
<ul>
<li>İşlem tarihi ve tutarı</li>
<li>Kullanılan ödeme yöntemi</li>
<li>Varsa ekran görüntüsü / hata mesajı</li>
</ul>
<p>İletişim seçenekleri için: <a href="/iletisim">{$brand} iletişim sayfası</a></p>

<h2>Hesap Güvenliği</h2>
<p>Güvenlik, özellikle finansal işlemler yapan kullanıcılar için temel bir konudur. {$brand} kullanıcılarına genelde şu adımlar önerilir:</p>
<ul>
<li>Güçlü şifre kullanımı ve düzenli değişim</li>
<li>Mümkünse <strong>iki faktörlü doğrulama (2FA)</strong></li>
<li>Resmi adres ve SSL sertifikası kontrolü</li>
<li>Şüpheli link ve sayfalardan kaçınma</li>
</ul>
<p>Ek güvenlik rehberi: <a href="/blog/{$prefix}-lisans-guvenilirlik">{$brand} lisans ve güvenilirlik</a></p>

<h2>Sık Sorulan Sorular</h2>

<h3>{$brand}'in güncel giriş adresi neden değişebilir?</h3>
<p>Alan adı erişimi, ülke bazlı kısıtlamalar ve teknik yönlendirmeler nedeniyle zaman içinde değişebilir. Bu yüzden güncel adresi yalnızca resmi duyurular üzerinden doğrulamak önemlidir. Güncel adres bilgisi için <a href="/blog/{$prefix}-guncel-giris-adresi">{$brand} güncel giriş adresi</a> sayfamızı ziyaret edin.</p>

<h3>Bonuslar her kullanıcı için uygun mu?</h3>
<p>Kampanya şartları kullanıcı profiline, ödeme yöntemine ve dönemsel kurallara göre değişebilir. Koşulları okumadan işlem yapmak önerilmez. Detaylı bilgi için <a href="/blog/{$prefix}-deneme-bonusu">{$brand} deneme bonusu</a> sayfamıza göz atabilirsiniz.</p>

<h3>Bu içerik bir öneri veya garanti anlamına gelir mi?</h3>
<p>Hayır. Bu sayfa bilgilendirme amaçlıdır. İşlem yapmadan önce resmi metinleri okuyup yasal durumunuzu değerlendirmeniz gerekir.</p>

<h3>{$brand} para çekme süresi ne kadardır?</h3>
<p>Çekim süreleri ödeme yöntemine ve hesap doğrulama durumuna göre değişiklik gösterebilir. Hesap doğrulama adımlarını önceden tamamlamak ve talep anında işlem bilgilerini hazır bulundurmak süreci kolaylaştırabilir. Detaylı bilgi için <a href="/blog/{$prefix}-cekim-suresi">{$brand} çekim süresi</a> rehberimize bakabilirsiniz.</p>

<p><strong>Şeffaflık notu:</strong> Bu içerik bilgilendirme amaçlı hazırlanmıştır. Platform şartları, erişim adresleri ve kampanya metinleri zaman içinde değişebilir. Güncel bilgiler için resmi kaynakları kontrol edin.</p>
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
    $slug = getArticleSlug($prefix);
    $brandAcc = getBrandAccusative($brand);

    echo "=== {$domain} ({$brand}) ===\n";

    // Check if slug already exists
    $check = $pdo->query("SELECT id FROM {$db}.posts WHERE slug = " . $pdo->quote($slug))->fetch();
    if ($check) {
        echo "  [SKIP] {$slug} zaten mevcut (ID: {$check['id']})\n\n";
        continue;
    }

    $content = generateArticleContent($brand, $domain, $prefix);
    $title = "{$brandAcc} Rakiplerinden Ayıran Özellikler: Tarafsız Rehber (2026)";
    $metaTitle = "{$brandAcc} Rakiplerinden Ayıran Özellikler | Tarafsız Rehber 2026";
    $metaDesc = "{$brand} hakkında tarafsız rehber: erişim, güvenlik, mobil kullanım, ödeme süreçleri, kampanya şartları ve destek kalitesi. Bilgilendirme amaçlı içerik (2026).";
    $excerpt = "{$brandAcc} rakiplerinden ayıran özellikler neler? Güvenli erişim, oyun çeşitliliği, ödeme süreçleri, bonus kampanyaları ve müşteri desteği hakkında tarafsız rehber.";

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
