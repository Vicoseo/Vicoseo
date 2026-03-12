<?php

/**
 * Lizabet Sites — SSS (FAQ) Page Seed
 * Creates unique FAQ pages for 4 Lizabet domains, each targeting different SEO intent.
 * Pages will be accessible at /sss and /sss/amp
 *
 * Usage:
 *   cd /var/www/multi-tenant-cms/backend
 *   php artisan tinker --execute="require 'seed_lizabet_sss_pages.php';"
 */

use App\Models\Site;
use Illuminate\Support\Facades\DB;

// ============================================================
// 1) lizabetcasino.com — Casino focused FAQ
// ============================================================
$sssPages['lizabetcasino.com'] = [
    'title' => 'Lizabet Casino Sıkça Sorulan Sorular (SSS)',
    'meta_title' => 'Lizabet Casino SSS — Sıkça Sorulan Sorular 2026',
    'meta_description' => 'Lizabet Casino hakkında sıkça sorulan sorular. Kayıt, bonus, ödeme yöntemleri, canlı casino, slot oyunları ve güvenlik hakkında detaylı cevaplar.',
    'content' => <<<'HTML'
<article>

<section class="lp-section">
<h2>Lizabet Casino Hakkında Genel Sorular</h2>

<h3>Lizabet Casino nedir?</h3>
<p>Lizabet Casino, uluslararası lisanslı bir online casino platformudur. Pragmatic Play, Evolution Gaming, NetEnt ve Microgaming gibi dünyaca ünlü sağlayıcıların oyunlarını sunmaktadır. Slot oyunları, canlı casino, masa oyunları ve anlık kazanç oyunları tek platform üzerinden erişilebilir durumdadır.</p>

<h3>Lizabet Casino güvenilir mi?</h3>
<p>Evet. Lizabet uluslararası oyun otoritesi tarafından lisanslanmıştır. Tüm oyunlar RNG (Rastgele Sayı Üreteci) sertifikasına sahiptir ve bağımsız denetim kuruluşları tarafından düzenli olarak test edilmektedir. 256-bit SSL şifreleme ile kullanıcı verileri korunmaktadır.</p>

<h3>Lizabet Casino Türkçe destek sunuyor mu?</h3>
<p>Evet. Platform tamamen Türkçe arayüze sahiptir. 7/24 Türkçe canlı destek hattı üzerinden yardım alabilirsiniz. Ortalama yanıt süresi 2 dakikanın altındadır.</p>
</section>

<section class="lp-section">
<h2>Kayıt ve Hesap İşlemleri</h2>

<h3>Lizabet Casino'ya nasıl kayıt olurum?</h3>
<p>Ana sayfadaki "Kayıt Ol" butonuna tıklayarak kayıt formunu doldurun. Ad, soyad, e-posta ve telefon bilgilerinizi girin. Güçlü bir şifre belirleyin. Kayıt işlemi birkaç dakika içinde tamamlanır.</p>

<h3>Hesap doğrulama gerekli mi?</h3>
<p>İlk çekim talebinizde kimlik doğrulama yapılması gerekmektedir. Kimlik belgesi ve adres ispat belgesi ile doğrulama tamamlanır. Bu işlem güvenliğiniz için uygulanmaktadır.</p>

<h3>Şifremi unuttum, ne yapmalıyım?</h3>
<p>Giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayın. Kayıtlı e-posta adresinize şifre sıfırlama linki gönderilecektir. Alternatif olarak canlı destek hattından da yardım alabilirsiniz.</p>
</section>

<section class="lp-section">
<h2>Bonus ve Promosyonlar</h2>

<h3>Hoşgeldin bonusu nasıl alınır?</h3>
<p>İlk yatırımınızda %50 hoşgeldin bonusu otomatik olarak hesabınıza tanımlanır. Minimum yatırım tutarı ve çevrim şartları promosyonlar sayfasında detaylı olarak açıklanmaktadır.</p>

<h3>Deneme bonusu var mı?</h3>
<p>Evet. Yeni üyeler kayıt işleminin ardından 500TL deneme bonusu alabilir. Bu bonus herhangi bir yatırım gerektirmeden otomatik olarak tanımlanır.</p>

<h3>Bonus çevrim şartı ne demektir?</h3>
<p>Çevrim şartı, bonus tutarının ne kadar oynanması gerektiğini belirler. Örneğin 10x çevrim şartı olan 100TL bonus için toplamda 1.000TL değerinde bahis yapılması gerekmektedir. Çevrim tamamlandığında kazanç çekilebilir bakiyeye dönüşür.</p>

<h3>Kripto bonusları nelerdir?</h3>
<p>Kripto para ile yatırım yapan kullanıcılara özel %20 yatırım bonusu, %30 kayıp bonusu ve %50 kripto slot bonusu sunulmaktadır. Bitcoin, Ethereum ve USDT ile yapılan yatırımlarda geçerlidir.</p>
</section>

<section class="lp-section">
<h2>Ödeme Yöntemleri</h2>

<h3>Hangi ödeme yöntemleri kabul ediliyor?</h3>
<p>Banka havalesi, Papara, kripto para (Bitcoin, Ethereum, USDT, Litecoin), kredi kartı ve çeşitli e-cüzdan seçenekleri kabul edilmektedir.</p>

<h3>Minimum yatırım tutarı ne kadar?</h3>
<p>Minimum yatırım tutarı ödeme yöntemine göre değişmektedir. Genel olarak 50TL'den başlayan tutarlarla yatırım yapılabilir. Kripto yatırımlarda minimum tutar daha düşük olabilir.</p>

<h3>Çekim işlemleri ne kadar sürer?</h3>
<p>Çekim talepleri ortalama 15 dakika içinde işleme alınır. Kripto çekimleri ağ onay süresine bağlı olarak birkaç dakika içinde tamamlanır. Banka havalesi ile çekimlerde 1-24 saat arası sürebilir.</p>

<h3>Çekim limiti var mı?</h3>
<p>Günlük ve aylık çekim limitleri hesap seviyesine göre değişmektedir. VIP üyeler daha yüksek çekim limitlerinden yararlanabilir. Detaylı bilgi için müşteri desteğine başvurabilirsiniz.</p>
</section>

<section class="lp-section">
<h2>Casino Oyunları</h2>

<h3>Kaç slot oyunu mevcut?</h3>
<p>Lizabet Casino'da Pragmatic Play, NetEnt, Play'n GO, Microgaming ve diğer sağlayıcılardan binlerce slot oyunu mevcuttur. Yeni oyunlar düzenli olarak eklenmektedir.</p>

<h3>Canlı casino nasıl çalışır?</h3>
<p>Canlı casino bölümünde gerçek krupiyeler eşliğinde HD kalitesinde yayınlanan masalarda oynarsınız. Lightning Roulette, Crazy Time, Blackjack ve Baccarat gibi oyunlar mevcuttur. Masalara gerçek zamanlı olarak katılabilirsiniz.</p>

<h3>Demo modda oynayabilir miyim?</h3>
<p>Evet. Birçok slot oyunu ücretsiz demo modda oynanabilir. Bu sayede gerçek para yatırmadan oyunları deneyebilirsiniz. Canlı casino oyunlarında demo mod mevcut değildir.</p>

<h3>Oyunlar adil mi?</h3>
<p>Evet. Tüm oyunlar bağımsız denetim kuruluşları tarafından test edilmiş RNG sertifikalı oyunlardır. Oyun sonuçları tamamen rastgele oluşturulur ve platform tarafından müdahale edilemez.</p>
</section>

<section class="lp-section">
<h2>Güvenlik ve Erişim</h2>

<h3>İki faktörlü doğrulama (2FA) var mı?</h3>
<p>Evet. Hesap güvenliğinizi artırmak için 2FA özelliğini aktif edebilirsiniz. Bu özellik, giriş yaparken ek bir doğrulama kodu gerektirir ve yetkisiz erişimleri engeller.</p>

<h3>Giriş adresi neden değişiyor?</h3>
<p>Türkiye'deki erişim kısıtlamaları nedeniyle giriş adresi belirli aralıklarla güncellenmektedir. Hesap bilgileriniz ve bakiyeniz her zaman korunur. Güncel adresi Telegram kanalımızdan takip edebilirsiniz.</p>

<h3>Mobil cihazdan erişebilir miyim?</h3>
<p>Evet. Lizabet Casino tüm mobil cihazlarla tam uyumlu çalışır. Herhangi bir uygulama indirmenize gerek yoktur. Tarayıcınız üzerinden tüm özelliklere erişebilirsiniz.</p>
</section>

</article>
HTML
];

// ============================================================
// 2) lizabetgiris.site — Access/login focused FAQ
// ============================================================
$sssPages['lizabetgiris.site'] = [
    'title' => 'Lizabet Giriş Sıkça Sorulan Sorular (SSS)',
    'meta_title' => 'Lizabet Giriş SSS — Erişim ve Giriş Sorunları Rehberi 2026',
    'meta_description' => 'Lizabet giriş sorunları ve çözümleri. Güncel adres, mobil erişim, VPN kullanımı, şifre sıfırlama ve hesap güvenliği hakkında sıkça sorulan sorular.',
    'content' => <<<'HTML'
<article>

<section class="lp-section">
<h2>Lizabet Giriş ve Erişim Soruları</h2>

<h3>Lizabet'e nasıl giriş yapılır?</h3>
<p>Güncel giriş adresine tarayıcınız üzerinden erişin. Kullanıcı adınız ve şifreniz ile giriş yapın. Güncel adresi bu site veya resmi Telegram kanalı üzerinden öğrenebilirsiniz.</p>

<h3>Lizabet giriş adresi neden sürekli değişiyor?</h3>
<p>Türkiye'deki BTK erişim engellemeleri nedeniyle domain adresleri belirli aralıklarla güncellenmektedir. Bu durum sadece erişim adresini etkiler. Hesabınız, bakiyeniz ve tüm verileriniz yeni adreste de aynen korunur.</p>

<h3>Güncel Lizabet giriş adresini nasıl öğrenirim?</h3>
<p>Güncel giriş adresini öğrenmek için: 1) Bu sayfayı yer imlerinize ekleyin, 2) Lizabet resmi Telegram kanalını takip edin, 3) Kayıtlı e-posta adresinize gelen bilgilendirmeleri kontrol edin. Üçüncü taraf sitelerden paylaşılan linklere dikkat edin.</p>

<h3>Eski giriş adresim çalışmıyor, ne yapmalıyım?</h3>
<p>Eski adres engellenmiş olabilir. Bu sayfadaki güncel linki kullanarak giriş yapabilirsiniz. Tüm hesap bilgileriniz yeni adreste de geçerlidir. Herhangi bir kayıp yaşanmaz.</p>
</section>

<section class="lp-section">
<h2>Kayıt ve Hesap Soruları</h2>

<h3>Lizabet'e nasıl kayıt olurum?</h3>
<p>Güncel giriş adresine giderek "Kayıt Ol" butonuna tıklayın. Ad, soyad, e-posta, telefon numarası ve doğum tarihi bilgilerinizi girin. Güçlü bir şifre oluşturun. Kayıt işlemi 2-3 dakika içinde tamamlanır.</p>

<h3>Kayıt olurken hangi bilgiler isteniyor?</h3>
<p>Kayıt için ad, soyad, e-posta adresi, telefon numarası, doğum tarihi ve tercih ettiğiniz kullanıcı adı ile şifre gerekmektedir. Tüm bilgiler gizli tutulur ve üçüncü taraflarla paylaşılmaz.</p>

<h3>Birden fazla hesap açabilir miyim?</h3>
<p>Hayır. Platform kuralları gereği her kullanıcı yalnızca bir hesap açabilir. Birden fazla hesap tespit edilmesi durumunda hesaplar askıya alınabilir.</p>

<h3>Şifremi unuttum, nasıl sıfırlarım?</h3>
<p>Giriş sayfasında "Şifremi Unuttum" bağlantısına tıklayarak kayıtlı e-posta adresinize sıfırlama linki gönderebilirsiniz. Link 30 dakika içinde geçerlidir. Alternatif olarak 7/24 canlı destek hattından da yardım alabilirsiniz.</p>
</section>

<section class="lp-section">
<h2>Mobil Erişim Soruları</h2>

<h3>Telefondan Lizabet'e giriş yapabilir miyim?</h3>
<p>Evet. Lizabet tüm mobil cihazlarla (iPhone, Android, tablet) tam uyumlu çalışır. Herhangi bir uygulama indirmenize gerek yoktur. Mobil tarayıcınız üzerinden güncel giriş adresine giderek tüm özelliklere erişebilirsiniz.</p>

<h3>Lizabet mobil uygulaması var mı?</h3>
<p>Ayrı bir uygulama indirmenize gerek yoktur. Web sitesi responsive tasarımı sayesinde mobil cihazlarda uygulama kalitesinde çalışır. Ana ekranınıza ekleyerek tek dokunuşla erişim sağlayabilirsiniz.</p>

<h3>Mobil giriş yavaş, ne yapabilirim?</h3>
<p>Tarayıcı önbelleğinizi temizlemeyi deneyin. Farklı bir tarayıcı (Chrome, Safari, Firefox) kullanın. Wi-Fi veya mobil veri bağlantınızı kontrol edin. Sorun devam ederse canlı destek hattından yardım alın.</p>
</section>

<section class="lp-section">
<h2>Güvenlik Soruları</h2>

<h3>Lizabet giriş yaparken güvenliğimi nasıl sağlarım?</h3>
<p>Güçlü ve benzersiz bir şifre kullanın. İki faktörlü doğrulamayı (2FA) aktif edin. Yalnızca resmi kanallardan paylaşılan giriş linklerini kullanın. Adres çubuğunda SSL kilit simgesini kontrol edin.</p>

<h3>VPN kullanmam gerekiyor mu?</h3>
<p>Güncel giriş adresi üzerinden VPN kullanmadan erişim sağlayabilirsiniz. Ancak ek güvenlik için VPN kullanmak istiyorsanız, herhangi bir kısıtlama uygulanmamaktadır.</p>

<h3>Hesabım ele geçirildi, ne yapmalıyım?</h3>
<p>Derhal canlı destek hattına başvurun. Hesabınız geçici olarak dondurulacak ve güvenlik incelemesi yapılacaktır. Şifrenizi değiştirin ve 2FA'yı aktif edin.</p>
</section>

<section class="lp-section">
<h2>Bonus ve Ödeme Soruları</h2>

<h3>Giriş yaptıktan sonra bonus alabilir miyim?</h3>
<p>Evet. Yeni üyeler 500TL deneme bonusu ve ilk yatırımda %50 hoşgeldin bonusu alabilir. Mevcut üyeler için haftalık jest bonusu, kayıp bonusu ve kripto bonusları gibi sürekli kampanyalar mevcuttur.</p>

<h3>Ödeme yöntemleri nelerdir?</h3>
<p>Banka havalesi, Papara, kripto para (Bitcoin, Ethereum, USDT), kredi kartı ve e-cüzdan seçenekleri mevcuttur. Yatırımlar genellikle anlık, çekimler ortalama 15 dakika içinde işleme alınır.</p>
</section>

</article>
HTML
];

// ============================================================
// 3) lizabahis.site — Sports betting focused FAQ
// ============================================================
$sssPages['lizabahis.site'] = [
    'title' => 'Lizabet Bahis Sıkça Sorulan Sorular (SSS)',
    'meta_title' => 'Lizabet Bahis SSS — Spor Bahisleri Sıkça Sorulan Sorular 2026',
    'meta_description' => 'Lizabet spor bahisleri hakkında sıkça sorulan sorular. Canlı bahis, kombine kupon, oranlar, bahis türleri, mobil bahis ve bonus kampanyaları.',
    'content' => <<<'HTML'
<article>

<section class="lp-section">
<h2>Lizabet Spor Bahisleri Genel Sorular</h2>

<h3>Lizabet'te hangi spor dallarına bahis yapılabilir?</h3>
<p>Futbol, basketbol, tenis, voleybol, hentbol, buz hokeyi, beyzbol, Amerikan futbolu, kriket, masa tenisi, e-spor ve daha fazlası dahil 30'dan fazla spor dalında bahis yapabilirsiniz.</p>

<h3>Minimum bahis tutarı ne kadar?</h3>
<p>Minimum bahis tutarı genel olarak 1TL'den başlamaktadır. Spor dalı ve bahis türüne göre farklılık gösterebilir. Detaylı bilgiye kupon oluşturma ekranından ulaşabilirsiniz.</p>

<h3>Lizabet oranları nasıl?</h3>
<p>Lizabet, sektördeki en rekabetçi oranları sunmaktadır. Özellikle futbol ve basketbol gibi popüler spor dallarında yüksek oranlar mevcuttur. Oranlar maç başlamadan önce ve canlı bahis sırasında anlık olarak güncellenir.</p>
</section>

<section class="lp-section">
<h2>Canlı Bahis Soruları</h2>

<h3>Canlı bahis nedir?</h3>
<p>Canlı bahis, devam eden bir maça gerçek zamanlı olarak bahis yapmanıza olanak tanır. Maçın gidişatını izleyerek anlık oranlarla bahis yapabilirsiniz. Futbol, basketbol, tenis ve diğer birçok spor dalında canlı bahis mevcuttur.</p>

<h3>Canlı bahiste cash-out nasıl kullanılır?</h3>
<p>Cash-out özelliği, bahsinizi maç bitmeden kapatmanıza olanak tanır. Kuponunuzda "Cash Out" butonu aktif olduğunda teklif edilen tutarı kabul ederek kazancınızı garantileyebilir veya kaybınızı sınırlayabilirsiniz.</p>

<h3>Canlı bahis sırasında maçı izleyebilir miyim?</h3>
<p>Lizabet canlı bahis bölümünde anlık skor takibi, istatistik tabloları ve animasyonlu maç görselleştirmeleri mevcuttur. Bazı maçlar için canlı yayın da sunulabilmektedir.</p>

<h3>Canlı bahiste oranlar ne sıklıkla değişir?</h3>
<p>Oranlar maçın gidişatına göre saniyeler içinde güncellenir. Gol, kırmızı kart, penaltı gibi kritik anlarda oranlar hızla değişebilir. Bahisinizi hızlı bir şekilde onaylamanız önerilir.</p>
</section>

<section class="lp-section">
<h2>Bahis Türleri ve Kuponlar</h2>

<h3>Kombine kupon nedir?</h3>
<p>Kombine kupon, birden fazla maçın tek bir kupona eklenmesiyle oluşturulur. Tüm tahminlerinizin doğru çıkması durumunda oranlar birbiriyle çarpılarak yüksek kazanç potansiyeli sunar. Lizabet'te 3 ve üzeri maçlık kombinelerde %20 ekstra bonus verilir.</p>

<h3>Hangi bahis türleri mevcut?</h3>
<p>Maç sonucu, çifte şans, alt/üst gol, handikap, ilk yarı/maç sonu, karşılıklı gol, toplam gol, golcü bahisleri, korner, kart, faul ve daha onlarca bahis marketi mevcuttur.</p>

<h3>Sistem bahisi nedir?</h3>
<p>Sistem bahisi, kombine kupondaki tüm maçların tutması gerekmeden kazanç elde etmenizi sağlar. Örneğin 3'lü sistem bahisinde 4 maçtan 3'ünün tutması yeterlidir. Riski azaltan ancak oranı düşüren bir bahis türüdür.</p>

<h3>Bahis kuponu ne zaman sonuçlanır?</h3>
<p>Kuponunuz, seçtiğiniz tüm maçlar tamamlandığında sonuçlanır. Kazanan kuponların tutarı hesabınıza anında yansır. Canlı bahislerde cash-out ile erken sonuçlandırma da mümkündür.</p>
</section>

<section class="lp-section">
<h2>Bahis Bonusları</h2>

<h3>Spor bahisleri için hangi bonuslar var?</h3>
<p>Hoşgeldin bonusu (%50), deneme bonusu (500TL), kombine bonusu (%20), spor kayıp bonusu (%20), gece kuşu kayıp bonusu (%25) ve kripto yatırım bonusu (%20) aktif kampanyalar arasındadır.</p>

<h3>Kombine bonusu nasıl hesaplanır?</h3>
<p>3 ve üzeri maçlık kombine kuponlarınızda toplam kazancınıza %20 ekstra bonus eklenir. Bonus tutarı kupon sonuçlandıktan sonra otomatik olarak hesabınıza yansır.</p>

<h3>Gece kuşu bonusu nedir?</h3>
<p>Gece 00:00 - 08:00 arasında yapılan spor bahislerinde oluşan kayıplarınızın %25'i ertesi gün hesabınıza iade edilir. NBA, NHL ve Güney Amerika ligleri gibi gece saatlerinde oynanan maçlar için ideal bir kampanyadır.</p>
</section>

<section class="lp-section">
<h2>Mobil ve Ödeme Soruları</h2>

<h3>Telefondan bahis yapabilir miyim?</h3>
<p>Evet. Lizabet'in mobil uyumlu arayüzü ile telefonunuzdan canlı bahis, kupon oluşturma ve tüm bahis işlemlerini sorunsuz şekilde gerçekleştirebilirsiniz. Uygulama indirmenize gerek yoktur.</p>

<h3>Kripto ile bahis yapabilir miyim?</h3>
<p>Evet. Bitcoin, Ethereum ve USDT ile yatırım yaparak tüm spor bahislerinde oynayabilirsiniz. Kripto yatırımlarda %20 ekstra bonus ve hızlı çekim avantajından yararlanabilirsiniz.</p>

<h3>Bahis geçmişimi nasıl görebilirim?</h3>
<p>Hesabınıza giriş yaptıktan sonra "Bahis Geçmişi" veya "Kuponlarım" bölümünden tüm geçmiş ve aktif kuponlarınızı detaylı şekilde inceleyebilirsiniz.</p>
</section>

</article>
HTML
];

// ============================================================
// 4) girislizabet.site — Informational/trust focused FAQ
// ============================================================
$sssPages['girislizabet.site'] = [
    'title' => 'Lizabet Hakkında Sıkça Sorulan Sorular (SSS)',
    'meta_title' => 'Lizabet SSS — Güvenilirlik, Ödeme ve Bonus Soruları 2026',
    'meta_description' => 'Lizabet hakkında sıkça sorulan sorular. Platform güvenilirliği, lisans bilgileri, ödeme yöntemleri, çekim süreleri ve kullanıcı hakları hakkında detaylı cevaplar.',
    'content' => <<<'HTML'
<article>

<section class="lp-section">
<h2>Lizabet Platform Güvenilirliği</h2>

<h3>Lizabet güvenilir mi?</h3>
<p>Lizabet, uluslararası oyun otoritesi tarafından lisanslanmış bir platformdur. Tüm oyunlar RNG sertifikalıdır ve bağımsız denetim kuruluşları tarafından düzenli olarak test edilmektedir. 256-bit SSL şifreleme ile kullanıcı verileri korunmaktadır. Sorumlu oyun politikası çerçevesinde bahis limiti belirleme ve hesap dondurma araçları sunulmaktadır.</p>

<h3>Lizabet'in lisans bilgileri nelerdir?</h3>
<p>Lizabet uluslararası oyun otoritesi tarafından düzenlenen lisans ile faaliyet göstermektedir. Lisans bilgileri platformun alt kısmında yer almaktadır. Bu lisans, oyun adaleti, finansal güvenlik ve kullanıcı haklarının korunmasını garanti eder.</p>

<h3>Kullanıcı verileri güvende mi?</h3>
<p>Evet. Tüm kişisel ve finansal veriler 256-bit SSL şifreleme ile korunmaktadır. Veriler üçüncü taraflarla paylaşılmaz. GDPR uyumlu gizlilik politikası uygulanmaktadır. İki faktörlü doğrulama (2FA) ile hesap güvenliği ekstra katmanda sağlanır.</p>

<h3>Lizabet'te hile veya manipülasyon var mı?</h3>
<p>Hayır. Tüm casino oyunları RNG (Rastgele Sayı Üreteci) sertifikalı sağlayıcılar tarafından sunulmaktadır. Oyun sonuçları tamamen rastgele oluşturulur ve platform tarafından müdahale edilmesi teknik olarak mümkün değildir.</p>
</section>

<section class="lp-section">
<h2>Ödeme ve Finansal İşlemler</h2>

<h3>Lizabet'te hangi ödeme yöntemleri var?</h3>
<p>Banka havalesi, Papara, kripto para (Bitcoin, Ethereum, USDT, Litecoin), kredi kartı ve çeşitli e-cüzdan seçenekleri mevcuttur. Her ödeme yönteminin minimum ve maksimum limitleri farklılık gösterebilir.</p>

<h3>Yatırım işlemleri ne kadar sürer?</h3>
<p>Kripto para yatırımları genellikle 1-10 dakika içinde onaylanır. Papara ve banka havalesi yatırımları 5-30 dakika içinde hesaba yansır. İşlem süreleri yoğunluk durumuna göre değişebilir.</p>

<h3>Çekim işlemleri ne kadar sürer?</h3>
<p>Çekim talepleri ortalama 15 dakika içinde işleme alınır. Kripto çekimleri ağ onay süresine bağlı olarak birkaç dakika içinde tamamlanır. Banka havalesi çekimlerinde 1-24 saat arası sürebilir.</p>

<h3>Çekim yaparken sorun yaşıyorum, ne yapmalıyım?</h3>
<p>Hesap doğrulamanızın tamamlandığından emin olun. Çevrim şartlarının karşılanıp karşılanmadığını kontrol edin. Minimum çekim tutarını sağladığınızdan emin olun. Sorun devam ederse 7/24 canlı destek hattına başvurun.</p>

<h3>Komisyon alınıyor mu?</h3>
<p>Yatırım işlemlerinde genel olarak komisyon alınmaz. Çekim işlemlerinde ödeme yöntemine göre minimal komisyon uygulanabilir. Kripto çekimlerde ağ ücretleri kullanıcıya yansıyabilir.</p>
</section>

<section class="lp-section">
<h2>Bonuslar ve Kampanyalar</h2>

<h3>Lizabet'te hangi bonuslar var?</h3>
<p>Hoşgeldin bonusu (%50), deneme bonusu (500TL), çevrimsiz yatırım bonusu (%15), kombine bonusu (%20), haftalık jest bonusu (%5), gece kuşu kayıp bonusu (%25), kripto yatırım bonusu (%20), kripto kayıp bonusu (%30), kripto slot bonusu (%50) ve sadakat programı aktif kampanyalar arasındadır.</p>

<h3>Bonus çevrim şartı ne demek?</h3>
<p>Bonus tutarının belirli bir miktar oynanması gerektiğini belirler. Örneğin 10x çevrim şartı olan 100TL bonus için 1.000TL değerinde bahis yapılması gerekir. Çevrimsiz bonuslarda bu şart uygulanmaz.</p>

<h3>Bonus iptal edilebilir mi?</h3>
<p>Evet. Aktif bir bonusu iptal etmek isterseniz müşteri desteğine başvurabilirsiniz. Ancak bonus iptalinde bonus bakiyesi ve bonusla elde edilen kazançlar da silinir.</p>
</section>

<section class="lp-section">
<h2>Kullanıcı Hakları ve Destek</h2>

<h3>Müşteri desteğine nasıl ulaşabilirim?</h3>
<p>7/24 canlı sohbet, e-posta ve sosyal medya kanalları üzerinden müşteri desteğine ulaşabilirsiniz. Canlı sohbette ortalama yanıt süresi 2 dakikanın altındadır.</p>

<h3>Şikayetimi nereye bildiririm?</h3>
<p>Şikayetlerinizi canlı destek hattı, e-posta veya platform içindeki şikayet formu üzerinden iletebilirsiniz. Tüm şikayetler 24 saat içinde değerlendirilir ve yanıtlanır.</p>

<h3>Hesabımı kapatabilir miyim?</h3>
<p>Evet. Müşteri desteğine başvurarak hesabınızı kalıcı olarak kapatabilirsiniz. Kapatma öncesi mevcut bakiyenizi çekmeniz önerilir. Sorumlu oyun kapsamında geçici hesap dondurma seçeneği de mevcuttur.</p>

<h3>Sorumlu oyun politikası nedir?</h3>
<p>Lizabet, kullanıcılarının kontrollü ve sağlıklı bir oyun deneyimi yaşaması için bahis limiti belirleme, kayıp limiti, oturum süresi sınırlandırma, hesap dondurma ve kendini dışlama araçları sunmaktadır.</p>
</section>

<section class="lp-section">
<h2>Erişim ve Teknik Sorular</h2>

<h3>Lizabet'e neden erişemiyorum?</h3>
<p>Türkiye'deki BTK erişim kısıtlamaları nedeniyle eski adresler engellenebilir. Güncel giriş adresini bu site veya resmi Telegram kanalı üzerinden öğrenebilirsiniz.</p>

<h3>Mobil cihazdan erişebilir miyim?</h3>
<p>Evet. Lizabet tüm mobil cihazlarla tam uyumlu çalışır. iPhone, Android telefon ve tabletlerde ayrı bir uygulama indirmenize gerek kalmadan tarayıcı üzerinden erişim sağlayabilirsiniz.</p>

<h3>Hangi tarayıcılar destekleniyor?</h3>
<p>Chrome, Safari, Firefox, Edge ve Opera dahil tüm modern tarayıcılar desteklenmektedir. En iyi deneyim için tarayıcınızın güncel sürümünü kullanmanız önerilir.</p>
</section>

</article>
HTML
];

// ============================================================
// Execute: Create SSS pages in tenant databases
// ============================================================

echo "=== Creating SSS Pages for Lizabet Sites ===\n\n";

foreach ($sssPages as $domain => $data) {
    $site = Site::where('domain', $domain)->first();
    if (!$site) {
        echo "SKIP: {$domain} not found\n";
        continue;
    }

    config(['database.connections.tenant.database' => $site->db_name]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    // Check if SSS page already exists
    $existing = DB::connection('tenant')->selectOne("SELECT id FROM pages WHERE slug = 'sss'");

    if ($existing) {
        DB::connection('tenant')->table('pages')
            ->where('slug', 'sss')
            ->update([
                'title'            => $data['title'],
                'meta_title'       => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'content'          => $data['content'],
                'updated_at'       => now(),
            ]);
        echo "{$domain}: SSS page UPDATED (id: {$existing->id})\n";
    } else {
        // Get max sort_order
        $maxOrder = DB::connection('tenant')->selectOne("SELECT MAX(sort_order) as m FROM pages");
        $sortOrder = ($maxOrder->m ?? 0) + 1;

        DB::connection('tenant')->table('pages')->insert([
            'slug'             => 'sss',
            'title'            => $data['title'],
            'meta_title'       => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'content'          => $data['content'],
            'sort_order'       => $sortOrder,
            'is_published'     => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        echo "{$domain}: SSS page CREATED (sort_order: {$sortOrder})\n";
    }
}

echo "\nDone! SSS pages created for all 4 Lizabet sites.\n";
echo "Accessible at: /sss (page) and /sss/amp (AMP version)\n";
