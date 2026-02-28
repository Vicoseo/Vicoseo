<?php
/**
 * Risebet Statik Sayfalar Seed Script
 * 6 risebet sitesine 5 statik sayfa ekler:
 * - hakkimizda, bonuslar, iletisim, canli-bahis, casino-oyunlari
 *
 * Kullanım: php seed_risebet_pages.php
 * Sunucuda çalıştırılmalı (MySQL erişimi gerekli)
 */

$host = '127.0.0.1';
$user = 'cms_user';
$pass = 'Cms@Pr0d2026!xK9';
$mainDb = 'cms_main';

$pdo = new PDO("mysql:host=$host;dbname=$mainDb;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Risebet sitelerini al (domain'de "rise" geçenler)
$sites = $pdo->query("SELECT id, domain, name, db_name FROM sites WHERE is_active = 1 AND domain LIKE '%rise%' ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

echo "Toplam " . count($sites) . " risebet sitesi bulundu.\n\n";

function getBrandName($domain) {
    $map = [
        'rise-bets.com' => 'Rise Bets',
        'risebett.me' => 'Risebett',
        'risebette.com' => 'Risebette',
        'risebets.click' => 'Rise Bets',
    ];
    return $map[$domain] ?? ucfirst(explode('.', $domain)[0]);
}

function getPages($brand, $domain) {
    $year = date('Y');
    $siteUrl = "https://{$domain}";

    return [
        // 1. Hakkımızda
        [
            'slug' => 'hakkimizda',
            'title' => "{$brand} Hakkında - Güvenilir Bahis Platformu",
            'meta_title' => "{$brand} Hakkında | Güvenilir Online Bahis & Casino {$year}",
            'meta_description' => "{$brand} hakkında detaylı bilgi. Lisanslı, güvenilir bahis ve casino platformu. Kurumsal bilgiler, güvenlik önlemleri ve neden {$brand} tercih etmelisiniz?",
            'meta_keywords' => "{$brand}, hakkında, güvenilir bahis, lisanslı casino, online bahis",
            'sort_order' => 2,
            'content' => <<<HTML
<h2>{$brand} Hakkında</h2>
<p>{$brand}, online bahis ve casino sektöründe güvenilirliğiyle öne çıkan, lisanslı ve profesyonel bir platformdur. Kurulduğu günden bu yana binlerce üyeye kesintisiz hizmet sunarak sektörde güçlü bir itibar kazanmıştır.</p>

<h2>Vizyonumuz ve Misyonumuz</h2>
<p>{$brand} olarak vizyonumuz, kullanıcılarımıza en güvenli, en eğlenceli ve en kaliteli online bahis deneyimini sunmaktır. Misyonumuz ise şeffaflık, güvenlik ve müşteri memnuniyetini her zaman ön planda tutarak sektörde öncü olmaktır.</p>
<p>Teknolojiyi yakından takip ediyor, platformumuzu sürekli geliştiriyor ve kullanıcı deneyimini en üst düzeye çıkarmak için çalışıyoruz.</p>

<h2>Lisans ve Güvenlik</h2>
<p>{$brand}, Curaçao eGaming lisansı altında faaliyet göstermektedir. Bu lisans, platformumuzun uluslararası standartlarda denetlendiğini ve adil oyun politikalarına uyduğunu garanti eder.</p>
<ul>
<li><strong>Curaçao eGaming Lisansı:</strong> Uluslararası geçerliliğe sahip, düzenli denetlenen lisans</li>
<li><strong>256-bit SSL Şifreleme:</strong> Tüm kişisel ve finansal verileriniz en üst düzeyde korunur</li>
<li><strong>Adil Oyun Garantisi:</strong> Tüm oyunlar bağımsız denetim kuruluşları tarafından test edilmektedir</li>
<li><strong>Sorumlu Bahis:</strong> Kullanıcılarımızın güvenliği ve sağlığı önceliğimizdir</li>
</ul>

<h2>Neden {$brand}?</h2>
<p>Binlerce kullanıcının {$brand}'i tercih etmesinin birçok önemli nedeni vardır:</p>

<h3>Geniş Bahis Seçenekleri</h3>
<p>{$brand}, spor bahisleri, canlı bahis, casino oyunları ve slot oyunları dahil olmak üzere kapsamlı bir oyun portföyü sunar. Futbol, basketbol, tenis, voleybol ve daha birçok spor dalında binlerce maça bahis yapabilirsiniz.</p>

<h3>Hızlı ve Güvenli Ödeme</h3>
<p>Para yatırma ve çekme işlemleriniz dakikalar içinde tamamlanır. Banka havalesi, kredi kartı, papara, kripto para ve daha birçok ödeme yöntemini destekliyoruz. Minimum yatırım tutarlarımız sektörün en düşük seviyelerindedir.</p>

<h3>7/24 Müşteri Desteği</h3>
<p>Profesyonel müşteri hizmetleri ekibimiz 7 gün 24 saat hizmetinizdedir. Canlı destek, e-posta ve Telegram kanallarımız üzerinden bize ulaşabilirsiniz.</p>

<h3>Canlı Casino Deneyimi</h3>
<p>Gerçek krupiyelerle oynanan canlı casino oyunlarımız, evinizin konforunda gerçek bir casino atmosferi yaşamanızı sağlar. Blackjack, rulet, baccarat ve poker masalarımız 7/24 aktiftir.</p>

<h3>Mobil Uyumluluk</h3>
<p>{$brand}, tüm mobil cihazlarla tam uyumludur. İster iOS ister Android kullanın, tarayıcınız üzerinden sorunsuz bir şekilde bahis yapabilir ve casino oyunları oynayabilirsiniz.</p>

<h2>{$brand} İstatistikleri</h2>
<table>
<thead>
<tr><th>Özellik</th><th>Detay</th></tr>
</thead>
<tbody>
<tr><td>Kuruluş</td><td>Online Bahis & Casino Platformu</td></tr>
<tr><td>Lisans</td><td>Curaçao eGaming</td></tr>
<tr><td>Spor Dalları</td><td>30+ spor dalı</td></tr>
<tr><td>Casino Oyunları</td><td>2.000+ oyun</td></tr>
<tr><td>Ödeme Yöntemleri</td><td>10+ yöntem</td></tr>
<tr><td>Müşteri Desteği</td><td>7/24 Canlı Destek</td></tr>
<tr><td>Mobil Uyum</td><td>iOS & Android uyumlu</td></tr>
</tbody>
</table>

<h2>Sorumlu Bahis Politikamız</h2>
<p>{$brand} olarak sorumlu bahis ilkelerini benimsiyoruz. Kullanıcılarımızın bütçelerini kontrol altında tutmalarını ve bahisi bir eğlence olarak görmelerini teşvik ediyoruz. Gerektiğinde hesap kısıtlama ve kendini dışlama seçenekleri sunuyoruz.</p>

<p>{$brand} ile güvenli, eğlenceli ve kazançlı bir bahis deneyimi için <a href="{$siteUrl}/bonuslar">bonuslarımızı</a> incelemeyi ve <a href="{$siteUrl}/iletisim">bizimle iletişime geçmeyi</a> unutmayın.</p>
HTML
        ],

        // 2. Bonuslar
        [
            'slug' => 'bonuslar',
            'title' => "{$brand} Bonusları ve Kampanyalar {$year}",
            'meta_title' => "{$brand} Bonusları & Kampanyalar {$year} | Hoşgeldin Bonusu",
            'meta_description' => "{$brand} güncel bonus kampanyaları {$year}. Hoşgeldin bonusu, kayıp bonusu, freespin ve özel promosyonlar. Bonus kuralları ve çevrim şartları.",
            'meta_keywords' => "{$brand} bonus, hoşgeldin bonusu, kayıp bonusu, freespin, kampanya",
            'sort_order' => 3,
            'content' => <<<HTML
<h2>{$brand} Bonusları ve Kampanyalar</h2>
<p>{$brand}, yeni ve mevcut üyelerine çeşitli bonus kampanyaları sunmaktadır. Aşağıda {$year} yılı güncel bonus fırsatlarımızı bulabilirsiniz.</p>

<h2>Hoşgeldin Bonusu</h2>
<p>{$brand}'e yeni üye olan kullanıcılarımız, ilk yatırımlarında cazip hoşgeldin bonusundan yararlanabilir. Bu bonus, bahis dünyasına güçlü bir başlangıç yapmanızı sağlar.</p>
<ul>
<li><strong>İlk Yatırım Bonusu:</strong> İlk yatırımınıza özel bonus</li>
<li><strong>Minimum Yatırım:</strong> Kampanya şartlarına göre değişir</li>
<li><strong>Çevrim Şartı:</strong> Bonus tutarının belirtilen kat sayısı kadar çevrilmesi gerekir</li>
<li><strong>Geçerlilik Süresi:</strong> Bonus alındıktan sonra belirtilen süre içinde kullanılmalıdır</li>
</ul>

<h2>Bonus Tablosu</h2>
<table>
<thead>
<tr><th>Bonus Türü</th><th>Açıklama</th><th>Çevrim Şartı</th></tr>
</thead>
<tbody>
<tr><td>Hoşgeldin Bonusu</td><td>İlk yatırıma özel bonus</td><td>Kampanya şartlarına göre</td></tr>
<tr><td>Kayıp Bonusu</td><td>Haftalık/aylık kayıplara bonus</td><td>Kampanya şartlarına göre</td></tr>
<tr><td>Freespin</td><td>Slot oyunlarında ücretsiz dönüş</td><td>Freespin kazançları için çevrim gerekebilir</td></tr>
<tr><td>Yatırım Bonusu</td><td>Her yatırıma özel bonus fırsatları</td><td>Kampanya şartlarına göre</td></tr>
<tr><td>Arkadaş Davet Bonusu</td><td>Arkadaşını davet et, bonus kazan</td><td>Koşullara göre değişir</td></tr>
</tbody>
</table>

<h2>Kayıp Bonusu</h2>
<p>{$brand} kayıp bonusu ile belirli dönemlerde yaşadığınız kayıpların bir kısmı hesabınıza geri yüklenir. Bu bonus, üyelerimizin kayıplarını telafi etmelerine yardımcı olmak amacıyla sunulmaktadır.</p>
<ul>
<li>Haftalık ve aylık kayıp bonusu seçenekleri mevcuttur</li>
<li>Bonus oranı ve tutarı kampanya dönemine göre değişir</li>
<li>Detaylı bilgi için canlı destek hattımızla iletişime geçebilirsiniz</li>
</ul>

<h2>Freespin Kampanyaları</h2>
<p>Slot oyunu severlere özel freespin kampanyalarımız ile ücretsiz dönüş hakkı kazanabilirsiniz. Popüler slot oyunlarında geçerli olan freespinler, ekstra kazanç şansı sunar.</p>

<h2>Özel Gün Kampanyaları</h2>
<p>{$brand}, özel günlerde ve önemli spor etkinliklerinde ekstra bonus kampanyaları düzenler. Şampiyonlar Ligi, Dünya Kupası, bayramlar ve özel günlerde kampanyalarımızı takip edin.</p>

<h2>Bonus Kuralları ve Şartları</h2>
<p>Bonuslardan yararlanırken dikkat edilmesi gereken önemli kurallar:</p>
<ul>
<li><strong>Çevrim Şartı:</strong> Her bonusun kendine özgü çevrim şartı vardır. Bonus tutarının belirtilen kat sayısı kadar bahis yapılması gerekir.</li>
<li><strong>Süre Sınırı:</strong> Bonuslar belirtilen süre içinde kullanılmalıdır, aksi halde otomatik olarak iptal edilir.</li>
<li><strong>Minimum Oran:</strong> Spor bahislerinde çevrim için minimum oran şartı uygulanabilir.</li>
<li><strong>Tek Hesap:</strong> Her kullanıcı yalnızca bir hesap açabilir. Çoklu hesap açılması durumunda bonuslar iptal edilir.</li>
<li><strong>Doğrulama:</strong> Bonus çekim işlemleri için hesap doğrulaması gerekebilir.</li>
</ul>

<p>Güncel kampanyalar ve özel bonus fırsatları hakkında bilgi almak için <a href="{$siteUrl}/iletisim">iletişim sayfamızı</a> ziyaret edebilir veya canlı destek hattımıza bağlanabilirsiniz.</p>
<p>{$brand} hakkında daha fazla bilgi almak için <a href="{$siteUrl}/hakkimizda">hakkımızda sayfamızı</a> inceleyebilirsiniz.</p>
HTML
        ],

        // 3. İletişim
        [
            'slug' => 'iletisim',
            'title' => "{$brand} İletişim - Bize Ulaşın",
            'meta_title' => "{$brand} İletişim | 7/24 Canlı Destek & Müşteri Hizmetleri",
            'meta_description' => "{$brand} iletişim bilgileri. 7/24 canlı destek, e-posta, Telegram ve sosyal medya kanalları. Sorularınız için bize hemen ulaşın.",
            'meta_keywords' => "{$brand} iletişim, canlı destek, müşteri hizmetleri, telegram, e-posta",
            'sort_order' => 4,
            'content' => <<<HTML
<h2>{$brand} İletişim</h2>
<p>{$brand} müşteri hizmetleri ekibi 7 gün 24 saat hizmetinizdedir. Her türlü soru, öneri ve şikayetleriniz için aşağıdaki kanallardan bize ulaşabilirsiniz.</p>

<h2>İletişim Kanallarımız</h2>

<h3>Canlı Destek</h3>
<p>En hızlı iletişim yöntemimiz olan canlı destek hattımız 7/24 aktiftir. Sitemiz üzerindeki canlı destek butonuna tıklayarak anında yardım alabilirsiniz. Ortalama yanıt süremiz 30 saniyenin altındadır.</p>

<h3>E-posta</h3>
<p>Detaylı sorularınız ve belgeleriniz için e-posta yoluyla bize ulaşabilirsiniz. E-postalarınıza en geç 24 saat içinde yanıt verilmektedir.</p>

<h3>Telegram</h3>
<p>Telegram kanalımız üzerinden güncel kampanyaları takip edebilir ve destek ekibimizle iletişime geçebilirsiniz.</p>

<h3>Sosyal Medya</h3>
<p>{$brand}'in resmi sosyal medya hesaplarını takip ederek güncel haberler, kampanyalar ve özel fırsatlardan haberdar olabilirsiniz.</p>

<h2>Sık Sorulan Konular</h2>

<h3>Hesap İşlemleri</h3>
<p>Hesap açma, şifre sıfırlama, hesap doğrulama ve hesap güncelleme gibi işlemler için canlı destek hattımızdan yardım alabilirsiniz.</p>

<h3>Para Yatırma ve Çekme</h3>
<p>Ödeme işlemleri ile ilgili sorunlarınız için müşteri hizmetlerimize başvurabilirsiniz. Para yatırma işlemleri genellikle anında, para çekme işlemleri ise belirtilen süre içinde tamamlanmaktadır.</p>

<h3>Bonus ve Kampanyalar</h3>
<p>Bonus kuralları, çevrim şartları ve kampanya detayları hakkında bilgi almak için <a href="{$siteUrl}/bonuslar">bonuslar sayfamızı</a> ziyaret edebilir veya canlı desteğe bağlanabilirsiniz.</p>

<h3>Teknik Sorunlar</h3>
<p>Site erişimi, oyun yüklenmesi veya diğer teknik sorunlarınız için teknik destek ekibimiz size yardımcı olacaktır.</p>

<h2>Bize Mesaj Gönderin</h2>
<p>Aşağıdaki formu doldurarak bize doğrudan mesaj gönderebilirsiniz. En kısa sürede size geri dönüş yapacağız.</p>
<!-- CONTACT_FORM -->

<p>{$brand} hakkında daha fazla bilgi için <a href="{$siteUrl}/hakkimizda">hakkımızda</a> sayfamızı ziyaret edebilirsiniz.</p>
HTML
        ],

        // 4. Canlı Bahis
        [
            'slug' => 'canli-bahis',
            'title' => "{$brand} Canlı Bahis - Maç Devam Ederken Bahis Yapın",
            'meta_title' => "{$brand} Canlı Bahis | Anlık Oranlar & Maç İçi Bahis {$year}",
            'meta_description' => "{$brand} canlı bahis bölümü ile maçlar devam ederken anlık oranlarla bahis yapın. Futbol, basketbol, tenis ve daha fazlası. Yüksek oranlar, hızlı bahis.",
            'meta_keywords' => "{$brand} canlı bahis, maç içi bahis, anlık oranlar, canlı maç",
            'sort_order' => 5,
            'content' => <<<HTML
<h2>{$brand} Canlı Bahis</h2>
<p>{$brand} canlı bahis bölümü, spor müsabakaları devam ederken anlık oranlarla bahis yapmanızı sağlar. Maç içi dinamik oranlar, detaylı istatistikler ve geniş bahis seçenekleri ile heyecan dolu bir deneyim yaşayın.</p>

<h2>Canlı Bahis Özellikleri</h2>
<ul>
<li><strong>Anlık Oran Güncellemesi:</strong> Oranlar maçın akışına göre saniyeler içinde güncellenir</li>
<li><strong>Geniş Market Seçeneği:</strong> Her maçta yüzlerce farklı bahis marketi</li>
<li><strong>Canlı İstatistikler:</strong> Maç içi istatistikler ve görsel analizler</li>
<li><strong>Hızlı Bahis:</strong> Tek tıkla bahis yapma imkanı</li>
<li><strong>Cash Out:</strong> Maç bitmeden bahsinizi kapatma seçeneği</li>
</ul>

<h2>Canlı Bahis Yapılan Spor Dalları</h2>

<h3>Futbol</h3>
<p>{$brand}'de Süper Lig, Premier Lig, La Liga, Serie A, Bundesliga ve daha birçok ligde canlı bahis yapabilirsiniz. Maç sonucu, ilk yarı/maç sonucu, toplam gol, handikap, korner ve kart bahisleri gibi geniş seçenekler mevcuttur.</p>

<h3>Basketbol</h3>
<p>NBA, EuroLeague, BSL ve dünya genelindeki basketbol liglerinde canlı bahis imkanı. Çeyrek bazlı bahisler, toplam sayı, handikap ve özel bahis seçenekleri sunulmaktadır.</p>

<h3>Tenis</h3>
<p>Grand Slam turnuvalarından ATP/WTA müsabakalarına kadar tüm tenis maçlarında set bazlı, game bazlı ve maç sonucu canlı bahis seçenekleri.</p>

<h3>Voleybol</h3>
<p>CEV Şampiyonlar Ligi, Sultanlar Ligi ve uluslararası voleybol müsabakalarında canlı bahis.</p>

<h3>Diğer Spor Dalları</h3>
<p>Hentbol, masa tenisi, badminton, buz hokeyi, beyzbol, Amerikan futbolu, MMA, boks ve daha birçok spor dalında canlı bahis seçenekleri {$brand}'de mevcuttur.</p>

<h2>Canlı Bahis Nasıl Yapılır?</h2>
<ol>
<li>{$brand} hesabınıza giriş yapın</li>
<li>Canlı bahis bölümüne gidin</li>
<li>İlgilendiğiniz maçı seçin</li>
<li>Bahis marketini ve oranı belirleyin</li>
<li>Bahis miktarınızı girin ve kuponunuzu onaylayın</li>
</ol>

<h2>Canlı Bahis İpuçları</h2>
<p>Canlı bahiste başarılı olmak için:</p>
<ul>
<li>Maçı canlı izleyerek oyunun akışını takip edin</li>
<li>İstatistikleri düzenli olarak kontrol edin</li>
<li>Cash out özelliğini doğru zamanda kullanın</li>
<li>Bütçe yönetiminize dikkat edin</li>
<li>Duygusal kararlar yerine analitik düşünün</li>
</ul>

<p>Canlı bahis deneyiminizi zenginleştirmek için <a href="{$siteUrl}/bonuslar">bonus kampanyalarımızı</a> inceleyin. Sorularınız için <a href="{$siteUrl}/iletisim">iletişim sayfamızdan</a> bize ulaşabilirsiniz.</p>
HTML
        ],

        // 5. Casino Oyunları
        [
            'slug' => 'casino-oyunlari',
            'title' => "{$brand} Casino Oyunları - Slot, Rulet, Blackjack, Poker",
            'meta_title' => "{$brand} Casino Oyunları | Slot, Rulet, Blackjack {$year}",
            'meta_description' => "{$brand} casino oyunları bölümünde 2.000+ slot, canlı rulet, blackjack, poker ve daha fazlası. En popüler casino sağlayıcılarından oyunlar.",
            'meta_keywords' => "{$brand} casino, slot oyunları, rulet, blackjack, poker, canlı casino",
            'sort_order' => 6,
            'content' => <<<HTML
<h2>{$brand} Casino Oyunları</h2>
<p>{$brand} casino bölümünde dünyanın en popüler oyun sağlayıcılarından binlerce casino oyunu sizleri bekliyor. Slot oyunlarından canlı casino masalarına, rulettten blackjack'e kadar geniş bir yelpazede eğlence ve kazanç fırsatı sunuyoruz.</p>

<h2>Slot Oyunları</h2>
<p>{$brand}'de 2.000'den fazla slot oyunu bulunmaktadır. Klasik 3 makaralı slotlardan modern video slotlara, jackpot oyunlarından megaways slotlara kadar her zevke uygun seçenek mevcuttur.</p>
<h3>Popüler Slot Sağlayıcıları</h3>
<ul>
<li><strong>Pragmatic Play:</strong> Gates of Olympus, Sweet Bonanza, Big Bass Bonanza</li>
<li><strong>NetEnt:</strong> Starburst, Gonzo's Quest, Dead or Alive</li>
<li><strong>Play'n GO:</strong> Book of Dead, Reactoonz, Fire Joker</li>
<li><strong>Microgaming:</strong> Mega Moolah, Immortal Romance, Thunderstruck II</li>
<li><strong>EGT:</strong> 40 Super Hot, Burning Hot, Flaming Hot</li>
</ul>

<h2>Canlı Casino</h2>
<p>Gerçek krupiyelerle oynanan canlı casino oyunları, evinizdeki konforunda otantik bir casino deneyimi yaşamanızı sağlar. HD kalitesinde yayınlanan masalarda profesyonel krupiyeler eşliğinde oynayın.</p>

<h3>Canlı Rulet</h3>
<p>Avrupa ruleti, Fransız ruleti ve Amerikan ruleti seçenekleri ile canlı rulet masalarında şansınızı deneyin. Lightning Roulette, Speed Roulette ve Auto Roulette gibi özel varyasyonlar da mevcuttur.</p>

<h3>Canlı Blackjack</h3>
<p>21'e en yakın eli elde etmeye çalıştığınız blackjack, casino oyunlarının en strateji odaklılarından biridir. Farklı masa limitleri ile her bütçeye uygun masalar bulabilirsiniz.</p>

<h3>Canlı Poker</h3>
<p>Texas Hold'em, Three Card Poker, Caribbean Stud ve Casino Hold'em gibi popüler poker varyasyonlarını canlı krupiyeler eşliğinde oynayabilirsiniz.</p>

<h3>Canlı Baccarat</h3>
<p>Basit kuralları ve hızlı oynanışıyla baccarat, en çok tercih edilen canlı casino oyunlarından biridir. Speed Baccarat ve Squeeze Baccarat gibi özel versiyonlar da mevcuttur.</p>

<h2>Masa Oyunları</h2>
<p>Klasik masa oyunlarını dijital ortamda deneyimleyebilirsiniz:</p>
<ul>
<li><strong>Rulet:</strong> Avrupa, Fransız ve Amerikan rulet varyasyonları</li>
<li><strong>Blackjack:</strong> Klasik, Avrupa ve Multi-hand blackjack</li>
<li><strong>Poker:</strong> Video poker çeşitleri</li>
<li><strong>Baccarat:</strong> Punto Banco ve Mini Baccarat</li>
</ul>

<h2>Güvenli Oyun Ortamı</h2>
<p>{$brand}'deki tüm casino oyunları, bağımsız test kuruluşları tarafından denetlenen RNG (Rastgele Sayı Üreteci) teknolojisi ile çalışır. Bu sayede oyun sonuçlarının tamamen adil ve rastgele olması garanti edilir.</p>

<p>Casino oyunları hakkında sorularınız için <a href="{$siteUrl}/iletisim">iletişim sayfamızı</a> ziyaret edebilirsiniz. Bonus fırsatları için <a href="{$siteUrl}/bonuslar">bonuslar sayfamıza</a> göz atın.</p>
HTML
        ],
    ];
}

$totalInserted = 0;
$now = date('Y-m-d H:i:s');

foreach ($sites as $site) {
    $db = $site['db_name'];
    $domain = $site['domain'];
    $brand = getBrandName($domain);

    echo "--- {$domain} ({$brand}) - DB: {$db} ---\n";

    $pages = getPages($brand, $domain);

    foreach ($pages as $page) {
        // Slug zaten varsa atla
        $check = $pdo->query("SELECT id FROM {$db}.pages WHERE slug = " . $pdo->quote($page['slug']))->fetch();
        if ($check) {
            echo "  [SKIP] {$page['slug']} zaten mevcut\n";
            continue;
        }

        $stmt = $pdo->prepare("INSERT INTO {$db}.pages (slug, title, content, meta_title, meta_description, meta_keywords, is_published, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)");
        $stmt->execute([
            $page['slug'],
            $page['title'],
            $page['content'],
            $page['meta_title'],
            $page['meta_description'],
            $page['meta_keywords'],
            $page['sort_order'],
            $now,
            $now,
        ]);
        echo "  [OK] {$page['slug']} eklendi\n";
        $totalInserted++;
    }

    echo "\n";
}

echo "Toplam {$totalInserted} sayfa eklendi.\n";
echo "Bitti!\n";
