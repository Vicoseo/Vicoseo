<?php

/**
 * Locabet Content Expansion Seed
 * Adds 10 new pages + 10 new posts + 1 SSS page for each of 4 locabet sites
 *
 * Usage:
 *   php -r "require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); require 'seed_locabet_content.php';"
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

$domains = [
    'locabeet.com',
    'locabetbonus.me',
    'locabetcasino.com',
    'locabetgiris.site',
];

// ─── PAGE DEFINITIONS ───
function getPageDefinitions($brand = 'Locabet') {
    return [
        [
            'slug' => 'locabet-giris',
            'title' => "$brand Giriş - Güncel Giriş Adresi 2026",
            'meta_title' => "$brand Giriş 2026 – Güncel Adres ve Hızlı Erişim",
            'meta_description' => "$brand güncel giriş adresi, alternatif linkler ve erişim rehberi. Kesintisiz platforma ulaşmak için doğru adres.",
            'sort_order' => 4,
            'content' => <<<HTML
<h2>$brand Güncel Giriş Adresi</h2>
<p>$brand platformuna güvenli ve hızlı erişim sağlamak için güncel giriş adresini kullanmanız gerekmektedir. Platform, kullanıcılarına kesintisiz hizmet sunabilmek adına zaman zaman alan adı güncellemesi yapmaktadır. Bu sayfada her zaman en güncel $brand giriş linkini bulabilirsiniz.</p>

<h2>Giriş Yapma Adımları</h2>
<ol>
<li>Güncel $brand giriş adresine tıklayın</li>
<li>Kullanıcı adınızı veya e-posta adresinizi girin</li>
<li>Şifrenizi yazın ve "Giriş Yap" butonuna tıklayın</li>
<li>İki faktörlü doğrulama aktifse, gelen kodu girin</li>
<li>Hesabınıza başarıyla giriş yapın</li>
</ol>

<h2>Erişim Sorunları ve Çözümleri</h2>
<p>$brand platformuna erişimde sorun yaşıyorsanız aşağıdaki yöntemleri deneyebilirsiniz:</p>
<ul>
<li><strong>DNS Değişikliği:</strong> Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) kullanarak erişim sağlayabilirsiniz</li>
<li><strong>VPN Kullanımı:</strong> Güvenilir bir VPN uygulaması ile bağlantı kurabilirsiniz</li>
<li><strong>Tarayıcı Cache Temizleme:</strong> Tarayıcınızın önbelleğini temizleyip tekrar deneyin</li>
<li><strong>Alternatif Tarayıcı:</strong> Chrome, Firefox veya Brave gibi farklı bir tarayıcı kullanın</li>
</ul>

<h2>Güvenli Giriş İpuçları</h2>
<p>$brand hesabınızın güvenliğini sağlamak için şu önlemleri alın: Güçlü ve benzersiz bir şifre kullanın. İki faktörlü doğrulamayı aktif edin. Yalnızca resmi giriş adreslerini kullanın. Şüpheli bağlantılara tıklamayın ve bilgilerinizi üçüncü taraflarla paylaşmayın.</p>

<h2>Mobil Giriş</h2>
<p>$brand mobil uyumlu tasarımı sayesinde akıllı telefonunuz üzerinden de kolayca giriş yapabilirsiniz. Mobil tarayıcınız üzerinden güncel adrese erişerek, masaüstü sürümündeki tüm özelliklere ulaşabilirsiniz. Android ve iOS cihazlarla tam uyumlu çalışmaktadır.</p>

<h2>Şifremi Unuttum</h2>
<p>Şifrenizi unuttuysanız, giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayarak kayıtlı e-posta adresinize şifre sıfırlama linki gönderebilirsiniz. Link e-postanıza birkaç dakika içinde ulaşacaktır. Spam klasörünü de kontrol etmeyi unutmayın.</p>
HTML
        ],
        [
            'slug' => 'locabet-bonuslari',
            'title' => "$brand Bonusları - Güncel Kampanyalar",
            'meta_title' => "$brand Bonusları 2026 – Hoş Geldin ve Yatırım Bonusları",
            'meta_description' => "$brand güncel bonus kampanyaları, hoş geldin bonusu, yatırım bonusu ve çevrim şartları. En avantajlı fırsatlar.",
            'sort_order' => 5,
            'content' => <<<HTML
<h2>$brand Güncel Bonus Kampanyaları</h2>
<p>$brand, yeni ve mevcut üyelerine çeşitli bonus fırsatları sunmaktadır. Hoş geldin bonusundan yatırım bonusuna, kayıp bonusundan arkadaş davet bonusuna kadar pek çok avantajlı kampanyadan yararlanabilirsiniz.</p>

<h2>Hoş Geldin Bonusu</h2>
<p>$brand'e ilk kez üye olan kullanıcılar, ilk yatırımlarında hoş geldin bonusundan faydalanabilir. Bu bonus, yeni üyelerin platformu keşfetmeleri için sunulan avantajlı bir fırsattır. Bonus miktarı ve çevrim şartları kampanya dönemine göre değişiklik gösterebilir.</p>

<h2>Yatırım Bonusu</h2>
<p>Her yatırımınızda ekstra bakiye kazanma fırsatı sunan yatırım bonusları, $brand'in en popüler kampanyaları arasında yer almaktadır. Minimum yatırım tutarı ve bonus oranları, seçtiğiniz ödeme yöntemine göre farklılık gösterebilir.</p>

<h2>Kayıp Bonusu</h2>
<p>Belirli dönemlerde kayıplarınızın bir kısmını geri almanızı sağlayan kayıp bonusu, $brand üyelerine sunulan önemli avantajlardan biridir. Haftalık veya aylık olarak hesaplanan kayıp bonusu, otomatik veya talep üzerine hesabınıza yansıtılmaktadır.</p>

<h2>Çevrim Şartları</h2>
<p>Bonusların çekilebilir bakiyeye dönüşmesi için belirli çevrim şartlarının yerine getirilmesi gerekmektedir. Her bonus kampanyasının kendine özgü çevrim koşulları bulunmaktadır. Bonus almadan önce ilgili kampanyanın şartlarını dikkatli bir şekilde okumanız önerilir.</p>

<h2>Bonus Kullanım İpuçları</h2>
<ul>
<li>Bonus almadan önce çevrim şartlarını mutlaka okuyun</li>
<li>Bonus kullanım süresine dikkat edin</li>
<li>Aynı anda birden fazla bonus aktif etmeyin</li>
<li>Minimum yatırım tutarını kontrol edin</li>
<li>Canlı destek hattından bonus talebi yapabilirsiniz</li>
</ul>
HTML
        ],
        [
            'slug' => 'locabet-casino-oyunlari',
            'title' => "$brand Casino Oyunları - Slot ve Masa Oyunları",
            'meta_title' => "$brand Casino Oyunları 2026 – Slot, Rulet ve Blackjack",
            'meta_description' => "$brand casino oyunları rehberi. Slot makineleri, rulet, blackjack, poker ve daha fazlası. En popüler oyun sağlayıcıları.",
            'sort_order' => 6,
            'content' => <<<HTML
<h2>$brand Casino Oyunları</h2>
<p>$brand casino bölümünde binlerce oyun seçeneği bulunmaktadır. Pragmatic Play, Evolution Gaming, NetEnt, Microgaming ve daha birçok lider oyun sağlayıcısının oyunlarına erişebilirsiniz.</p>

<h2>Slot Oyunları</h2>
<p>$brand'de Gates of Olympus, Sweet Bonanza, The Dog House, Big Bass Bonanza ve daha yüzlerce popüler slot oyununa erişebilirsiniz. Klasik 3 makaralı slotlardan modern video slotlara, jackpot oyunlarından megaways mekanikli slotlara kadar geniş bir yelpaze sunulmaktadır.</p>

<h2>Masa Oyunları</h2>
<p>Rulet, blackjack, baccarat ve poker gibi klasik casino masa oyunları $brand'de mevcuttur. Farklı bahis limitleri ve oyun varyasyonları ile her seviyedeki oyuncuya uygun seçenekler bulunmaktadır.</p>

<h2>Oyun Sağlayıcıları</h2>
<ul>
<li><strong>Pragmatic Play:</strong> Gates of Olympus, Sweet Bonanza, The Dog House</li>
<li><strong>Evolution Gaming:</strong> Canlı rulet, blackjack, game show oyunları</li>
<li><strong>NetEnt:</strong> Starburst, Gonzo's Quest, Dead or Alive</li>
<li><strong>Play'n GO:</strong> Book of Dead, Reactoonz, Fire Joker</li>
<li><strong>Microgaming:</strong> Mega Moolah, Immortal Romance, Thunderstruck</li>
</ul>

<h2>Demo Modu</h2>
<p>Birçok slot oyununu gerçek para yatırmadan demo modunda deneyebilirsiniz. Bu sayede oyunların mekaniklerini, bonus özelliklerini ve volatilitesini risk almadan öğrenebilirsiniz.</p>

<h2>RTP ve Volatilite Rehberi</h2>
<p>Oyun seçerken RTP (Return to Player) oranına ve volatilite seviyesine dikkat etmeniz önerilir. Yüksek RTP'li oyunlar uzun vadede daha fazla geri ödeme sunarken, düşük volatiliteli oyunlar daha sık kazanç sağlar. Tercihlerinize uygun oyunu seçmek için bu değerleri karşılaştırabilirsiniz.</p>
HTML
        ],
        [
            'slug' => 'locabet-mobil',
            'title' => "$brand Mobil - Telefondan Erişim Rehberi",
            'meta_title' => "$brand Mobil 2026 – Android ve iOS Erişim Rehberi",
            'meta_description' => "$brand mobil uygulaması, Android APK indirme ve iOS erişim rehberi. Telefonunuzdan güvenli erişim.",
            'sort_order' => 7,
            'content' => <<<HTML
<h2>$brand Mobil Erişim</h2>
<p>$brand, mobil uyumlu tasarımı sayesinde akıllı telefonlar ve tabletler üzerinden sorunsuz bir kullanım deneyimi sunmaktadır. Herhangi bir uygulama indirmenize gerek kalmadan mobil tarayıcınız üzerinden platforma erişebilirsiniz.</p>

<h2>Android Erişim</h2>
<p>Android cihazınızdan $brand'e erişmek için Chrome, Firefox veya Brave tarayıcısını kullanabilirsiniz. Platformun mobil versiyonu tüm Android sürümleriyle uyumlu çalışmaktadır. Ana ekrana kısayol ekleyerek uygulama benzeri bir deneyim elde edebilirsiniz.</p>

<h2>iOS Erişim</h2>
<p>iPhone ve iPad kullanıcıları Safari tarayıcısı üzerinden $brand'e erişebilir. "Ana Ekrana Ekle" seçeneğini kullanarak platforma tek dokunuşla ulaşabilirsiniz. iOS 15 ve üzeri sürümlerle tam uyumludur.</p>

<h2>Mobil Özellikler</h2>
<ul>
<li>Tüm casino oyunlarına mobil erişim</li>
<li>Canlı bahis ve canlı casino desteği</li>
<li>Mobil para yatırma ve çekme işlemleri</li>
<li>Push bildirimleri ile kampanya takibi</li>
<li>Parmak izi ve yüz tanıma ile hızlı giriş</li>
</ul>

<h2>Mobil Performans İpuçları</h2>
<p>En iyi mobil deneyim için tarayıcınızı güncel tutun, stabil bir internet bağlantısı kullanın ve arka planda çalışan gereksiz uygulamaları kapatın. Wi-Fi bağlantısı özellikle canlı casino oyunlarında daha akıcı bir deneyim sunar.</p>
HTML
        ],
        [
            'slug' => 'locabet-kayit',
            'title' => "$brand Kayıt - Üyelik Nasıl Yapılır",
            'meta_title' => "$brand Kayıt 2026 – Hızlı Üyelik ve Hesap Açma Rehberi",
            'meta_description' => "$brand üyelik rehberi. Adım adım kayıt işlemi, hesap doğrulama ve ilk yatırım bonus avantajı.",
            'sort_order' => 8,
            'content' => <<<HTML
<h2>$brand Kayıt İşlemi</h2>
<p>$brand'e üye olmak hızlı ve kolaydır. Birkaç dakika içinde hesabınızı oluşturabilir ve platformun sunduğu tüm hizmetlerden yararlanmaya başlayabilirsiniz.</p>

<h2>Adım Adım Kayıt</h2>
<ol>
<li><strong>Siteye Girin:</strong> Güncel $brand giriş adresine tıklayın</li>
<li><strong>Kayıt Ol Butonu:</strong> Ana sayfada yer alan "Kayıt Ol" veya "Üye Ol" butonuna tıklayın</li>
<li><strong>Kişisel Bilgiler:</strong> Ad, soyad, doğum tarihi bilgilerinizi girin</li>
<li><strong>İletişim Bilgileri:</strong> Geçerli e-posta adresi ve telefon numaranızı yazın</li>
<li><strong>Kullanıcı Adı ve Şifre:</strong> Benzersiz bir kullanıcı adı ve güçlü bir şifre belirleyin</li>
<li><strong>Doğrulama:</strong> E-posta veya SMS ile hesabınızı doğrulayın</li>
<li><strong>İlk Yatırım:</strong> Hesabınıza ilk yatırımınızı yaparak bonus fırsatından yararlanın</li>
</ol>

<h2>Hesap Doğrulama</h2>
<p>Güvenliğiniz için hesap doğrulama işleminin tamamlanması gerekmektedir. Kimlik belgesi (nüfus cüzdanı veya ehliyet) ve adres belgesi (fatura veya banka ekstresi) göndermeniz istenebilir. Doğrulama işlemi genellikle 24 saat içinde tamamlanır.</p>

<h2>Kayıt Şartları</h2>
<ul>
<li>18 yaşından büyük olmak</li>
<li>Geçerli bir e-posta adresine sahip olmak</li>
<li>Doğru kişisel bilgiler vermek</li>
<li>Platform kurallarını kabul etmek</li>
<li>Her kullanıcı yalnızca bir hesap açabilir</li>
</ul>
HTML
        ],
        [
            'slug' => 'locabet-para-yatirma',
            'title' => "$brand Para Yatırma - Ödeme Yöntemleri",
            'meta_title' => "$brand Para Yatırma 2026 – Hızlı Ödeme Yöntemleri Rehberi",
            'meta_description' => "$brand para yatırma yöntemleri, minimum limitler ve işlem süreleri. Papara, kripto, banka havalesi rehberi.",
            'sort_order' => 9,
            'content' => <<<HTML
<h2>$brand Para Yatırma Yöntemleri</h2>
<p>$brand, kullanıcılarına çeşitli ödeme yöntemleri sunmaktadır. Hızlı ve güvenli para yatırma işlemleri için aşağıdaki yöntemlerden birini tercih edebilirsiniz.</p>

<h2>Ödeme Seçenekleri</h2>
<ul>
<li><strong>Papara:</strong> Anında yatırım, 7/24 aktif, minimum 50 TL</li>
<li><strong>Kripto Para:</strong> Bitcoin, Ethereum, USDT ile yatırım. Düşük komisyon, hızlı onay</li>
<li><strong>Banka Havalesi:</strong> EFT ve havale ile yatırım. İş günlerinde 15-30 dakika</li>
<li><strong>Jeton Cüzdan:</strong> E-cüzdan ile hızlı yatırım</li>
<li><strong>CMT Cüzdan:</strong> Pratik ve güvenli yatırım yöntemi</li>
</ul>

<h2>Para Çekme İşlemleri</h2>
<p>Kazançlarınızı çekmek için hesabınızın doğrulanmış olması gerekmektedir. Para çekme talepleri genellikle 1-24 saat içinde işleme alınır. Minimum çekim tutarı ve işlem süreleri seçilen yönteme göre değişiklik gösterir.</p>

<h2>Güvenli İşlem İpuçları</h2>
<p>Para yatırma ve çekme işlemlerinde güvenliğiniz için yalnızca kendi adınıza kayıtlı hesaplardan işlem yapın. Üçüncü şahıs hesaplarından yapılan transferler kabul edilmez. Şüpheli durumlarda müşteri hizmetleri ile iletişime geçin.</p>
HTML
        ],
        [
            'slug' => 'locabet-canli-casino',
            'title' => "$brand Canlı Casino - Gerçek Krupiyelerle Oynayın",
            'meta_title' => "$brand Canlı Casino 2026 – Canlı Rulet, Blackjack ve Poker",
            'meta_description' => "$brand canlı casino lobisi. Gerçek krupiyelerle canlı rulet, blackjack, baccarat ve poker masaları.",
            'sort_order' => 10,
            'content' => <<<HTML
<h2>$brand Canlı Casino</h2>
<p>$brand canlı casino bölümünde gerçek krupiyelerle, gerçek zamanlı oyun deneyimi yaşayabilirsiniz. Evolution Gaming, Pragmatic Play Live ve Ezugi gibi lider sağlayıcıların canlı masaları mevcuttur.</p>

<h2>Canlı Rulet</h2>
<p>Avrupa ruleti, Fransız ruleti, Lightning Roulette ve Speed Roulette gibi farklı rulet varyasyonlarında şansınızı deneyebilirsiniz. Türkçe masalar da dahil olmak üzere geniş bir masa seçeneği sunulmaktadır.</p>

<h2>Canlı Blackjack</h2>
<p>Klasik blackjack, VIP blackjack ve Speed Blackjack masalarında 21'e en yakın eli oluşturmaya çalışın. Farklı bahis limitleri ile her bütçeye uygun masalar mevcuttur.</p>

<h2>Game Show Oyunları</h2>
<p>Crazy Time, Dream Catcher, Monopoly Live ve Lightning Dice gibi interaktif game show oyunları $brand canlı casino bölümünde sizleri bekliyor. Bu oyunlar eğlenceli sunumları ve yüksek çarpanlarıyla dikkat çekmektedir.</p>

<h2>Canlı Casino İpuçları</h2>
<ul>
<li>Stabil bir internet bağlantısı kullanın</li>
<li>Bütçenizi önceden belirleyin</li>
<li>Masa limitlerini kontrol edin</li>
<li>Temel strateji tablolarından faydalanın</li>
<li>Sorumlu oyun ilkelerine uyun</li>
</ul>
HTML
        ],
        [
            'slug' => 'locabet-slot',
            'title' => "$brand Slot Oyunları - En Popüler Slotlar",
            'meta_title' => "$brand Slot Oyunları 2026 – Gates of Olympus, Sweet Bonanza",
            'meta_description' => "$brand slot oyunları rehberi. En popüler slotlar, RTP oranları, strateji ipuçları ve ücretsiz deneme.",
            'sort_order' => 11,
            'content' => <<<HTML
<h2>$brand Slot Oyunları</h2>
<p>$brand'de yüzlerce slot oyunu arasından size en uygun olanı seçebilirsiniz. Klasik meyve makinelerinden modern video slotlara, megaways mekaniklerinden jackpot oyunlarına kadar geniş bir koleksiyon sunulmaktadır.</p>

<h2>En Popüler Slotlar</h2>
<ul>
<li><strong>Gates of Olympus:</strong> Pragmatic Play'in efsanevi slot oyunu. %96.50 RTP, yüksek volatilite</li>
<li><strong>Sweet Bonanza:</strong> Renkli grafikleri ve tumble mekaniğiyle sevilen slot. %96.48 RTP</li>
<li><strong>The Dog House Megaways:</strong> 117.649 kazanma yolu, sticky wild özelliği</li>
<li><strong>Big Bass Bonanza:</strong> Balıkçılık temalı eğlenceli slot. %96.71 RTP</li>
<li><strong>Book of Dead:</strong> Mısır temalı klasik slot. %96.21 RTP</li>
<li><strong>Starlight Princess:</strong> Anime tarzı grafikleriyle dikkat çeken yüksek volatiliteli slot</li>
</ul>

<h2>Slot Türleri</h2>
<p><strong>Klasik Slotlar:</strong> 3 makaralı, basit mekanikli nostaljik oyunlar. <strong>Video Slotlar:</strong> 5 makaralı, bonus turlu modern oyunlar. <strong>Megaways:</strong> Her dönüşte değişen kazanma yolları sunan yenilikçi mekanik. <strong>Jackpot Slotlar:</strong> Büyük ödül havuzlu progresif jackpot oyunları.</p>

<h2>Slot Stratejileri</h2>
<p>Slot oyunlarında kesin kazanma garantisi olmasa da, akıllı stratejiler uygulayarak deneyiminizi optimize edebilirsiniz. Yüksek RTP'li oyunları tercih edin, bütçenizi yönetin ve bonus özelliklerini anlayın. Demo modunda pratik yaparak oyun mekaniklerini öğrenebilirsiniz.</p>
HTML
        ],
        [
            'slug' => 'locabet-guvenilir-mi',
            'title' => "$brand Güvenilir Mi - Detaylı İnceleme",
            'meta_title' => "$brand Güvenilir Mi? 2026 – Lisans ve Güvenlik Analizi",
            'meta_description' => "$brand güvenilir mi? Lisans bilgileri, SSL güvenliği, ödeme güvenilirliği ve kullanıcı yorumları ile detaylı inceleme.",
            'sort_order' => 12,
            'content' => <<<HTML
<h2>$brand Güvenilir Mi?</h2>
<p>$brand, kullanıcı güvenliğini ön planda tutan ve uluslararası lisanslı bir platform olarak hizmet vermektedir. Bu yazıda platformun güvenilirliğini çeşitli açılardan inceliyoruz.</p>

<h2>Lisans ve Yasal Durum</h2>
<p>$brand, uluslararası oyun otoriteleri tarafından lisanslanmış bir platformdur. Lisanslı platformlar düzenli denetimlere tabi tutulur ve adil oyun politikalarını uygulamak zorundadır.</p>

<h2>SSL Güvenliği</h2>
<p>Platform, 256-bit SSL şifreleme teknolojisi kullanmaktadır. Bu sayede tüm kişisel ve finansal verileriniz güvenli bir şekilde şifrelenerek korunmaktadır. Tarayıcınızın adres çubuğundaki kilit simgesini kontrol ederek SSL sertifikasını doğrulayabilirsiniz.</p>

<h2>Ödeme Güvenliği</h2>
<p>$brand, para yatırma ve çekme işlemlerinde güvenli ödeme altyapıları kullanmaktadır. Kullanıcıların ödemeleri zamanında ve eksiksiz olarak gerçekleştirilmektedir. Çekim talepleri belirtilen sürelerde işleme alınır.</p>

<h2>Kullanıcı Yorumları</h2>
<p>$brand kullanıcılarından gelen geri bildirimler genel olarak olumlu yöndedir. Hızlı ödeme, geniş oyun seçeneği ve 7/24 müşteri desteği, en çok beğenilen özellikler arasında yer almaktadır.</p>

<h2>Sorumlu Oyun</h2>
<p>$brand, sorumlu oyun ilkelerini benimsemektedir. Kullanıcılar hesaplarına yatırım limitleri koyabilir, oyun sürelerini sınırlayabilir ve gerektiğinde kendi kendine yasaklama uygulayabilir.</p>
HTML
        ],
        [
            'slug' => 'locabet-telegram',
            'title' => "$brand Telegram - Resmi Kanal ve Destek",
            'meta_title' => "$brand Telegram 2026 – Resmi Kanal, Güncel Adres Bildirimleri",
            'meta_description' => "$brand Telegram kanalı. Güncel adres bildirimleri, bonus duyuruları ve canlı destek. Resmi kanala katılın.",
            'sort_order' => 13,
            'content' => <<<HTML
<h2>$brand Telegram Kanalı</h2>
<p>$brand resmi Telegram kanalı, güncel giriş adresleri, bonus kampanyaları ve platform duyurularını takip edebileceğiniz en hızlı iletişim kanalıdır.</p>

<h2>Telegram Kanalının Avantajları</h2>
<ul>
<li><strong>Anlık Adres Güncellemeleri:</strong> Yeni giriş adresi değişikliklerinden anında haberdar olun</li>
<li><strong>Özel Bonus Kodları:</strong> Telegram'a özel bonus ve promosyon kodlarına erişin</li>
<li><strong>Hızlı Destek:</strong> Müşteri temsilcileriyle doğrudan iletişim kurun</li>
<li><strong>Duyurular:</strong> Bakım çalışmaları ve sistem güncellemelerinden haberdar olun</li>
<li><strong>Topluluk:</strong> Diğer kullanıcılarla deneyimlerinizi paylaşın</li>
</ul>

<h2>Nasıl Katılırım?</h2>
<ol>
<li>Telegram uygulamasını telefonunuza indirin</li>
<li>$brand resmi web sitesindeki Telegram linkine tıklayın</li>
<li>"Kanala Katıl" butonuna basın</li>
<li>Bildirimleri açık tutarak güncellemelerden haberdar olun</li>
</ol>

<h2>Güvenlik Uyarısı</h2>
<p>Yalnızca $brand'in resmi web sitesinde paylaşılan Telegram kanalına katılın. Üçüncü şahısların oluşturduğu sahte kanallara itibar etmeyin. Resmi kanal asla şifre, kişisel bilgi veya ödeme bilgisi talep etmez.</p>
HTML
        ],
    ];
}

// ─── SSS (FAQ) PAGE ───
function getSssPage($brand = 'Locabet') {
    return [
        'slug' => 'locabet-sss',
        'title' => "$brand Sık Sorulan Sorular (SSS)",
        'meta_title' => "$brand SSS 2026 – Sık Sorulan Sorular ve Cevapları",
        'meta_description' => "$brand hakkında sık sorulan sorular. Giriş, kayıt, bonus, para yatırma, çekme ve güvenlik konularında detaylı cevaplar.",
        'sort_order' => 14,
        'content' => <<<HTML
<h2>$brand Sık Sorulan Sorular</h2>
<p>$brand platformu hakkında en çok merak edilen soruları ve detaylı cevaplarını bu sayfada bulabilirsiniz.</p>

<h2>Genel Sorular</h2>

<h3>$brand nedir?</h3>
<p>$brand, online bahis ve casino oyunları sunan lisanslı bir platformdur. Spor bahisleri, canlı casino, slot oyunları ve daha birçok eğlence seçeneği sunmaktadır.</p>

<h3>$brand'e nasıl üye olurum?</h3>
<p>Güncel giriş adresine giderek "Kayıt Ol" butonuna tıklayın. Ad, soyad, e-posta ve telefon bilgilerinizi girin, güçlü bir şifre oluşturun ve e-posta doğrulamasını tamamlayın.</p>

<h3>$brand güvenilir mi?</h3>
<p>Evet, $brand uluslararası lisanslı bir platformdur. 256-bit SSL şifreleme teknolojisi kullanır ve kullanıcı verilerini güvenli sunucularda korur.</p>

<h2>Giriş ve Erişim Soruları</h2>

<h3>$brand'e giriş yapamıyorum, ne yapmalıyım?</h3>
<p>DNS ayarlarınızı Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) olarak değiştirin. Tarayıcı önbelleğinizi temizleyin veya alternatif bir tarayıcı deneyin. VPN kullanarak da erişim sağlayabilirsiniz.</p>

<h3>Güncel giriş adresini nasıl öğrenebilirim?</h3>
<p>$brand resmi Telegram kanalını takip ederek güncel adres bildirimlerini anında alabilirsiniz. Ayrıca bu sitede her zaman en güncel adresi bulabilirsiniz.</p>

<h3>Şifremi unuttum, nasıl sıfırlayabilirim?</h3>
<p>Giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayarak kayıtlı e-posta adresinize şifre sıfırlama linki gönderebilirsiniz.</p>

<h2>Bonus ve Kampanya Soruları</h2>

<h3>Hoş geldin bonusunu nasıl alırım?</h3>
<p>Platforma ilk kez üye olduktan sonra ilk yatırımınızı gerçekleştirdiğinizde hoş geldin bonusu otomatik olarak hesabınıza tanımlanır. Bazı durumlarda canlı destekten talep etmeniz gerekebilir.</p>

<h3>Bonus çevrim şartı ne demek?</h3>
<p>Bonus tutarının çekilebilir bakiyeye dönüşmesi için belirli bir miktarda bahis yapmanız gerekmektedir. Örneğin 10x çevrim şartı, bonus tutarının 10 katı kadar bahis yapmanız gerektiği anlamına gelir.</p>

<h3>Birden fazla bonus kullanabilir miyim?</h3>
<p>Aynı anda birden fazla bonus aktif edilmesi genellikle mümkün değildir. Mevcut bonusunuzu tamamladıktan veya iptal ettikten sonra yeni bonus talep edebilirsiniz.</p>

<h2>Para Yatırma ve Çekme Soruları</h2>

<h3>Hangi ödeme yöntemleriyle para yatırabilirim?</h3>
<p>Papara, kripto para (Bitcoin, Ethereum, USDT), banka havalesi, Jeton Cüzdan ve CMT Cüzdan gibi yöntemlerle yatırım yapabilirsiniz.</p>

<h3>Minimum para yatırma tutarı nedir?</h3>
<p>Minimum yatırım tutarı ödeme yöntemine göre değişmektedir. Genel olarak 50 TL ile 100 TL arasında değişen minimum limitler uygulanmaktadır.</p>

<h3>Para çekme işlemi ne kadar sürer?</h3>
<p>Para çekme talepleri genellikle 1-24 saat içinde işleme alınır. Hesabınızın doğrulanmış olması gerekir. Seçtiğiniz ödeme yöntemine göre süre değişiklik gösterebilir.</p>

<h3>Başka birinin hesabından para yatırabilir miyim?</h3>
<p>Hayır, güvenlik nedeniyle yalnızca kendi adınıza kayıtlı hesaplardan para yatırabilirsiniz. Üçüncü şahıs hesaplarından yapılan transferler kabul edilmez.</p>

<h2>Casino ve Oyun Soruları</h2>

<h3>Oyunları ücretsiz deneyebilir miyim?</h3>
<p>Evet, birçok slot oyununu demo modunda ücretsiz deneyebilirsiniz. Canlı casino oyunları için ise gerçek bakiye gereklidir.</p>

<h3>Canlı casino oyunları mobilde çalışır mı?</h3>
<p>Evet, canlı casino oyunları mobil cihazlarda sorunsuz çalışmaktadır. Stabil bir internet bağlantısı önerilir.</p>

<h3>Hangi oyun sağlayıcıları mevcut?</h3>
<p>Pragmatic Play, Evolution Gaming, NetEnt, Microgaming, Play'n GO, Ezugi ve daha birçok lider oyun sağlayıcısının oyunları $brand'de mevcuttur.</p>

<h2>Güvenlik Soruları</h2>

<h3>Kişisel bilgilerim güvende mi?</h3>
<p>$brand, 256-bit SSL şifreleme teknolojisi kullanarak tüm kişisel ve finansal verileri korumaktadır. Verileriniz güvenli sunucularda saklanır ve üçüncü taraflarla paylaşılmaz.</p>

<h3>İki faktörlü doğrulama (2FA) var mı?</h3>
<p>Evet, hesap güvenliğinizi artırmak için iki faktörlü doğrulama özelliğini aktif edebilirsiniz. Güvenlik ayarlarından bu özelliği etkinleştirebilirsiniz.</p>

<h2>Destek Soruları</h2>

<h3>Müşteri desteğine nasıl ulaşabilirim?</h3>
<p>$brand 7/24 canlı destek hizmeti sunmaktadır. Canlı sohbet, e-posta veya Telegram kanalı üzerinden müşteri hizmetlerine ulaşabilirsiniz.</p>

<h3>Şikayet ve önerilerimi nereye iletebilirim?</h3>
<p>Şikayet ve önerilerinizi canlı destek hattı üzerinden veya resmi e-posta adresine göndererek iletebilirsiniz. Talepleriniz en kısa sürede değerlendirilir.</p>
HTML
    ];
}

// ─── POST DEFINITIONS ───
function getPostDefinitions($brand = 'Locabet') {
    return [
        [
            'slug' => 'locabet-giris-nasil-yapilir-2026',
            'title' => "$brand Giriş Nasıl Yapılır? 2026 Güncel Rehber",
            'excerpt' => "$brand platformuna giriş yapma adımları, güncel adres bilgileri ve erişim sorunlarının çözümü.",
            'meta_title' => "$brand Giriş Nasıl Yapılır? 2026 – Adım Adım Rehber",
            'meta_description' => "$brand giriş rehberi. Güncel adres, DNS ayarları, VPN kullanımı ve mobil giriş adımları.",
            'category_slug' => 'erisim',
            'content' => <<<HTML
<h2>$brand Giriş Rehberi 2026</h2>
<p>$brand platformuna güvenli ve hızlı bir şekilde giriş yapmak istiyorsanız, bu rehberi takip edebilirsiniz. Adım adım giriş işlemi, erişim sorunlarının çözümü ve güvenli bağlantı yöntemlerini detaylı olarak açıklıyoruz.</p>

<h2>Adım Adım Giriş</h2>
<ol>
<li>$brand güncel giriş adresine tıklayın veya tarayıcınıza yazın</li>
<li>Sağ üst köşedeki "Giriş Yap" butonuna tıklayın</li>
<li>Kullanıcı adınızı veya kayıtlı e-posta adresinizi girin</li>
<li>Şifrenizi yazarak "Giriş" butonuna tıklayın</li>
<li>İki faktörlü doğrulama aktifse doğrulama kodunu girin</li>
</ol>

<h2>Erişim Sorunları İçin Çözümler</h2>
<p>$brand'e erişimde problem yaşıyorsanız, aşağıdaki yöntemleri sırasıyla deneyebilirsiniz. DNS değişikliği en yaygın ve etkili çözüm yöntemidir.</p>

<h3>DNS Ayarı Değiştirme</h3>
<p>Windows kullanıcıları Ağ ve Paylaşım Merkezi'nden, Mac kullanıcıları Sistem Tercihleri'nden DNS sunucusunu 8.8.8.8 (Google) veya 1.1.1.1 (Cloudflare) olarak değiştirebilir.</p>

<h3>VPN ile Erişim</h3>
<p>Güvenilir bir VPN uygulaması indirerek farklı bir lokasyondan bağlanabilirsiniz. NordVPN, ExpressVPN veya ücretsiz Proton VPN tercih edilebilir.</p>

<h3>Tarayıcı Önbellek Temizleme</h3>
<p>Chrome'da Ctrl+Shift+Delete, Firefox'ta Ctrl+Shift+Delete, Safari'de tercihlerden geçmişi temizleyebilirsiniz.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>$brand'e neden giriş yapamıyorum?</h3>
<p>Erişim engeli, DNS sorunları veya alan adı değişikliği nedeniyle giriş yapamıyor olabilirsiniz. Yukarıdaki çözüm yöntemlerini deneyin.</p>

<h3>Şifremi nasıl sıfırlarım?</h3>
<p>Giriş ekranındaki "Şifremi Unuttum" bağlantısından e-posta ile şifre sıfırlama linki alabilirsiniz.</p>
HTML
        ],
        [
            'slug' => 'locabet-bonus-rehberi-2026',
            'title' => "$brand Bonus Rehberi 2026 – Tüm Kampanyalar",
            'excerpt' => "$brand güncel bonus kampanyaları, çevrim şartları ve bonus kullanım ipuçları.",
            'meta_title' => "$brand Bonus Rehberi 2026 – Hoş Geldin ve Yatırım Bonusları",
            'meta_description' => "$brand bonus rehberi. Güncel kampanyalar, çevrim şartları ve bonus avantajları hakkında detaylı bilgi.",
            'category_slug' => 'bonus',
            'content' => <<<HTML
<h2>$brand Bonus Kampanyaları 2026</h2>
<p>$brand platformu, kullanıcılarına çeşitli bonus kampanyaları sunarak oyun deneyimini daha avantajlı hale getirmektedir. Bu rehberde tüm güncel bonus seçeneklerini ve kullanım koşullarını detaylı olarak inceliyoruz.</p>

<h2>Hoş Geldin Bonusu</h2>
<p>$brand'e ilk kez üye olan kullanıcılar, ilk yatırımlarında özel hoş geldin bonusundan yararlanabilir. Bonus miktarı yatırım tutarınıza oranla belirlenir ve kampanya kurallarına göre değişiklik gösterebilir.</p>

<h2>Yatırım Bonusu</h2>
<p>Her yatırımınızda ekstra bakiye kazanma imkanı sunan yatırım bonusları, düzenli oyuncular için büyük avantaj sağlamaktadır. Ödeme yöntemine göre farklı bonus oranları uygulanabilir.</p>

<h2>Kayıp Bonusu</h2>
<p>Belirli dönemlerde kayıplarınızın bir kısmı iade edilmektedir. Haftalık veya aylık olarak hesaplanan kayıp bonusu, aktif oyuncular için önemli bir geri kazanım fırsatıdır.</p>

<h2>Çevrim Şartları Nedir?</h2>
<p>Bonusların çekilebilir bakiyeye dönüşmesi için belirli çevrim şartları vardır. Örneğin 10x çevrim, bonus tutarının 10 katı kadar bahis yapmanız gerektiği anlamına gelir. Her bonus türü için farklı çevrim koşulları uygulanır.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>Bonus çekilebilir mi?</h3>
<p>Bonus tutarı doğrudan çekilemez, ancak çevrim şartı tamamlandığında elde edilen kazançlar çekilebilir.</p>

<h3>Bonus süresi var mı?</h3>
<p>Evet, her bonusun belirli bir kullanım süresi vardır. Süre dolmadan çevrim şartlarını tamamlamanız gerekir.</p>
HTML
        ],
        [
            'slug' => 'locabet-casino-rehberi',
            'title' => "$brand Casino Rehberi – En İyi Oyunlar ve Stratejiler",
            'excerpt' => "$brand casino oyunları hakkında kapsamlı rehber. Slot, rulet, blackjack stratejileri.",
            'meta_title' => "$brand Casino Rehberi 2026 – Oyun Seçimi ve Strateji İpuçları",
            'meta_description' => "$brand casino rehberi. En popüler slot oyunları, masa oyunları ve kazanma stratejileri hakkında detaylı bilgi.",
            'category_slug' => 'oyun',
            'content' => <<<HTML
<h2>$brand Casino Oyunları Rehberi</h2>
<p>$brand casino lobisinde binlerce oyun seçeneği bulunmaktadır. Bu rehberde en popüler oyunları, strateji ipuçlarını ve oyun seçiminde dikkat edilmesi gereken noktaları ele alıyoruz.</p>

<h2>Slot Oyunlarında Dikkat Edilecekler</h2>
<p>Slot oyunlarında RTP (Return to Player) oranı, oyunun uzun vadedeki geri ödeme yüzdesini gösterir. %96 ve üzeri RTP'li oyunlar tercih edilmelidir. Volatilite seviyesi de önemlidir: düşük volatilite sık ama küçük kazançlar, yüksek volatilite nadir ama büyük kazançlar sunar.</p>

<h2>Masa Oyunları Stratejileri</h2>
<p>Blackjack'te temel strateji tablosu takip edilmelidir. Rulette Avrupa ruleti (tek sıfır) tercih edilmelidir. Baccarat'ta banker bahsi en düşük kasa avantajına sahiptir.</p>

<h2>Sorumlu Oyun</h2>
<p>Her zaman bütçenizi belirleyin ve bu bütçeyi aşmayın. Kaybettiğiniz parayı kovalamayın. Eğlenmek için oynayın, gelir kaynağı olarak görmeyin.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>Hangi slot oyununu seçmeliyim?</h3>
<p>RTP'si %96 üzerinde olan oyunları tercih edin. Gates of Olympus, Sweet Bonanza ve Big Bass Bonanza popüler seçeneklerdir.</p>

<h3>Casino oyunlarını ücretsiz deneyebilir miyim?</h3>
<p>Evet, çoğu slot oyununu demo modunda ücretsiz deneyebilirsiniz.</p>
HTML
        ],
        [
            'slug' => 'locabet-mobil-erisim-rehberi',
            'title' => "$brand Mobil Erişim – Android ve iOS Rehberi",
            'excerpt' => "$brand mobil erişim rehberi. Android ve iOS cihazlardan platforma nasıl erişilir.",
            'meta_title' => "$brand Mobil Erişim 2026 – Telefondan Giriş Rehberi",
            'meta_description' => "$brand mobil erişim rehberi. Android APK, iOS Safari erişimi ve mobil optimizasyon ipuçları.",
            'category_slug' => 'mobil',
            'content' => <<<HTML
<h2>$brand Mobil Erişim Rehberi</h2>
<p>$brand platformuna mobil cihazlarınızdan kolayca erişebilirsiniz. Bu rehberde Android ve iOS cihazlardan platforma erişim yöntemlerini, mobil optimizasyon ipuçlarını ve mobil deneyimi iyileştirme yollarını anlatıyoruz.</p>

<h2>Android Cihazlardan Erişim</h2>
<p>Android telefonunuzdan Chrome veya Firefox tarayıcısını açın ve $brand güncel adresini yazın. Tarayıcının menüsünden "Ana Ekrana Ekle" seçeneğini kullanarak uygulaması gibi platforma tek dokunuşla ulaşabilirsiniz.</p>

<h2>iOS Cihazlardan Erişim</h2>
<p>iPhone veya iPad kullanıyorsanız Safari tarayıcısını açın. Paylaş butonuna dokunun ve "Ana Ekrana Ekle" seçeneğini seçin. Bu sayede uygulaması gibi bir kısayol oluşturmuş olursunuz.</p>

<h2>Mobil Performans İpuçları</h2>
<ul>
<li>Tarayıcınızı her zaman güncel tutun</li>
<li>Wi-Fi bağlantısı kullanarak daha stabil erişim sağlayın</li>
<li>Arka planda çalışan gereksiz uygulamaları kapatın</li>
<li>Tarayıcı önbelleğini düzenli temizleyin</li>
</ul>

<h2>Sık Sorulan Sorular</h2>
<h3>$brand mobil uygulaması var mı?</h3>
<p>$brand'in özel bir mobil uygulaması bulunmamaktadır, ancak mobil uyumlu web sitesi uygulama benzeri bir deneyim sunmaktadır.</p>

<h3>Mobilde tüm oyunlar çalışır mı?</h3>
<p>Evet, HTML5 teknolojisi sayesinde tüm casino ve slot oyunları mobil cihazlarda sorunsuz çalışmaktadır.</p>
HTML
        ],
        [
            'slug' => 'locabet-papara-yatirma',
            'title' => "$brand Papara ile Para Yatırma Rehberi",
            'excerpt' => "$brand'e Papara ile nasıl para yatırılır? Adım adım rehber ve limitler.",
            'meta_title' => "$brand Papara Yatırma 2026 – Hızlı ve Kolay Para Yatırma",
            'meta_description' => "$brand Papara ile para yatırma rehberi. Adım adım yatırım, limitler ve sık sorulan sorular.",
            'category_slug' => 'odeme',
            'content' => <<<HTML
<h2>$brand Papara ile Para Yatırma</h2>
<p>Papara, $brand platformunda en çok tercih edilen ödeme yöntemlerinden biridir. Hızlı işlem süreleri, düşük komisyon oranları ve 7/24 kullanılabilirlik avantajlarıyla öne çıkmaktadır.</p>

<h2>Papara ile Yatırım Adımları</h2>
<ol>
<li>$brand hesabınıza giriş yapın</li>
<li>"Para Yatır" bölümüne gidin</li>
<li>Ödeme yöntemi olarak "Papara" seçin</li>
<li>Yatırmak istediğiniz tutarı girin</li>
<li>Papara hesap numarasını kopyalayın</li>
<li>Papara uygulamasından belirtilen hesaba transfer yapın</li>
<li>Bakiyeniz birkaç dakika içinde hesabınıza yansır</li>
</ol>

<h2>Papara Yatırım Limitleri</h2>
<p>Minimum yatırım tutarı 50 TL olarak belirlenmiştir. Maksimum yatırım limiti hesap doğrulama seviyenize göre değişiklik gösterebilir. Detaylı limit bilgisi için canlı destek hattına başvurabilirsiniz.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>Papara ile para çekebilir miyim?</h3>
<p>Evet, $brand'den Papara hesabınıza para çekimi yapabilirsiniz. Çekim talebi genellikle 1-12 saat içinde işleme alınır.</p>

<h3>Papara yatırımına bonus var mı?</h3>
<p>Evet, Papara ile yapılan yatırımlara özel bonus kampanyaları zaman zaman düzenlenmektedir.</p>
HTML
        ],
        [
            'slug' => 'locabet-kripto-yatirma',
            'title' => "$brand Kripto Para ile Yatırım Rehberi",
            'excerpt' => "$brand'e Bitcoin, Ethereum ve USDT ile nasıl para yatırılır.",
            'meta_title' => "$brand Kripto Yatırma 2026 – Bitcoin ve USDT Rehberi",
            'meta_description' => "$brand kripto para ile yatırım rehberi. Bitcoin, Ethereum, USDT yatırma adımları ve avantajlar.",
            'category_slug' => 'odeme',
            'content' => <<<HTML
<h2>$brand Kripto Para ile Yatırım</h2>
<p>$brand, Bitcoin, Ethereum ve USDT gibi popüler kripto paralarla para yatırma imkanı sunmaktadır. Kripto para yatırımları düşük komisyon, hızlı işlem süresi ve yüksek güvenlik avantajları sağlar.</p>

<h2>Desteklenen Kripto Paralar</h2>
<ul>
<li><strong>Bitcoin (BTC):</strong> En yaygın kullanılan kripto para</li>
<li><strong>Ethereum (ETH):</strong> Hızlı transfer süreleri</li>
<li><strong>Tether (USDT):</strong> Dolar sabitli stablecoin, kur riski yok</li>
<li><strong>Litecoin (LTC):</strong> Düşük transfer ücreti</li>
</ul>

<h2>Kripto Yatırım Adımları</h2>
<ol>
<li>$brand hesabınızda "Para Yatır" bölümüne gidin</li>
<li>Kripto para yöntemini ve coin türünü seçin</li>
<li>Gösterilen cüzdan adresini kopyalayın veya QR kodu tarayın</li>
<li>Kripto cüzdanınızdan belirtilen adrese transfer yapın</li>
<li>Blockchain onayından sonra bakiyeniz yansır (genellikle 10-30 dakika)</li>
</ol>

<h2>Kripto Yatırım Avantajları</h2>
<p>Kimlik doğrulaması gerektirmeden hızlı işlem, düşük transfer komisyonları, yüksek güvenlik ve 7/24 kullanılabilirlik kripto yatırımın başlıca avantajlarıdır.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>Kripto ile para çekebilir miyim?</h3>
<p>Evet, kazançlarınızı kripto cüzdanınıza çekebilirsiniz. Çekim talebi onaylandıktan sonra blockchain ağına gönderilir.</p>

<h3>Yanlış ağa gönderim yaparsam ne olur?</h3>
<p>Kripto transferlerinde doğru ağ (network) seçimine dikkat edin. Yanlış ağa gönderilen tutarlar geri alınamayabilir.</p>
HTML
        ],
        [
            'slug' => 'locabet-slot-stratejileri',
            'title' => "$brand Slot Stratejileri – Kazanma İpuçları 2026",
            'excerpt' => "Slot oyunlarında kazanma şansınızı artırma ipuçları ve strateji rehberi.",
            'meta_title' => "$brand Slot Stratejileri 2026 – RTP ve Volatilite Rehberi",
            'meta_description' => "$brand slot stratejileri. RTP oranları, volatilite analizi, bütçe yönetimi ve bonus turları hakkında ipuçları.",
            'category_slug' => 'oyun',
            'content' => <<<HTML
<h2>$brand Slot Stratejileri</h2>
<p>Slot oyunları tamamen şansa dayalı olsa da, bilinçli oyun stratejileri uygulayarak deneyiminizi optimize edebilirsiniz. Bu rehberde slot oyunlarında dikkat edilmesi gereken temel stratejileri ele alıyoruz.</p>

<h2>RTP Oranını Anlayın</h2>
<p>RTP (Return to Player), bir slot oyununun uzun vadede oyunculara geri ödediği yüzdeyi gösterir. Örneğin %96 RTP, her 100 TL bahiste ortalama 96 TL geri ödeme anlamına gelir. Daima %95 üzeri RTP'li oyunları tercih edin.</p>

<h2>Volatilite Seçimi</h2>
<p><strong>Düşük Volatilite:</strong> Sık kazanç, küçük miktarlar. Uzun süre oynamak isteyenler için ideal. <strong>Yüksek Volatilite:</strong> Nadir kazanç, büyük miktarlar. Risk seven oyuncular için. <strong>Orta Volatilite:</strong> Dengeli bir oyun deneyimi sunar.</p>

<h2>Bütçe Yönetimi</h2>
<ul>
<li>Oynamadan önce bütçenizi belirleyin ve bunu aşmayın</li>
<li>Kazancınızın bir kısmını her zaman ayırın</li>
<li>Tek bir spin'e toplam bütçenizin %2'sinden fazlasını yatırmayın</li>
<li>Kaybettikçe bahis miktarını artırmayın</li>
</ul>

<h2>Demo Modu Kullanımı</h2>
<p>Yeni bir slot oyununu gerçek parayla oynamadan önce demo modunda deneyin. Bu sayede oyunun mekaniğini, bonus özelliklerini ve ödeme tablolarını risksiz bir şekilde öğrenebilirsiniz.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>En çok kazandıran slot hangisi?</h3>
<p>Slot oyunları şansa dayalıdır. Ancak yüksek RTP'li oyunlar istatistiksel olarak daha fazla geri ödeme sunar. Gates of Olympus (%96.50) ve Sweet Bonanza (%96.48) popüler seçeneklerdir.</p>
HTML
        ],
        [
            'slug' => 'locabet-canli-casino-rehberi',
            'title' => "$brand Canlı Casino Rehberi – Masa Oyunları İpuçları",
            'excerpt' => "$brand canlı casino rehberi. Rulet, blackjack ve poker masalarında strateji ipuçları.",
            'meta_title' => "$brand Canlı Casino Rehberi 2026 – Rulet ve Blackjack İpuçları",
            'meta_description' => "$brand canlı casino rehberi. Canlı rulet stratejileri, blackjack temel strateji, baccarat ve poker ipuçları.",
            'category_slug' => 'oyun',
            'content' => <<<HTML
<h2>$brand Canlı Casino Rehberi</h2>
<p>Canlı casino oyunları, gerçek krupiyelerle gerçek zamanlı oynama deneyimi sunmaktadır. $brand'de Evolution Gaming, Pragmatic Play Live ve Ezugi gibi lider sağlayıcıların masaları mevcuttur.</p>

<h2>Canlı Rulet Stratejileri</h2>
<p>Rulet tamamen şansa dayalı bir oyundur, ancak bazı bahis stratejileri riskinizi yönetmenize yardımcı olabilir. Avrupa ruletini tercih edin (tek sıfır, %2.7 kasa avantajı). Dış bahisler (kırmızı/siyah, tek/çift) daha sık kazanç sağlar.</p>

<h2>Blackjack Temel Stratejisi</h2>
<p>Blackjack'te temel strateji tablosunu takip ederek kasa avantajını minimize edebilirsiniz. 11'de her zaman double down yapın. Krupiye 2-6 gösteriyorken 12-16'da dur. 8'leri ve as'ları her zaman bölün.</p>

<h2>Game Show Oyunları</h2>
<p>Crazy Time, Lightning Roulette ve Monopoly Live gibi eğlenceli game show formatındaki oyunlar hem eğlence hem de büyük kazanç potansiyeli sunmaktadır.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>Canlı casino masalarında minimum bahis ne kadar?</h3>
<p>Masalara göre değişmekle birlikte, genellikle 5 TL'den başlayan minimum bahis limitleri bulunmaktadır.</p>

<h3>Canlı casino mobilde çalışır mı?</h3>
<p>Evet, tüm canlı casino oyunları mobil cihazlarda sorunsuz çalışmaktadır. Stabil internet bağlantısı önerilir.</p>
HTML
        ],
        [
            'slug' => 'locabet-guvenlik-rehberi',
            'title' => "$brand Güvenlik Rehberi – Hesap Koruma İpuçları",
            'excerpt' => "$brand'de hesap güvenliğinizi sağlamak için bilmeniz gereken her şey.",
            'meta_title' => "$brand Güvenlik Rehberi 2026 – Hesap Güvenliği ve Koruma",
            'meta_description' => "$brand güvenlik rehberi. İki faktörlü doğrulama, güçlü şifre, phishing koruması ve güvenli ödeme ipuçları.",
            'category_slug' => 'guvenlik',
            'content' => <<<HTML
<h2>$brand Güvenlik Rehberi</h2>
<p>Online platformlarda güvenlik son derece önemlidir. $brand, kullanıcı güvenliğini en üst düzeyde tutmak için çeşitli güvenlik önlemleri uygulamaktadır. Bu rehberde hesabınızı korumanız için yapmanız gerekenleri anlatıyoruz.</p>

<h2>Güçlü Şifre Oluşturma</h2>
<p>Şifreniz en az 8 karakter uzunluğunda olmalı ve büyük harf, küçük harf, rakam ve özel karakter içermelidir. Doğum tarihinizi veya yaygın kelimeleri şifre olarak kullanmaktan kaçının. Her platform için farklı şifre kullanmanız önerilir.</p>

<h2>İki Faktörlü Doğrulama (2FA)</h2>
<p>Hesabınıza ekstra güvenlik katmanı eklemek için iki faktörlü doğrulamayı aktif edin. Google Authenticator veya SMS doğrulama ile her girişte ek bir güvenlik kodu istenecektir.</p>

<h2>Phishing Saldırılarından Korunma</h2>
<ul>
<li>Yalnızca resmi $brand adresinden giriş yapın</li>
<li>Şüpheli e-postalardaki bağlantılara tıklamayın</li>
<li>Hiçbir durumda şifrenizi başkalarıyla paylaşmayın</li>
<li>SSL sertifikasını (adres çubuğundaki kilit simgesi) kontrol edin</li>
</ul>

<h2>Güvenli Ödeme İpuçları</h2>
<p>Para yatırma ve çekme işlemlerinde yalnızca kendi adınıza kayıtlı hesapları kullanın. Halka açık Wi-Fi ağlarından finansal işlem yapmaktan kaçının. İşlem geçmişinizi düzenli olarak kontrol edin.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>Hesabım ele geçirildi, ne yapmalıyım?</h3>
<p>Hemen canlı destek hattına başvurarak hesabınızı geçici olarak dondurun. Şifrenizi sıfırlayın ve iki faktörlü doğrulamayı aktif edin.</p>
HTML
        ],
        [
            'slug' => 'locabet-uyelik-avantajlari',
            'title' => "$brand Üyelik Avantajları – Neden $brand?",
            'excerpt' => "$brand'e üye olmanın avantajları. Bonus fırsatları, geniş oyun seçeneği ve 7/24 destek.",
            'meta_title' => "$brand Üyelik Avantajları 2026 – Neden $brand Tercih Edilmeli?",
            'meta_description' => "$brand üyelik avantajları. Bonus kampanyaları, geniş oyun yelpazesi, hızlı ödeme ve 7/24 müşteri desteği.",
            'category_slug' => 'genel',
            'content' => <<<HTML
<h2>$brand Üyelik Avantajları</h2>
<p>$brand, kullanıcılarına sunduğu çeşitli avantajlarla sektörde öne çıkmaktadır. Bu yazıda $brand üyesi olmanın sağladığı ayrıcalıkları detaylı olarak inceliyoruz.</p>

<h2>Geniş Oyun Yelpazesi</h2>
<p>Slot oyunlarından canlı casinoya, spor bahislerinden sanal oyunlara kadar geniş bir yelpazede oyun seçeneği sunulmaktadır. Pragmatic Play, Evolution Gaming, NetEnt gibi dünya çapında tanınan sağlayıcıların oyunlarına erişebilirsiniz.</p>

<h2>Avantajlı Bonus Sistemi</h2>
<p>Hoş geldin bonusu, yatırım bonusu, kayıp bonusu ve özel kampanyalarla oyun deneyiminizi daha avantajlı hale getirebilirsiniz. Düzenli olarak güncellenen kampanyalardan yararlanabilirsiniz.</p>

<h2>Hızlı ve Güvenli Ödemeler</h2>
<p>Papara, kripto para, banka havalesi ve e-cüzdan gibi çeşitli ödeme yöntemleriyle hızlı para yatırma ve çekme işlemleri gerçekleştirebilirsiniz. Çekim talepleri hızla işleme alınır.</p>

<h2>7/24 Müşteri Desteği</h2>
<p>$brand, 7 gün 24 saat canlı destek hizmeti sunmaktadır. Canlı sohbet, e-posta ve Telegram üzerinden müşteri hizmetlerine ulaşabilirsiniz.</p>

<h2>Mobil Uyumluluk</h2>
<p>Responsive tasarım sayesinde her cihazdan sorunsuz erişim sağlayabilirsiniz. Android ve iOS cihazlarla tam uyumlu çalışan platform, mobilde de masaüstü kalitesinde deneyim sunar.</p>

<h2>Sık Sorulan Sorular</h2>
<h3>$brand'e üye olmak ücretli mi?</h3>
<p>Hayır, $brand'e üyelik tamamen ücretsizdir. Kayıt işlemi birkaç dakika içinde tamamlanır.</p>
HTML
        ],
    ];
}

// ─── MAIN EXECUTION ───

$totalPages = 0;
$totalPosts = 0;

foreach ($domains as $domain) {
    echo "\n═══════════════════════════════════\n";
    echo "  Processing: $domain\n";
    echo "═══════════════════════════════════\n";

    $site = \App\Models\Site::where('domain', $domain)->first();
    if (!$site) {
        echo "  ❌ Site not found: $domain\n";
        continue;
    }

    Config::set('database.connections.tenant.database', $site->db_name);
    DB::purge('tenant');
    DB::reconnect('tenant');

    $db = DB::connection('tenant');

    // ─── INSERT PAGES ───
    $pages = getPageDefinitions();
    $sssPage = getSssPage();
    $allPages = array_merge($pages, [$sssPage]);

    $pageCount = 0;
    foreach ($allPages as $p) {
        $exists = $db->table('pages')->where('slug', $p['slug'])->exists();
        if ($exists) {
            echo "  ⏭ Page exists: {$p['slug']}\n";
            continue;
        }

        $db->table('pages')->insert([
            'slug' => $p['slug'],
            'title' => $p['title'],
            'content' => $p['content'],
            'meta_title' => $p['meta_title'],
            'meta_description' => $p['meta_description'],
            'is_published' => true,
            'sort_order' => $p['sort_order'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $pageCount++;
        echo "  ✅ Page: {$p['slug']}\n";
    }

    echo "  Pages added: $pageCount\n";
    $totalPages += $pageCount;

    // ─── INSERT POSTS ───
    $posts = getPostDefinitions();
    $postCount = 0;

    // Get category mapping
    $categories = $db->table('categories')->pluck('id', 'slug')->toArray();

    foreach ($posts as $post) {
        $exists = $db->table('posts')->where('slug', $post['slug'])->exists();
        if ($exists) {
            echo "  ⏭ Post exists: {$post['slug']}\n";
            continue;
        }

        $categoryId = $categories[$post['category_slug']] ?? null;

        $db->table('posts')->insert([
            'slug' => $post['slug'],
            'title' => $post['title'],
            'excerpt' => $post['excerpt'],
            'content' => $post['content'],
            'meta_title' => $post['meta_title'],
            'meta_description' => $post['meta_description'],
            'category_id' => $categoryId,
            'is_published' => true,
            'published_at' => now()->subDays(rand(1, 30)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $postCount++;
        echo "  ✅ Post: {$post['slug']}\n";
    }

    echo "  Posts added: $postCount\n";
    $totalPosts += $postCount;

    // ─── VERIFY TOTALS ───
    $totalPagesInDb = $db->table('pages')->where('is_published', true)->count();
    $totalPostsInDb = $db->table('posts')->where('is_published', true)->count();
    echo "  📊 Total: $totalPagesInDb pages, $totalPostsInDb posts\n";
}

echo "\n\n════════════════════════\n";
echo "  SUMMARY\n";
echo "════════════════════════\n";
echo "  New pages created: $totalPages\n";
echo "  New posts created: $totalPosts\n";
echo "  Done!\n";
