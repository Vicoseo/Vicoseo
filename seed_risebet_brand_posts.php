<?php
/**
 * Risebet Marka-Odaklı Blog Yazıları Seed Script
 * Her risebet sitesine 6 marka-keyword blog yazısı ekler
 *
 * Kullanım: php seed_risebet_brand_posts.php
 * Sunucuda çalıştırılmalı (MySQL erişimi gerekli)
 */

$host = '127.0.0.1';
$user = 'cms_user';
$pass = 'Cms@Pr0d2026!xK9';
$mainDb = 'cms_main';

$pdo = new PDO("mysql:host=$host;dbname=$mainDb;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Tüm aktif siteleri al
$sites = $pdo->query("SELECT id, domain, name, db_name FROM sites WHERE is_active = 1 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

echo "Toplam " . count($sites) . " site bulundu.\n\n";

function getBrandName($domain) {
    $map = [
        'rise-bets.com' => 'Rise Bets',
        'casival.me' => 'Casival',
        'ilkbahis.click' => 'İlkbahis',
        'ilkbahis.link' => 'İlkbahis',
        'ilkbahisgiri.net' => 'İlkbahis Giriş',
        'ilkbahisonline.com' => 'İlkbahis Online',
        'prensbet.me' => 'Prensbet',
        'risebett.me' => 'Risebett',
        'rayzbet.net' => 'Rayzbet',
        'prensbetgiris.online' => 'Prensbet Giriş',
        'prensbetgiris.site' => 'Prensbet Giriş',
        'prensbetgirisonline.one' => 'Prensbet Giriş Online',
        'prenssbet.com' => 'Prenssbet',
        'prenssbet.net' => 'Prenssbet',
        'risebette.com' => 'Risebette',
        'risebets.click' => 'Rise Bets',
        'pragmaticlive.click' => 'Pragmatic Live',
    ];
    return $map[$domain] ?? ucfirst(explode('.', $domain)[0]);
}

function getSlugPrefix($domain) {
    $map = [
        'rise-bets.com' => 'risebets',
        'casival.me' => 'casival',
        'ilkbahis.click' => 'ilkbahis',
        'ilkbahis.link' => 'ilkbahis-link',
        'ilkbahisgiri.net' => 'ilkbahisgiri',
        'ilkbahisonline.com' => 'ilkbahisonline',
        'prensbet.me' => 'prensbet',
        'risebett.me' => 'risebett',
        'rayzbet.net' => 'rayzbet',
        'prensbetgiris.online' => 'prensbetgiris',
        'prensbetgiris.site' => 'prensbetgiris-site',
        'prensbetgirisonline.one' => 'prensbetgirisonline',
        'prenssbet.com' => 'prenssbet',
        'prenssbet.net' => 'prenssbet-net',
        'risebette.com' => 'risebette',
        'risebets.click' => 'risebets-click',
        'pragmaticlive.click' => 'pragmaticlive',
    ];
    return $map[$domain] ?? strtolower(str_replace(['.', '-'], ['', ''], $domain));
}

function getBrandPosts($brand, $domain, $prefix) {
    $year = date('Y');
    $siteUrl = "https://{$domain}";
    $brandLower = mb_strtolower($brand);

    return [
        // 1. Güncel Giriş Adresi
        [
            'slug' => "{$prefix}-guncel-giris-adresi",
            'title' => "{$brand} Güncel Giriş Adresi {$year} - Yeni Link",
            'meta_title' => "{$brand} Güncel Giriş Adresi {$year} ✓ Yeni Giriş Linki",
            'meta_description' => "{$brand} güncel giriş adresi {$year}. En son {$brand} giriş linki, yeni adres bilgisi ve erişim rehberi. Güvenli giriş yap.",
            'excerpt' => "{$brand} güncel giriş adresi {$year} yılı için en son güncellenen link ve erişim bilgileri. Kesintisiz giriş rehberi.",
            'content' => <<<HTML
<h1>{$brand} Güncel Giriş Adresi {$year}</h1>

<p>{$brand} güncel giriş adresi arayanlar için en son güncellenen erişim bilgilerini bu sayfada paylaşıyoruz. BTK kararları nedeniyle zaman zaman değişen giriş adresleri, {$brand} tarafından hızlı bir şekilde yenilenmektedir.</p>

<h2>{$brand} Yeni Giriş Adresi Nasıl Bulunur?</h2>
<p>{$brand} giriş adresi değiştiğinde, yeni adrese ulaşmanın birkaç güvenilir yolu vardır:</p>
<ul>
<li><strong>Resmi web sitemiz:</strong> Bu sayfayı takip ederek en güncel adresi bulabilirsiniz</li>
<li><strong>Telegram kanalı:</strong> {$brand} resmi Telegram kanalı üzerinden anlık adres bildirimleri yapılmaktadır</li>
<li><strong>Sosyal medya:</strong> Resmi sosyal medya hesaplarımız üzerinden adres değişiklikleri duyurulur</li>
<li><strong>E-posta bildirimi:</strong> Kayıtlı üyelerimize yeni adres e-posta ile bildirilir</li>
</ul>

<h2>Neden Adres Değişir?</h2>
<p>Türkiye'deki yasal düzenlemeler gereği, online bahis sitelerinin erişim adresleri BTK (Bilgi Teknolojileri ve İletişim Kurumu) tarafından engellenebilmektedir. Bu durumda {$brand}, kesintisiz hizmet vermek için hızla yeni bir adrese taşınır.</p>

<h2>{$brand} Giriş Yaparken Dikkat Edilmesi Gerekenler</h2>
<ol>
<li><strong>Resmi kaynakları kullanın:</strong> Giriş adresini sadece resmi kanallardan öğrenin</li>
<li><strong>URL'yi kontrol edin:</strong> Tarayıcınızın adres çubuğunda SSL sertifikası (kilit ikonu) olduğundan emin olun</li>
<li><strong>Şifrenizi paylaşmayın:</strong> {$brand} çalışanları asla şifrenizi sormaz</li>
<li><strong>Güçlü şifre kullanın:</strong> Harf, rakam ve özel karakter içeren güçlü bir şifre tercih edin</li>
</ol>

<h2>{$brand} Giriş Sorunu Yaşıyorsanız</h2>
<p>Giriş yapamıyorsanız aşağıdaki adımları deneyebilirsiniz:</p>
<ul>
<li>Tarayıcı önbelleğinizi ve çerezlerinizi temizleyin</li>
<li>Farklı bir tarayıcı deneyin (Chrome, Firefox, Safari)</li>
<li>DNS ayarlarınızı değiştirin (Google DNS: 8.8.8.8)</li>
<li>VPN kullanmayı deneyin</li>
<li><a href="{$siteUrl}/iletisim">Müşteri hizmetlerimizle</a> iletişime geçin</li>
</ul>

<h2>{$brand} Mobil Giriş</h2>
<p>{$brand}'e mobil cihazınızdan da sorunsuz giriş yapabilirsiniz. Mobil tarayıcınız üzerinden güncel adresi girerek tüm bahis ve casino özelliklerine erişebilirsiniz.</p>

<p>{$brand} güncel giriş adresi için bu sayfayı yer imlerinize ekleyin. <a href="{$siteUrl}/bonuslar">Bonus kampanyalarımızı</a> da incelemeyi unutmayın.</p>
HTML
        ],

        // 2. Bonuslar ve Kampanyalar
        [
            'slug' => "{$prefix}-bonuslari-kampanyalar",
            'title' => "{$brand} Bonusları ve Kampanyalar {$year} - Güncel Fırsatlar",
            'meta_title' => "{$brand} Bonusları {$year} | Hoşgeldin Bonusu & Kampanyalar",
            'meta_description' => "{$brand} bonus kampanyaları {$year}. Hoşgeldin bonusu, kayıp bonusu, freespin, yatırım bonusu ve çevrim şartları. Tüm güncel fırsatlar.",
            'excerpt' => "{$brand} güncel bonus kampanyaları ve fırsatları. Hoşgeldin bonusu, kayıp bonusu, freespin ve özel promosyonlar.",
            'content' => <<<HTML
<h1>{$brand} Bonusları ve Kampanyalar {$year}</h1>

<p>{$brand}, üyelerine sunduğu cazip bonus kampanyalarıyla dikkat çekmektedir. {$year} yılı güncel bonus fırsatları ve kampanya detaylarını bu yazımızda sizlerle paylaşıyoruz.</p>

<h2>{$brand} Hoşgeldin Bonusu</h2>
<p>{$brand}'e yeni kayıt olan üyeler, ilk yatırımlarında özel hoşgeldin bonusundan yararlanabilir. Bu bonus, platforma güçlü bir başlangıç yapmanızı sağlar ve bahis deneyiminizi zenginleştirir.</p>
<ul>
<li>İlk yatırıma özel hoşgeldin bonusu</li>
<li>Casino ve spor bahisleri için ayrı bonus seçenekleri</li>
<li>Bonus çevrim şartları hakkında detaylı bilgi canlı destekte mevcuttur</li>
</ul>

<h2>{$brand} Yatırım Bonusu</h2>
<p>Sadece ilk yatırımda değil, sonraki yatırımlarınızda da bonus kazanma şansınız vardır. {$brand} düzenli olarak yatırım bonusu kampanyaları düzenlemektedir.</p>
<ul>
<li>Hafta içi ve hafta sonu özel yatırım bonusları</li>
<li>Belirli ödeme yöntemlerine özel ekstra bonus</li>
<li>VIP üyeler için arttırılmış bonus oranları</li>
</ul>

<h2>{$brand} Kayıp Bonusu</h2>
<p>Kayıp bonusu, belirli dönemlerde yaşadığınız kayıpların bir kısmının hesabınıza iade edilmesidir. {$brand}'de hem haftalık hem de aylık kayıp bonusu seçenekleri mevcuttur.</p>

<h2>{$brand} Freespin Kampanyaları</h2>
<p>Slot oyunlarında ücretsiz dönüş hakkı kazandıran freespin kampanyaları, {$brand}'in en popüler promosyonlarından biridir. Gates of Olympus, Sweet Bonanza gibi popüler slot oyunlarında freespin fırsatı sunulmaktadır.</p>

<h2>{$brand} Özel Gün Kampanyaları</h2>
<p>{$brand}, önemli spor etkinlikleri ve özel günlerde ekstra bonus ve kampanyalar düzenler:</p>
<ul>
<li>Şampiyonlar Ligi ve Avrupa Ligi maçlarına özel bonuslar</li>
<li>Dünya Kupası ve Avrupa Şampiyonası dönemlerinde ekstra fırsatlar</li>
<li>Bayram ve özel gün kampanyaları</li>
<li>Doğum günü bonusu</li>
</ul>

<h2>Bonus Çevrim Şartları</h2>
<p>Her bonusun kendine özgü çevrim şartları bulunmaktadır. Çevrim şartı, bonus tutarının belirtilen kat sayısı kadar bahis yapılması anlamına gelir. Detaylı bilgi için <a href="{$siteUrl}/bonuslar">bonus sayfamızı</a> ziyaret edebilir veya <a href="{$siteUrl}/iletisim">canlı desteğe</a> başvurabilirsiniz.</p>

<p>{$brand} hakkında daha fazla bilgi için <a href="{$siteUrl}/hakkimizda">hakkımızda sayfamızı</a> ziyaret edin.</p>
HTML
        ],

        // 3. Mobil Uygulama
        [
            'slug' => "{$prefix}-mobil-uygulama",
            'title' => "{$brand} Mobil Uygulama İndirme Rehberi {$year}",
            'meta_title' => "{$brand} Mobil Uygulama {$year} | iOS & Android İndirme",
            'meta_description' => "{$brand} mobil uygulama indirme rehberi {$year}. Android APK indirme, iOS kullanım, mobil site özellikleri ve mobil bahis yapma rehberi.",
            'excerpt' => "{$brand} mobil uygulama ve mobil site kullanım rehberi. Android ve iOS cihazlarda {$brand}'e nasıl erişilir?",
            'content' => <<<HTML
<h1>{$brand} Mobil Uygulama İndirme Rehberi {$year}</h1>

<p>{$brand}, mobil cihazlarla tam uyumlu platformu sayesinde her yerden bahis yapmanızı ve casino oyunları oynamanızı mümkün kılar. Bu rehberde {$brand} mobil kullanımı hakkında bilmeniz gereken her şeyi bulacaksınız.</p>

<h2>{$brand} Mobil Site</h2>
<p>{$brand} mobil sitesi, masaüstü sürümün tüm özelliklerini mobil cihazınıza taşır. Responsive tasarımı sayesinde ekran boyutunuza otomatik olarak uyum sağlar.</p>
<ul>
<li><strong>Hızlı yükleme:</strong> Optimize edilmiş altyapı ile saniyeler içinde açılır</li>
<li><strong>Tüm özellikler:</strong> Bahis, casino, canlı casino, para yatırma/çekme</li>
<li><strong>Kolay navigasyon:</strong> Mobil dostu menü ve arayüz</li>
<li><strong>Uygulama gerektirmez:</strong> Tarayıcıdan doğrudan erişim</li>
</ul>

<h2>Android Cihazlarda {$brand}</h2>
<p>Android cihazınızdan {$brand}'e erişmek için:</p>
<ol>
<li>Mobil tarayıcınızı açın (Chrome, Firefox veya Opera önerilir)</li>
<li>Güncel {$brand} adresini girin</li>
<li>Hesabınıza giriş yapın veya yeni hesap açın</li>
<li>İsterseniz "Ana ekrana ekle" seçeneği ile kısayol oluşturun</li>
</ol>

<h3>Ana Ekrana Ekleme (Android)</h3>
<ol>
<li>Chrome tarayıcıda {$brand} sitesini açın</li>
<li>Sağ üstteki üç nokta menüsüne tıklayın</li>
<li>"Ana ekrana ekle" seçeneğini seçin</li>
<li>İsim verin ve "Ekle" butonuna basın</li>
<li>Artık ana ekranınızdan tek dokunuşla erişebilirsiniz</li>
</ol>

<h2>iOS Cihazlarda {$brand} (iPhone/iPad)</h2>
<p>iPhone veya iPad'den {$brand}'e erişmek için:</p>
<ol>
<li>Safari tarayıcısını açın</li>
<li>Güncel {$brand} adresini girin</li>
<li>Alt kısımdaki paylaş butonuna dokunun</li>
<li>"Ana Ekrana Ekle" seçeneğini seçin</li>
<li>Uygulama gibi ana ekranınızdan erişin</li>
</ol>

<h2>{$brand} Mobil Özellikleri</h2>
<ul>
<li><strong>Canlı bahis:</strong> Mobilde de anlık oranlarla canlı bahis yapın</li>
<li><strong>Casino oyunları:</strong> Tüm slot ve masa oyunlarına erişim</li>
<li><strong>Canlı casino:</strong> Gerçek krupiyelerle mobil canlı casino</li>
<li><strong>Para işlemleri:</strong> Mobilde güvenli yatırım ve çekim</li>
<li><strong>Canlı destek:</strong> 7/24 mobil canlı destek</li>
<li><strong>Push bildirimler:</strong> Kampanya ve sonuç bildirimleri</li>
</ul>

<h2>Mobil Bahis İpuçları</h2>
<ul>
<li>Güvenli Wi-Fi veya mobil veri bağlantısı kullanın</li>
<li>Tarayıcınızı güncel tutun</li>
<li>Otomatik doldurma özelliğini kullanarak hızlı giriş yapın</li>
<li>Bütçe yönetiminizi mobilde de takip edin</li>
</ul>

<p>Mobil erişimle ilgili sorunlar için <a href="{$siteUrl}/iletisim">iletişim sayfamızdan</a> destek alabilirsiniz. <a href="{$siteUrl}/bonuslar">Mobil özel bonus kampanyalarımızı</a> da incelemeyi unutmayın.</p>
HTML
        ],

        // 4. Lisans ve Güvenilirlik
        [
            'slug' => "{$prefix}-lisans-guvenilirlik",
            'title' => "{$brand} Lisans Bilgisi ve Güvenilirlik Analizi {$year}",
            'meta_title' => "{$brand} Güvenilir Mi? Lisans Bilgisi & Güvenlik {$year}",
            'meta_description' => "{$brand} güvenilir mi? Lisans bilgisi, güvenlik önlemleri, ödeme güvencesi ve kullanıcı yorumları. {$brand} hakkında bilmeniz gereken her şey.",
            'excerpt' => "{$brand} güvenilir mi? Lisans bilgileri, güvenlik altyapısı ve kullanıcı deneyimleri hakkında detaylı analiz.",
            'content' => <<<HTML
<h1>{$brand} Lisans Bilgisi ve Güvenilirlik {$year}</h1>

<p>Online bahis platformu seçerken güvenilirlik en önemli kriterlerden biridir. Bu yazımızda {$brand}'in lisans bilgileri, güvenlik altyapısı ve güvenilirlik analizini detaylı olarak ele alıyoruz.</p>

<h2>{$brand} Lisanslı Mı?</h2>
<p>{$brand}, Curaçao eGaming lisansı altında faaliyet göstermektedir. Curaçao lisansı, dünya genelinde en yaygın kabul gören online bahis lisanslarından biridir ve platformun uluslararası standartlarda denetlendiğini gösterir.</p>
<ul>
<li><strong>Lisans Türü:</strong> Curaçao eGaming</li>
<li><strong>Denetim:</strong> Düzenli olarak bağımsız kuruluşlar tarafından denetlenmektedir</li>
<li><strong>Adil Oyun:</strong> RNG (Rastgele Sayı Üreteci) sertifikalı oyunlar</li>
</ul>

<h2>{$brand} Güvenlik Önlemleri</h2>

<h3>SSL Şifreleme</h3>
<p>{$brand}, 256-bit SSL (Secure Socket Layer) şifreleme teknolojisi kullanmaktadır. Bu teknoloji, kişisel bilgileriniz ve finansal verilerinizin üçüncü taraflarca ele geçirilmesini önler.</p>

<h3>Veri Koruma</h3>
<p>Kişisel verileriniz GDPR standartlarına uygun olarak saklanır ve işlenir. Verileriniz hiçbir koşulda üçüncü taraflarla paylaşılmaz.</p>

<h3>Hesap Güvenliği</h3>
<ul>
<li>Güçlü şifre gereksinimleri</li>
<li>Hesap doğrulama prosedürleri</li>
<li>Şüpheli aktivite izleme</li>
<li>Oturum güvenliği ve otomatik çıkış</li>
</ul>

<h2>{$brand} Ödeme Güvencesi</h2>
<p>{$brand}'in ödeme güvenilirliği konusunda bilinmesi gerekenler:</p>
<ul>
<li><strong>Hızlı ödemeler:</strong> Para çekme talepleri belirtilen süre içinde işleme alınır</li>
<li><strong>Çoklu ödeme yöntemleri:</strong> Banka havalesi, papara, kripto para ve daha fazlası</li>
<li><strong>Minimum çekim:</strong> Sektörde rekabetçi minimum çekim limitleri</li>
<li><strong>Şeffaf kurallar:</strong> Çekim kuralları ve limitleri açıkça belirtilmiştir</li>
</ul>

<h2>{$brand} Müşteri Memnuniyeti</h2>
<p>{$brand}, müşteri memnuniyetini ön planda tutan yaklaşımıyla öne çıkar:</p>
<ul>
<li>7/24 Türkçe canlı destek</li>
<li>Hızlı sorun çözümü</li>
<li>Düzenli kampanya ve bonuslar</li>
<li>Kullanıcı dostu arayüz</li>
</ul>

<h2>Sonuç</h2>
<p>{$brand}, Curaçao lisansı, güçlü güvenlik altyapısı, hızlı ödeme sistemi ve profesyonel müşteri hizmetleri ile güvenilir bir online bahis platformudur. Platforma güvenle kayıt olabilir ve hizmetlerinden yararlanabilirsiniz.</p>

<p>Detaylı bilgi için <a href="{$siteUrl}/hakkimizda">hakkımızda sayfamızı</a> ziyaret edebilirsiniz. Sorularınız için <a href="{$siteUrl}/iletisim">iletişim sayfamızdan</a> bize ulaşın.</p>
HTML
        ],

        // 5. Müşteri Hizmetleri
        [
            'slug' => "{$prefix}-musteri-hizmetleri",
            'title' => "{$brand} Müşteri Hizmetleri İletişim Rehberi {$year}",
            'meta_title' => "{$brand} Müşteri Hizmetleri | 7/24 Canlı Destek İletişim",
            'meta_description' => "{$brand} müşteri hizmetleri iletişim bilgileri. 7/24 canlı destek, Telegram, e-posta. Hızlı ve profesyonel müşteri desteği.",
            'excerpt' => "{$brand} müşteri hizmetleri ve iletişim kanalları. 7/24 canlı destek, Telegram ve e-posta ile nasıl ulaşılır?",
            'content' => <<<HTML
<h1>{$brand} Müşteri Hizmetleri İletişim {$year}</h1>

<p>{$brand} müşteri hizmetleri, kullanıcılarına 7 gün 24 saat profesyonel destek sunmaktadır. Her türlü soru, sorun ve öneriniz için birçok farklı kanaldan bize ulaşabilirsiniz.</p>

<h2>{$brand} Canlı Destek</h2>
<p>En hızlı ve en pratik iletişim yöntemimiz canlı destek hattımızdır. {$brand} sitesi üzerindeki canlı destek butonuna tıklayarak anında bir müşteri temsilcisiyle görüşebilirsiniz.</p>
<ul>
<li><strong>Hizmet saati:</strong> 7/24 kesintisiz</li>
<li><strong>Ortalama yanıt süresi:</strong> 30 saniyenin altında</li>
<li><strong>Dil:</strong> Türkçe</li>
<li><strong>Konular:</strong> Tüm konularda yardım</li>
</ul>

<h2>{$brand} Telegram Desteği</h2>
<p>{$brand} resmi Telegram kanalı üzerinden:</p>
<ul>
<li>Güncel giriş adreslerini öğrenebilirsiniz</li>
<li>Bonus kampanyalarını takip edebilirsiniz</li>
<li>Destek ekibine mesaj gönderebilirsiniz</li>
<li>Anlık bildirimler alabilirsiniz</li>
</ul>

<h2>{$brand} E-posta Desteği</h2>
<p>Detaylı konular, belge gönderimi veya resmi yazışmalar için e-posta kanalımızı kullanabilirsiniz. E-postalarınıza en geç 24 saat içinde yanıt verilmektedir.</p>

<h2>Sık Sorulan Sorular ve Çözümler</h2>

<h3>Hesap Sorunları</h3>
<p><strong>Şifre sıfırlama:</strong> Giriş sayfasındaki "Şifremi Unuttum" linkini kullanarak şifrenizi sıfırlayabilirsiniz. Sorun devam ederse canlı desteğe bağlanın.</p>
<p><strong>Hesap doğrulama:</strong> Kimlik doğrulama işlemi için gerekli belgeleri canlı destek üzerinden veya e-posta ile gönderebilirsiniz.</p>

<h3>Ödeme Sorunları</h3>
<p><strong>Para yatırma:</strong> Yatırım işleminiz tamamlanmadıysa, önce banka/ödeme sağlayıcınızla kontrol edin, ardından canlı desteğe başvurun.</p>
<p><strong>Para çekme:</strong> Çekim talebiniz bekliyorsa, hesap doğrulamanızın tamamlandığından ve çevrim şartlarının yerine getirildiğinden emin olun.</p>

<h3>Bonus Sorunları</h3>
<p><strong>Bonus yansımadı:</strong> Bonus otomatik yansımadıysa canlı destekten talep edebilirsiniz.</p>
<p><strong>Çevrim bilgisi:</strong> Aktif bonusunuzun çevrim durumunu hesabınızdan veya canlı destekten öğrenebilirsiniz.</p>

<h3>Teknik Sorunlar</h3>
<p><strong>Site açılmıyor:</strong> <a href="{$siteUrl}/blog/{$prefix}-guncel-giris-adresi">Güncel giriş adresi</a> sayfamızdan en son adresi kontrol edin.</p>
<p><strong>Oyun yüklenmiyor:</strong> Tarayıcınızı güncelleyin, önbelleği temizleyin veya farklı bir tarayıcı deneyin.</p>

<h2>{$brand} Müşteri Hizmetleri Avantajları</h2>
<ul>
<li>Türkçe ana dil desteği</li>
<li>Profesyonel ve deneyimli ekip</li>
<li>Hızlı sorun çözümü</li>
<li>Gizlilik ve güvenlik odaklı hizmet</li>
</ul>

<p>{$brand} hakkında daha fazla bilgi için <a href="{$siteUrl}/hakkimizda">hakkımızda</a> sayfamızı ziyaret edin. <a href="{$siteUrl}/bonuslar">Bonus kampanyalarımızı</a> da incelemeyi unutmayın.</p>
HTML
        ],

        // 6. Kayıt ve Üyelik Rehberi
        [
            'slug' => "{$prefix}-kayit-rehberi",
            'title' => "{$brand} Türkiye Kayıt ve Üyelik Rehberi {$year}",
            'meta_title' => "{$brand} Kayıt Ol {$year} | Üyelik Açma Rehberi & Adımlar",
            'meta_description' => "{$brand} kayıt ve üyelik rehberi {$year}. Adım adım hesap açma, doğrulama, ilk yatırım ve hoşgeldin bonusu alma rehberi.",
            'excerpt' => "{$brand} kayıt ve üyelik açma rehberi. Adım adım hesap açma süreci, gerekli bilgiler ve ilk yatırım bonusu.",
            'content' => <<<HTML
<h1>{$brand} Türkiye Kayıt ve Üyelik Rehberi {$year}</h1>

<p>{$brand}'e üye olmak hızlı ve kolaydır. Bu rehberde adım adım kayıt sürecini, gerekli bilgileri ve ilk yatırım sonrası yapmanız gerekenleri anlatıyoruz.</p>

<h2>{$brand} Kayıt Olma Adımları</h2>
<ol>
<li><strong>Siteye giriş:</strong> {$brand} güncel giriş adresine gidin</li>
<li><strong>Kayıt butonu:</strong> Ana sayfadaki "Kayıt Ol" veya "Üye Ol" butonuna tıklayın</li>
<li><strong>Bilgileri doldurun:</strong> İstenen bilgileri eksiksiz ve doğru girin</li>
<li><strong>Şartları kabul edin:</strong> Kullanım şartlarını ve gizlilik politikasını okuyup onaylayın</li>
<li><strong>Hesabı oluşturun:</strong> "Hesap Oluştur" butonuna basın</li>
<li><strong>Doğrulama:</strong> Gerekiyorsa e-posta veya telefon doğrulamasını tamamlayın</li>
</ol>

<h2>Kayıt İçin Gerekli Bilgiler</h2>
<ul>
<li><strong>Ad ve Soyad:</strong> Gerçek kimlik bilgilerinizle eşleşmeli</li>
<li><strong>Doğum Tarihi:</strong> 18 yaşından büyük olmanız gerekmektedir</li>
<li><strong>E-posta Adresi:</strong> Aktif kullanılan bir e-posta adresi</li>
<li><strong>Telefon Numarası:</strong> Doğrulama ve bildirimler için</li>
<li><strong>Kullanıcı Adı:</strong> Tercih ettiğiniz kullanıcı adı</li>
<li><strong>Şifre:</strong> Güçlü bir şifre oluşturun (en az 8 karakter, harf ve rakam)</li>
</ul>

<h2>Kayıt Sonrası Yapılması Gerekenler</h2>

<h3>1. Hesap Doğrulama</h3>
<p>Güvenliğiniz için hesap doğrulama işlemini tamamlamanız önerilir. Bu işlem kimlik belgesi ve adres belgesi ile yapılır. Doğrulanmış hesaplar, tüm özelliklerden sınırsız yararlanabilir.</p>

<h3>2. İlk Yatırım</h3>
<p>{$brand}'de ilk yatırımınızı yaparak bahis ve casino oyunlarına başlayabilirsiniz. Desteklenen ödeme yöntemleri:</p>
<ul>
<li>Banka havalesi / EFT</li>
<li>Papara</li>
<li>Kripto para (Bitcoin, USDT vb.)</li>
<li>Kredi kartı</li>
<li>Diğer ödeme yöntemleri</li>
</ul>

<h3>3. Hoşgeldin Bonusu</h3>
<p>İlk yatırımınızı yaptıktan sonra <a href="{$siteUrl}/bonuslar">hoşgeldin bonusu</a> almayı unutmayın. Bonus talebi için canlı destek hattımızla iletişime geçebilirsiniz.</p>

<h2>Önemli Uyarılar</h2>
<ul>
<li>18 yaşından küçükler kayıt olamaz</li>
<li>Her kişi yalnızca bir hesap açabilir</li>
<li>Kayıt bilgileri gerçek olmalıdır (sahte bilgi ile açılan hesaplar kapatılabilir)</li>
<li>Sorumlu bahis ilkelerine uygun hareket edin</li>
</ul>

<h2>Kayıt Sorunları</h2>
<p>Kayıt sırasında herhangi bir sorunla karşılaşırsanız:</p>
<ul>
<li>Bilgilerinizi kontrol edin (yazım hatası olabilir)</li>
<li>Farklı bir tarayıcı deneyin</li>
<li><a href="{$siteUrl}/iletisim">Müşteri hizmetlerimize</a> başvurun</li>
</ul>

<p>{$brand} hakkında daha fazla bilgi almak için <a href="{$siteUrl}/hakkimizda">hakkımızda</a> sayfamızı ziyaret edin.</p>
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
    $prefix = getSlugPrefix($domain);

    echo "--- {$domain} ({$brand}) - DB: {$db} ---\n";

    $posts = getBrandPosts($brand, $domain, $prefix);

    foreach ($posts as $post) {
        // Slug zaten varsa atla
        $check = $pdo->query("SELECT id FROM {$db}.posts WHERE slug = " . $pdo->quote($post['slug']))->fetch();
        if ($check) {
            echo "  [SKIP] {$post['slug']} zaten mevcut\n";
            continue;
        }

        $stmt = $pdo->prepare("INSERT INTO {$db}.posts (slug, title, excerpt, content, meta_title, meta_description, is_published, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)");
        $stmt->execute([
            $post['slug'],
            $post['title'],
            $post['excerpt'],
            $post['content'],
            $post['meta_title'],
            $post['meta_description'],
            $now,
            $now,
            $now,
        ]);
        echo "  [OK] {$post['slug']} eklendi\n";
        $totalInserted++;
    }

    echo "\n";
}

echo "Toplam {$totalInserted} blog yazısı eklendi.\n";
echo "Bitti!\n";
