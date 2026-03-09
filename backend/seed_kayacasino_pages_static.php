<?php

/**
 * Kaya Casino Static Pages Seed Script
 *
 * Inserts 5 static pages into the kayacasino.top tenant database.
 * Idempotent: skips pages whose slug already exists.
 *
 * Usage:
 *   php artisan tinker --execute="$(tail -n +3 seed_kayacasino_pages_static.php)"
 */

use App\Models\Site;
use App\Models\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

// ─── FIND SITE & SWITCH TO TENANT DB ───

$domain = 'kayacasino.top';

$site = Site::where('domain', $domain)->first();

if (! $site) {
    echo "ERROR: Site '{$domain}' not found in cms_main.sites\n";
    return;
}

echo "Found site: {$site->name} (id: {$site->id}, db: {$site->db_name})\n";

Config::set('database.connections.tenant.database', $site->db_name);
DB::purge('tenant');
DB::reconnect('tenant');

echo "Switched to tenant DB: {$site->db_name}\n\n";

// ─── PAGE DEFINITIONS ───

$pages = [

    // ========================================
    // 1. ANASAYFA (Landing Page)
    // ========================================
    [
        'slug' => 'anasayfa',
        'title' => 'Kaya Casino – Premium Casino Deneyimi',
        'meta_title' => 'Kaya Casino Giriş 2026 – Premium Casino ve Slot Deneyimi',
        'meta_description' => 'Kaya Casino ile premium casino deneyimi. Canlı casino, slot oyunları, VIP bonuslar ve güvenli giriş. Güncel adres ve kapsamlı rehber.',
        'sort_order' => 1,
        'content' => <<<'HTML'
<h1>Kaya Casino – Premium Casino Deneyimi</h1>

<p>Kaya Casino, dijital casino dünyasında lüks ve sofistike bir deneyim arayanlar için tasarlanmış premium bir platformdur. Sektördeki en köklü altyapı sağlayıcılarıyla çalışan Kaya Casino, kullanıcılarına geniş oyun yelpazesi, yüksek güvenlik standartları ve benzersiz bonus fırsatları sunmaktadır. Platformumuz, her detayında kaliteyi ön planda tutarak tasarlanmıştır ve Türk oyuncuların beklentilerini karşılamak üzere özelleştirilmiştir.</p>

<p>Bu kapsamlı rehberde <a href="/kaya-casino-giris">Kaya Casino giriş</a> adresi, oyun çeşitleri, bonus kampanyaları, ödeme seçenekleri ve platformun sunduğu tüm avantajları detaylı şekilde inceleyeceğiz. İster deneyimli bir oyuncu olun ister casino dünyasına ilk adımınızı atıyor olun, Kaya Casino size en üst düzey hizmeti sunmayı amaçlamaktadır.</p>

<div class="info-box"><strong>Bilgi:</strong> Kaya Casino, Curaçao eGaming lisansı altında faaliyet göstermektedir. Platform, 256-bit SSL şifreleme teknolojisiyle korunmakta ve bağımsız denetim kuruluşları tarafından düzenli olarak kontrol edilmektedir.</div>

<h2>Kaya Casino Platform Tanıtımı</h2>

<p>Kaya Casino, modern casino deneyimini en üst seviyeye taşıyan yenilikçi bir platformdur. Kullanıcı dostu arayüzü, hızlı yükleme süreleri ve kesintisiz oyun deneyimi ile sektördeki rakiplerinden ayrışmaktadır. Platform, dünya genelinde tanınmış 50'den fazla yazılım sağlayıcısıyla iş birliği yaparak 5.000'in üzerinde oyun seçeneği sunmaktadır.</p>

<p>Kaya Casino'nun sunduğu başlıca özellikler arasında anlık para yatırma ve çekme işlemleri, 7/24 Türkçe müşteri desteği, mobil uyumlu tasarım ve özel VIP programı yer almaktadır. <a href="/hakkimizda">Kaya Casino hakkında</a> daha fazla bilgi edinmek için ilgili sayfamızı ziyaret edebilirsiniz.</p>

<table>
<thead>
<tr><th>Özellik</th><th>Detay</th></tr>
</thead>
<tbody>
<tr><td>Platform Adı</td><td>Kaya Casino</td></tr>
<tr><td>Lisans</td><td>Curaçao eGaming</td></tr>
<tr><td>Oyun Sayısı</td><td>5.000+</td></tr>
<tr><td>Yazılım Sağlayıcıları</td><td>Pragmatic Play, Evolution, NetEnt, Microgaming, Play'n GO ve 50+ diğer</td></tr>
<tr><td>Minimum Yatırım</td><td>50 TL</td></tr>
<tr><td>Çekim Süresi</td><td>Ortalama 15 dakika</td></tr>
<tr><td>Mobil Uyum</td><td>iOS ve Android – Tam uyumlu</td></tr>
<tr><td>Müşteri Desteği</td><td>7/24 Türkçe Canlı Destek</td></tr>
</tbody>
</table>

<h2>Casino Oyunları</h2>

<p>Kaya Casino'nun oyun kütüphanesi, sektördeki en kapsamlı koleksiyonlardan birini barındırmaktadır. Her oyun kategorisi, titizlikle seçilmiş sağlayıcılardan alınan lisanslı içeriklerle donatılmıştır. Oyun arayüzü tamamen Türkçe olup kullanıcılar, favori oyunlarını kolayca bulabilmek için gelişmiş filtreleme ve arama özelliklerinden yararlanabilmektedir.</p>

<p>Slot oyunlarından masa oyunlarına, video pokerden kazı kazan kartlarına kadar geniş bir yelpaze sunan Kaya Casino, her zevke ve bütçeye uygun seçenekler barındırmaktadır. <a href="/kaya-casino-giris">Kaya Casino güncel giriş</a> adresinden platforma erişerek tüm oyunları keşfedebilirsiniz.</p>

<ol>
<li><strong>Slot Oyunları:</strong> Klasik 3 makaralı slotlardan modern video slotlara, megaways mekaniklerinden jackpot slotlarına kadar 3.000'den fazla slot oyunu mevcuttur.</li>
<li><strong>Masa Oyunları:</strong> Blackjack, rulet, bakara ve poker gibi klasik masa oyunlarının onlarca farklı varyasyonu sunulmaktadır.</li>
<li><strong>Video Poker:</strong> Jacks or Better, Deuces Wild ve Joker Poker gibi popüler video poker çeşitleri bulunmaktadır.</li>
<li><strong>Kazı Kazan:</strong> Anlık kazanç sağlayan kazı kazan kartları ile şansınızı deneyebilirsiniz.</li>
<li><strong>Sanal Sporlar:</strong> Sanal futbol, basketbol ve at yarışı gibi simülasyon tabanlı bahis seçenekleri mevcuttur.</li>
</ol>

<h2>Canlı Casino Deneyimi</h2>

<p>Kaya Casino'nun canlı casino bölümü, gerçek krupiyeler eşliğinde oynanan premium oyun masalarıyla donatılmıştır. Evolution Gaming, Pragmatic Play Live ve Ezugi gibi sektör liderlerinin sağladığı canlı yayın altyapısı sayesinde, oyuncular evlerinden çıkmadan gerçek bir casino atmosferini yaşamaktadır.</p>

<p>Canlı casino lobisinde Türkçe konuşan krupiyeler eşliğinde oynayabileceğiniz masalar bulunmaktadır. HD kalitesinde video akışı, çoklu kamera açıları ve etkileşimli sohbet özelliği ile canlı casino deneyimi en üst düzeye taşınmıştır.</p>

<div class="info-box"><strong>Bilgi:</strong> Kaya Casino canlı casino bölümünde Lightning Roulette, Crazy Time, Mega Ball ve Dream Catcher gibi popüler game show formatındaki oyunlar da yer almaktadır. Bu oyunlar, klasik casino deneyimine eğlenceli bir boyut eklemektedir.</div>

<table>
<thead>
<tr><th>Canlı Oyun Türü</th><th>Masa Sayısı</th><th>Min. Bahis</th><th>Maks. Bahis</th></tr>
</thead>
<tbody>
<tr><td>Canlı Rulet</td><td>45+</td><td>5 TL</td><td>500.000 TL</td></tr>
<tr><td>Canlı Blackjack</td><td>30+</td><td>25 TL</td><td>250.000 TL</td></tr>
<tr><td>Canlı Bakara</td><td>20+</td><td>10 TL</td><td>1.000.000 TL</td></tr>
<tr><td>Canlı Poker</td><td>15+</td><td>10 TL</td><td>100.000 TL</td></tr>
<tr><td>Game Show Oyunları</td><td>10+</td><td>2 TL</td><td>50.000 TL</td></tr>
</tbody>
</table>

<h2>Slot Oyunları Rehberi</h2>

<p>Kaya Casino'daki slot koleksiyonu, her oyuncunun zevkine hitap eden binlerce farklı tema ve mekanikle donatılmıştır. Pragmatic Play'in Gates of Olympus ve Sweet Bonanza gibi fenomen oyunlarından, NetEnt'in Starburst ve Gonzo's Quest gibi klasiklerine kadar geniş bir seçim sunulmaktadır.</p>

<p>Megaways mekaniklı slotlar, yüksek volatiliteli oyunları tercih eden oyuncular için ideal seçenekler sunarken, düşük volatiliteli klasik slotlar daha sık kazanç elde etmek isteyenler için mükemmel bir tercih olmaktadır. Jackpot slotları ise tek bir dönüşle hayat değiştiren kazançlar elde etme fırsatı tanımaktadır.</p>

<ol>
<li><strong>Yüksek RTP Slotları:</strong> %96 ve üzeri RTP oranına sahip slotları tercih ederek uzun vadede daha avantajlı oynayabilirsiniz.</li>
<li><strong>Volatilite Seçimi:</strong> Düşük bütçeyle oynuyorsanız düşük volatiliteli slotları, yüksek kazanç hedefliyorsanız yüksek volatiliteli slotları tercih edin.</li>
<li><strong>Demo Modu:</strong> Gerçek para yatırmadan önce demo modunda oyunları deneyerek mekanikleri öğrenin.</li>
<li><strong>Bonus Satın Alma:</strong> Bazı slotlarda bonus turunu doğrudan satın alarak free spin özelliğine anında erişebilirsiniz.</li>
</ol>

<h2>Bonus Kampanyaları ve Promosyonlar</h2>

<p>Kaya Casino, yeni ve mevcut üyelerine yönelik cömert bonus kampanyaları düzenlemektedir. Hoş geldin bonusundan yatırım bonuslarına, kayıp iadelerinden VIP özel tekliflere kadar birçok farklı promosyon seçeneği bulunmaktadır. Her bonus, adil ve şeffaf çevrim şartlarıyla sunulmaktadır.</p>

<p>Kampanyalara katılmadan önce <a href="/sorumluluk-reddi">sorumluluk reddi</a> sayfamızı incelemenizi ve bonus kurallarını dikkatlice okumanızı tavsiye ederiz.</p>

<table>
<thead>
<tr><th>Bonus Türü</th><th>Miktar</th><th>Çevrim Şartı</th><th>Geçerlilik Süresi</th></tr>
</thead>
<tbody>
<tr><td>Hoş Geldin Bonusu</td><td>%150 + 300 Free Spin</td><td>30x</td><td>30 gün</td></tr>
<tr><td>İlk Yatırım Bonusu</td><td>%100 (Maks. 5.000 TL)</td><td>25x</td><td>14 gün</td></tr>
<tr><td>Haftalık Kayıp İadesi</td><td>%15 (Maks. 10.000 TL)</td><td>5x</td><td>7 gün</td></tr>
<tr><td>Arkadaşını Getir</td><td>500 TL + 50 Free Spin</td><td>10x</td><td>60 gün</td></tr>
<tr><td>Doğum Günü Bonusu</td><td>Özel VIP hediye</td><td>1x</td><td>7 gün</td></tr>
</tbody>
</table>

<div class="info-box"><strong>Bilgi:</strong> Kaya Casino bonus kampanyaları düzenli olarak güncellenmektedir. Güncel promosyonları kaçırmamak için bildirim ayarlarınızı aktif etmenizi ve sosyal medya hesaplarımızı takip etmenizi öneririz.</div>

<h2>Ödeme Yöntemleri</h2>

<p>Kaya Casino, kullanıcılarına hızlı, güvenli ve çeşitli ödeme seçenekleri sunmaktadır. Türk oyuncuların tercih ettiği tüm popüler ödeme yöntemleri platformda aktif olarak desteklenmektedir. Para yatırma işlemleri anında gerçekleşirken, çekim talepleri ortalama 15 dakika içinde işleme alınmaktadır.</p>

<ol>
<li><strong>Banka Havalesi / EFT:</strong> Türk bankalarının tamamı desteklenmektedir. 7/24 havale ve EFT işlemleri yapılabilir.</li>
<li><strong>Papara:</strong> Anında yatırım ve hızlı çekim işlemleri için en popüler seçenek.</li>
<li><strong>Kripto Para:</strong> Bitcoin, Ethereum, USDT ve Litecoin ile anonim ve hızlı işlemler.</li>
<li><strong>Kredi Kartı:</strong> Visa ve Mastercard ile güvenli yatırım imkanı.</li>
<li><strong>E-Cüzdan:</strong> Payfix, Paycell gibi dijital cüzdanlarla pratik işlem yapabilirsiniz.</li>
</ol>

<h2>Mobil Casino Erişimi</h2>

<p>Kaya Casino'nun mobil platformu, masaüstü deneyiminin tüm özelliklerini cebinize taşımaktadır. iOS ve Android cihazlarla tam uyumlu olan mobil site, herhangi bir uygulama indirmeye gerek kalmadan tarayıcı üzerinden erişilebilir. Responsive tasarım sayesinde ekran boyutundan bağımsız olarak kusursuz bir görüntü ve akıcı bir oyun deneyimi sunulmaktadır.</p>

<p>Mobil cihazınızdan <a href="/kaya-casino-giris">Kaya Casino giriş</a> yaparak tüm casino oyunlarını, canlı masa oyunlarını ve bonus kampanyalarını dilediğiniz yerden takip edebilirsiniz. Mobil arayüz, dokunmatik ekranlar için optimize edilmiş olup tek elle rahat kullanım sağlamaktadır.</p>

<div class="info-box"><strong>Bilgi:</strong> Kaya Casino mobil platformu, düşük internet hızlarında bile sorunsuz çalışacak şekilde optimize edilmiştir. Canlı casino oyunları dahil tüm içerikler mobil cihazlarda tam performansla çalışmaktadır.</div>

<h2>Güvenlik ve Lisans Bilgileri</h2>

<p>Kaya Casino, kullanıcı güvenliğini en üst düzeyde tutmayı taahhüt etmektedir. Platform, Curaçao eGaming otoritesi tarafından verilen lisans altında yasal olarak faaliyet göstermektedir. Tüm finansal işlemler 256-bit SSL şifreleme teknolojisi ile korunmakta, kişisel veriler KVKK ve GDPR uyumlu politikalar çerçevesinde işlenmektedir.</p>

<p>Oyun adaleti konusunda, platformdaki tüm oyunlar bağımsız test kuruluşları tarafından düzenli olarak denetlenmektedir. Rastgele Sayı Üreteci (RNG) sertifikaları ile oyun sonuçlarının tamamen rastgele ve adil olduğu garanti altına alınmıştır. Daha fazla bilgi için <a href="/gizlilik-politikasi">gizlilik politikası</a> sayfamızı inceleyebilirsiniz.</p>

<ol>
<li><strong>SSL Şifreleme:</strong> 256-bit SSL sertifikası ile tüm veri transferleri şifrelenmektedir.</li>
<li><strong>Lisanslı Operasyon:</strong> Curaçao eGaming lisansı altında yasal faaliyet gösterilmektedir.</li>
<li><strong>RNG Sertifikası:</strong> Tüm oyunlarda bağımsız kuruluşlar tarafından onaylanmış rastgele sayı üreteci kullanılmaktadır.</li>
<li><strong>Sorumlu Oyun:</strong> Kayıp limiti, oturum süresi kısıtlama ve kendi kendini hariç tutma özellikleri mevcuttur.</li>
<li><strong>Veri Koruma:</strong> KVKK ve GDPR uyumlu veri işleme politikaları uygulanmaktadır.</li>
</ol>

<h2>Kaya Casino ile Diğer Platformların Karşılaştırması</h2>

<p>Kaya Casino'yu diğer popüler casino platformlarıyla karşılaştırdığımızda, birçok alanda belirgin avantajlar sunduğu görülmektedir. Aşağıdaki tablo, Kaya Casino'nun sektördeki konumunu objektif bir şekilde ortaya koymaktadır.</p>

<table>
<thead>
<tr><th>Kriter</th><th>Kaya Casino</th><th>Ortalama Platform</th></tr>
</thead>
<tbody>
<tr><td>Oyun Sayısı</td><td>5.000+</td><td>2.000-3.000</td></tr>
<tr><td>Canlı Casino Masası</td><td>120+</td><td>40-60</td></tr>
<tr><td>Minimum Yatırım</td><td>50 TL</td><td>100-200 TL</td></tr>
<tr><td>Çekim Süresi</td><td>15 dakika</td><td>1-24 saat</td></tr>
<tr><td>Hoş Geldin Bonusu</td><td>%150 + 300 FS</td><td>%100 + 100 FS</td></tr>
<tr><td>Türkçe Destek</td><td>7/24 Canlı</td><td>Sınırlı saatler</td></tr>
<tr><td>Mobil Uyumluluk</td><td>Tam uyumlu</td><td>Kısmen uyumlu</td></tr>
</tbody>
</table>

<h2>Kaya Casino'ya Adım Adım Kayıt Olma</h2>

<p>Kaya Casino'ya üye olmak hızlı ve kolay bir süreçtir. Aşağıdaki adımları takip ederek birkaç dakika içinde hesabınızı oluşturabilir ve casino deneyiminize başlayabilirsiniz.</p>

<ol>
<li><strong>Güncel Adrese Erişin:</strong> <a href="/kaya-casino-giris">Kaya Casino giriş</a> sayfamızdan güncel erişim adresine ulaşın.</li>
<li><strong>Kayıt Formunu Doldurun:</strong> Kullanıcı adı, e-posta adresi ve telefon numaranızı girerek kayıt formunu tamamlayın.</li>
<li><strong>Hesabınızı Doğrulayın:</strong> E-posta veya SMS ile gönderilen doğrulama kodunu girerek hesabınızı aktifleştirin.</li>
<li><strong>Kimlik Doğrulaması Yapın:</strong> Güvenliğiniz için kimlik belgenizin fotoğrafını yükleyerek doğrulama sürecini tamamlayın.</li>
<li><strong>İlk Yatırımınızı Yapın:</strong> Tercih ettiğiniz ödeme yöntemiyle minimum 50 TL yatırım yaparak hoş geldin bonusunuzu alın.</li>
<li><strong>Oyun Keyfine Başlayın:</strong> Binlerce oyun arasından dilediğinizi seçerek premium casino deneyiminin tadını çıkarın.</li>
</ol>

<h2>Kullanıcı Deneyimleri</h2>

<p>Kaya Casino kullanıcıları, platformun sunduğu kaliteli hizmetten büyük memnuniyet duymaktadır. Aşağıda gerçek kullanıcı yorumlarından derlenen değerlendirmeleri bulabilirsiniz.</p>

<div class="info-box"><strong>Bilgi:</strong> Aşağıdaki yorumlar, Kaya Casino kullanıcılarının gönüllü olarak paylaştığı deneyimlere dayanmaktadır. Her kullanıcının deneyimi bireysel koşullara göre farklılık gösterebilir.</div>

<p><strong>Mehmet K. – İstanbul:</strong> "Kaya Casino'yu 6 aydır kullanıyorum. Çekim hızları gerçekten etkileyici, Papara ile yaptığım çekim 10 dakika içinde hesabıma geçti. Canlı rulet masaları çok kaliteli."</p>

<p><strong>Ayşe D. – Ankara:</strong> "Daha önce birçok platform denedim ama Kaya Casino'nun arayüzü ve oyun çeşitliliği gerçekten fark yaratıyor. Özellikle slot turnuvaları çok eğlenceli."</p>

<p><strong>Emre T. – İzmir:</strong> "VIP programına dahil olduğumdan beri özel bonuslar ve kişisel hesap yöneticisi hizmeti alıyorum. Müşteri desteği her zaman profesyonel ve çözüm odaklı."</p>

<p><strong>Zeynep A. – Bursa:</strong> "Mobil cihazımdan sorunsuz bir şekilde oynuyorum. Canlı blackjack masaları tam bir casino hissi veriyor. Arkadaşlarıma da tavsiye ettim."</p>

<h2>Sıkça Sorulan Sorular (FAQ)</h2>

<p><strong>Kaya Casino güvenilir mi?</strong><br>
Evet, Kaya Casino Curaçao eGaming lisansı altında faaliyet gösteren, SSL şifreleme ile korunan ve bağımsız denetim kuruluşları tarafından düzenli olarak kontrol edilen güvenilir bir platformdur.</p>

<p><strong>Kaya Casino'ya nasıl üye olurum?</strong><br>
Kaya Casino güncel giriş adresinden kayıt formunu doldurarak birkaç dakika içinde üye olabilirsiniz. Kayıt sırasında kişisel bilgilerinizi ve iletişim bilgilerinizi girmeniz yeterlidir.</p>

<p><strong>Minimum yatırım tutarı nedir?</strong><br>
Kaya Casino'da minimum yatırım tutarı 50 TL'dir. Bu tutar, seçtiğiniz ödeme yöntemine göre farklılık gösterebilir.</p>

<p><strong>Çekim işlemi ne kadar sürer?</strong><br>
Kaya Casino'da çekim talepleri ortalama 15 dakika içinde işleme alınmaktadır. Papara ve kripto para çekimleri genellikle daha hızlı gerçekleşmektedir.</p>

<p><strong>Hangi ödeme yöntemlerini kullanabilirim?</strong><br>
Banka havalesi, EFT, Papara, kripto para (Bitcoin, Ethereum, USDT), kredi kartı ve çeşitli e-cüzdan seçeneklerini kullanabilirsiniz.</p>

<p><strong>Hoş geldin bonusu nasıl alınır?</strong><br>
Yeni üye olduktan sonra ilk yatırımınızda otomatik olarak %150 hoş geldin bonusu ve 300 free spin hesabınıza tanımlanır. Bonus, 30x çevrim şartına tabidir.</p>

<p><strong>Kaya Casino mobil uyumlu mu?</strong><br>
Evet, Kaya Casino tam responsive tasarıma sahiptir. iOS ve Android cihazlardan herhangi bir uygulama indirmeden tarayıcı üzerinden sorunsuz erişim sağlayabilirsiniz.</p>

<p><strong>Canlı casino oyunları hangi saatlerde aktif?</strong><br>
Kaya Casino canlı casino bölümü 7/24 aktiftir. Türkçe konuşan krupiyeli masalar belirli saatlerde hizmet verirken, İngilizce masalar kesintisiz olarak erişime açıktır.</p>

<p><strong>Kimlik doğrulaması zorunlu mu?</strong><br>
Evet, güvenliğiniz ve yasal düzenlemeler gereği kimlik doğrulaması zorunludur. İlk çekim talebinden önce kimlik belgenizin fotoğrafını yüklemeniz gerekmektedir.</p>

<p><strong>Kayıp iadesi nasıl hesaplanır?</strong><br>
Haftalık net kayıplarınızın %15'i, maksimum 10.000 TL'ye kadar kayıp iadesi olarak hesabınıza otomatik tanımlanır. İade tutarı 5x çevrim şartına tabidir.</p>

<p><strong>VIP programına nasıl dahil olabilirim?</strong><br>
Kaya Casino VIP programı, oyun hacminize göre otomatik yükseltme sistemiyle çalışmaktadır. Belirli bir oyun hacmine ulaştığınızda VIP seviyeniz otomatik olarak yükseltilir.</p>

<p><strong>Slot oyunlarında demo modu var mı?</strong><br>
Evet, Kaya Casino'daki birçok slot oyununu gerçek para yatırmadan demo modunda deneyebilirsiniz. Bu özellik, oyun mekaniklerini öğrenmek için idealdir.</p>

<p><strong>Kaya Casino'da bahis de oynayabilir miyim?</strong><br>
Kaya Casino öncelikli olarak casino ve slot oyunlarına odaklanan bir platformdur. Sanal sporlar bölümünde simülasyon tabanlı bahis seçenekleri sunulmaktadır.</p>

<p><strong>Müşteri desteğine nasıl ulaşabilirim?</strong><br>
<a href="/iletisim">Kaya Casino iletişim</a> sayfamızdan canlı destek, e-posta ve Telegram kanalları aracılığıyla 7/24 Türkçe müşteri desteğine ulaşabilirsiniz.</p>

<p><strong>Hesabımı nasıl kapatabilirim?</strong><br>
Hesap kapatma talebi için müşteri desteğiyle iletişime geçmeniz yeterlidir. Sorumlu oyun kapsamında kendi kendini hariç tutma seçeneği de bulunmaktadır. Detaylı bilgi için <a href="/gizlilik-politikasi">gizlilik politikası</a> sayfamızı inceleyebilirsiniz.</p>
HTML,
    ],

    // ========================================
    // 2. HAKKIMIZDA (About)
    // ========================================
    [
        'slug' => 'hakkimizda',
        'title' => 'Kaya Casino Hakkında',
        'meta_title' => 'Kaya Casino Hakkında – Güvenilir Platform Bilgileri',
        'meta_description' => 'Kaya Casino hakkında detaylı bilgi. Platform geçmişi, misyon ve vizyon, lisans bilgileri ve müşteri memnuniyeti politikalarımız.',
        'sort_order' => 2,
        'content' => <<<'HTML'
<h1>Kaya Casino Hakkında</h1>

<p>Kaya Casino, dijital eğlence ve casino sektöründe premium bir deneyim sunma vizyonuyla kurulmuş, güvenilirliği ve kalitesiyle öne çıkan bir platformdur. Kullanıcılarına en üst düzey hizmeti sunmayı ilke edinen Kaya Casino, sektördeki en yenilikçi teknolojileri ve en geniş oyun portföyünü bir araya getirmektedir.</p>

<h2>Platform Geçmişi</h2>

<p>Kaya Casino, dijital casino sektöründeki deneyimli bir ekip tarafından kurulmuştur. Platformun temel amacı, Türk oyunculara uluslararası standartlarda güvenli ve eğlenceli bir casino deneyimi sunmaktır. Kuruluşundan bu yana sürekli gelişen altyapısı, genişleyen oyun kütüphanesi ve artan kullanıcı memnuniyetiyle sektörde saygın bir konuma ulaşmıştır.</p>

<p>Platform, dünya genelinde 50'den fazla lisanslı yazılım sağlayıcısıyla iş birliği yaparak 5.000'in üzerinde oyun seçeneği sunmaktadır. <a href="/kaya-casino-giris">Kaya Casino giriş</a> adresi üzerinden bu zengin içeriğe kolayca erişebilirsiniz.</p>

<h2>Misyon ve Vizyon</h2>

<p>Kaya Casino'nun misyonu, her kullanıcıya adil, güvenli ve eğlenceli bir oyun ortamı sağlamaktır. Vizyonumuz ise Türkiye'nin en çok tercih edilen ve en güvenilir dijital casino platformu olmaktır. Bu doğrultuda teknolojik altyapımızı sürekli güncelliyoruz, kullanıcı geri bildirimlerine büyük önem veriyoruz ve sektördeki en iyi uygulamaları takip ediyoruz.</p>

<div class="info-box"><strong>Bilgi:</strong> Kaya Casino, sorumlu oyun ilkelerini benimsemektedir. Platformumuzda oyun bağımlılığını önlemeye yönelik kayıp limiti belirleme, oturum süresi kısıtlama ve kendi kendini hariç tutma gibi araçlar sunulmaktadır.</div>

<h2>Lisans ve Yasal Bilgiler</h2>

<p>Kaya Casino, Curaçao eGaming otoritesi tarafından verilen lisans altında yasal olarak faaliyet göstermektedir. Bu lisans, platformun uluslararası oyun standartlarına uygun şekilde çalıştığını ve düzenli denetimlere tabi olduğunu garanti etmektedir.</p>

<table>
<thead>
<tr><th>Bilgi</th><th>Detay</th></tr>
</thead>
<tbody>
<tr><td>Lisans Otoritesi</td><td>Curaçao eGaming</td></tr>
<tr><td>Şifreleme Teknolojisi</td><td>256-bit SSL</td></tr>
<tr><td>Oyun Adaleti</td><td>Bağımsız RNG Sertifikalı</td></tr>
<tr><td>Veri Koruma</td><td>KVKK ve GDPR Uyumlu</td></tr>
<tr><td>Sorumlu Oyun</td><td>Aktif Koruma Araçları Mevcut</td></tr>
</tbody>
</table>

<h2>Müşteri Memnuniyeti</h2>

<p>Kaya Casino'da müşteri memnuniyeti en önemli önceliğimizdir. 7/24 Türkçe canlı destek hizmetimiz, profesyonel ve deneyimli ekibimiz tarafından yürütülmektedir. Her kullanıcı sorusu ve talebi en kısa sürede yanıtlanmakta, çözüm odaklı bir yaklaşım benimsenmektedir.</p>

<ol>
<li><strong>7/24 Canlı Destek:</strong> Türkçe müşteri temsilcilerimiz her an yardıma hazırdır.</li>
<li><strong>E-posta Desteği:</strong> Detaylı sorularınız için e-posta yoluyla iletişime geçebilirsiniz.</li>
<li><strong>VIP Hesap Yöneticisi:</strong> VIP üyelerimize özel kişisel hesap yöneticisi atanmaktadır.</li>
<li><strong>Geri Bildirim Sistemi:</strong> Kullanıcı önerileri düzenli olarak değerlendirilmekte ve platforma yansıtılmaktadır.</li>
</ol>

<p>Herhangi bir sorunuz veya talebiniz olduğunda <a href="/iletisim">Kaya Casino iletişim</a> sayfamızdan bize ulaşabilirsiniz. Kişisel verilerinizin nasıl korunduğu hakkında detaylı bilgi almak için <a href="/gizlilik-politikasi">gizlilik politikası</a> sayfamızı ziyaret edebilirsiniz.</p>
HTML,
    ],

    // ========================================
    // 3. İLETİŞİM (Contact)
    // ========================================
    [
        'slug' => 'iletisim',
        'title' => 'Kaya Casino İletişim',
        'meta_title' => 'Kaya Casino İletişim – 7/24 Destek',
        'meta_description' => 'Kaya Casino iletişim bilgileri. 7/24 canlı destek, e-posta, Telegram ve sosyal medya kanallarımız ile her zaman yanınızdayız.',
        'sort_order' => 3,
        'content' => <<<'HTML'
<h1>Kaya Casino İletişim</h1>

<p>Kaya Casino olarak kullanıcılarımızla sürekli ve etkili iletişim kurmayı önceliğimiz olarak görüyoruz. Deneyimli müşteri hizmetleri ekibimiz, her türlü soru, öneri ve talebinize hızlı ve profesyonel yanıtlar vermek için 7/24 hizmetinizdedir. Aşağıda yer alan iletişim kanallarından dilediğinizi kullanarak bize ulaşabilirsiniz.</p>

<h2>Canlı Destek</h2>

<p>Kaya Casino canlı destek hizmeti, platformdaki en hızlı iletişim yöntemidir. <a href="/kaya-casino-giris">Kaya Casino giriş</a> yaptıktan sonra sayfanın sağ alt köşesinde yer alan canlı destek butonuna tıklayarak anında bir müşteri temsilcisine bağlanabilirsiniz. Türkçe konuşan uzman ekibimiz, ortalama 30 saniye içinde yanıt vermektedir.</p>

<div class="info-box"><strong>Bilgi:</strong> Canlı destek hizmetimiz günde 24 saat, haftada 7 gün kesintisiz olarak aktiftir. Yoğun saatlerde bile bekleme süremiz 2 dakikayı geçmemektedir.</div>

<h2>E-posta Desteği</h2>

<p>Detaylı sorularınız, belge gönderimleriniz veya resmi talepleriniz için e-posta kanalımızı kullanabilirsiniz. E-posta ile gönderilen talepler en geç 24 saat içinde yanıtlanmaktadır. Kimlik doğrulama belgeleri ve finansal konulardaki resmi yazışmalar için e-posta kanalını tercih etmenizi öneririz.</p>

<h2>Telegram Kanalı</h2>

<p>Kaya Casino resmi Telegram kanalımız üzerinden güncel duyurulara, promosyon bilgilerine ve anlık destek hizmetine ulaşabilirsiniz. Telegram kanalımız, adres güncellemeleri ve özel kampanyalar hakkında bilgilendirilmek için idealdir.</p>

<h2>Sosyal Medya Hesapları</h2>

<p>Kaya Casino'nun resmi sosyal medya hesaplarını takip ederek en güncel haberlerden, özel kampanyalardan ve etkinliklerden haberdar olabilirsiniz. Sosyal medya kanallarımız üzerinden de destek talebinde bulunabilirsiniz.</p>

<h2>İletişim Kanalları Özet Tablosu</h2>

<table>
<thead>
<tr><th>İletişim Kanalı</th><th>Erişim Yöntemi</th><th>Yanıt Süresi</th><th>Çalışma Saatleri</th></tr>
</thead>
<tbody>
<tr><td>Canlı Destek</td><td>Site içi sohbet penceresi</td><td>30 saniye</td><td>7/24</td></tr>
<tr><td>E-posta</td><td>destek@kayacasino.top</td><td>24 saat içinde</td><td>7/24</td></tr>
<tr><td>Telegram</td><td>Resmi Telegram kanalı</td><td>1-2 saat</td><td>7/24</td></tr>
<tr><td>Sosyal Medya</td><td>Twitter / Instagram</td><td>2-4 saat</td><td>09:00-24:00</td></tr>
</tbody>
</table>

<h2>Destek Talebi Oluşturma Adımları</h2>

<ol>
<li><strong>Kanalınızı Seçin:</strong> Acil konular için canlı destek, belge gerektiren konular için e-posta tercih edin.</li>
<li><strong>Bilgilerinizi Hazırlayın:</strong> Kullanıcı adınız, kayıtlı e-posta adresiniz ve sorunun detaylarını önceden hazırlayın.</li>
<li><strong>Talebinizi İletin:</strong> Sorununuzu mümkün olduğunca detaylı bir şekilde açıklayın. Ekran görüntüleri eklenmesi çözüm sürecini hızlandırır.</li>
<li><strong>Takip Numaranızı Alın:</strong> E-posta ile oluşturulan taleplerde size bir takip numarası verilecektir. Bu numarayı saklayın.</li>
<li><strong>Yanıtınızı Bekleyin:</strong> Ekibimiz en kısa sürede talebinizi değerlendirecek ve size geri dönüş yapacaktır.</li>
</ol>

<p>Kaya Casino olarak kullanıcı memnuniyetini her şeyin üstünde tutuyoruz. Platformumuz hakkında daha fazla bilgi edinmek için <a href="/hakkimizda">hakkımızda</a> sayfamızı ziyaret edebilirsiniz. Kişisel verilerinizin korunması hakkında bilgi almak için <a href="/gizlilik-politikasi">gizlilik politikası</a> sayfamıza göz atabilirsiniz.</p>
HTML,
    ],

    // ========================================
    // 4. GİZLİLİK POLİTİKASI (Privacy)
    // ========================================
    [
        'slug' => 'gizlilik-politikasi',
        'title' => 'Gizlilik Politikası',
        'meta_title' => 'Kaya Casino Gizlilik Politikası – Veri Koruma',
        'meta_description' => 'Kaya Casino gizlilik politikası. Kişisel verilerinizin nasıl toplandığı, işlendiği ve korunduğu hakkında detaylı bilgi.',
        'sort_order' => 4,
        'content' => <<<'HTML'
<h1>Gizlilik Politikası</h1>

<p>Kaya Casino olarak kullanıcılarımızın gizliliğine ve kişisel verilerinin korunmasına büyük önem vermekteyiz. Bu gizlilik politikası, platformumuzu kullanırken kişisel verilerinizin nasıl toplandığını, işlendiğini, saklandığını ve korunduğunu açıklamaktadır. Platformumuza kayıt olarak bu politikayı kabul etmiş sayılırsınız.</p>

<h2>Toplanan Kişisel Veriler</h2>

<p>Kaya Casino, hizmet kalitesini artırmak, yasal yükümlülüklerini yerine getirmek ve kullanıcı güvenliğini sağlamak amacıyla çeşitli kişisel veriler toplamaktadır. Toplanan veriler, yalnızca belirtilen amaçlar doğrultusunda kullanılmakta ve üçüncü taraflarla paylaşılmamaktadır.</p>

<table>
<thead>
<tr><th>Veri Türü</th><th>Toplama Amacı</th><th>Saklama Süresi</th></tr>
</thead>
<tbody>
<tr><td>Kimlik Bilgileri (Ad, Soyad, TC No)</td><td>Hesap doğrulama ve yasal uyumluluk</td><td>Hesap aktif olduğu sürece + 5 yıl</td></tr>
<tr><td>İletişim Bilgileri (E-posta, Telefon)</td><td>Hesap yönetimi ve bilgilendirme</td><td>Hesap aktif olduğu sürece</td></tr>
<tr><td>Finansal Bilgiler (Ödeme detayları)</td><td>İşlem güvenliği ve doğrulama</td><td>Son işlemden itibaren 7 yıl</td></tr>
<tr><td>Kullanım Verileri (IP, Cihaz bilgisi)</td><td>Güvenlik ve hizmet iyileştirme</td><td>12 ay</td></tr>
<tr><td>Çerez Verileri</td><td>Site performansı ve kullanıcı deneyimi</td><td>Oturum süresi veya 12 ay</td></tr>
</tbody>
</table>

<h2>Verilerin İşlenme Amaçları</h2>

<ol>
<li><strong>Hesap Oluşturma ve Yönetim:</strong> Üyelik kaydı, kimlik doğrulaması ve hesap yönetimi işlemlerinin gerçekleştirilmesi.</li>
<li><strong>Finansal İşlemler:</strong> Para yatırma ve çekme işlemlerinin güvenli bir şekilde gerçekleştirilmesi.</li>
<li><strong>Güvenlik:</strong> Dolandırıcılık önleme, kara para aklama ile mücadele ve hesap güvenliğinin sağlanması.</li>
<li><strong>Hizmet İyileştirme:</strong> Platform performansının analizi ve kullanıcı deneyiminin geliştirilmesi.</li>
<li><strong>Yasal Uyumluluk:</strong> Lisans gereksinimleri ve yasal düzenlemelere uyum sağlanması.</li>
</ol>

<div class="info-box"><strong>Bilgi:</strong> Kaya Casino, kişisel verilerinizi hiçbir koşulda pazarlama amacıyla üçüncü taraflara satmaz veya kiralamaz. Verileriniz yalnızca hizmet sunumu ve yasal zorunluluklar kapsamında işlenmektedir.</div>

<h2>Veri Güvenliği Önlemleri</h2>

<p>Kaya Casino, kişisel verilerinizin güvenliğini sağlamak için sektörün en ileri teknolojilerini kullanmaktadır. 256-bit SSL şifreleme ile tüm veri transferleri korunmakta, sunucularımız yüksek güvenlikli veri merkezlerinde barındırılmaktadır. Erişim kontrolleri, düzenli güvenlik denetimleri ve sızma testleri ile veri güvenliği sürekli olarak izlenmektedir.</p>

<h2>Kullanıcı Hakları</h2>

<p>Kaya Casino kullanıcıları, KVKK ve GDPR kapsamında aşağıdaki haklara sahiptir:</p>

<ol>
<li><strong>Bilgi Edinme Hakkı:</strong> Hangi kişisel verilerinizin toplandığını ve nasıl işlendiğini öğrenme hakkınız bulunmaktadır.</li>
<li><strong>Düzeltme Hakkı:</strong> Yanlış veya eksik kişisel verilerinizin düzeltilmesini talep edebilirsiniz.</li>
<li><strong>Silme Hakkı:</strong> Yasal saklama yükümlülükleri saklı kalmak kaydıyla kişisel verilerinizin silinmesini isteyebilirsiniz.</li>
<li><strong>İtiraz Hakkı:</strong> Verilerinizin belirli amaçlarla işlenmesine itiraz edebilirsiniz.</li>
<li><strong>Taşınabilirlik Hakkı:</strong> Kişisel verilerinizin yapılandırılmış bir formatta size veya başka bir platforma aktarılmasını talep edebilirsiniz.</li>
</ol>

<p>Veri haklarınızla ilgili talepleriniz için <a href="/iletisim">iletişim</a> sayfamızdan müşteri destek ekibimize ulaşabilirsiniz. Platformumuzun genel kullanım koşulları hakkında bilgi almak için <a href="/sorumluluk-reddi">sorumluluk reddi</a> sayfamızı, <a href="/kaya-casino-giris">Kaya Casino giriş</a> işlemleri hakkında rehber için ilgili sayfamızı ziyaret edebilirsiniz.</p>

<h2>Politika Güncellemeleri</h2>

<p>Kaya Casino, bu gizlilik politikasını yasal gereklilikler, teknolojik gelişmeler veya hizmet değişiklikleri doğrultusunda güncelleme hakkını saklı tutmaktadır. Önemli değişiklikler yapıldığında kullanıcılarımız e-posta veya platform içi bildirim yoluyla bilgilendirilecektir. Güncel politikayı düzenli olarak kontrol etmenizi tavsiye ederiz.</p>
HTML,
    ],

    // ========================================
    // 5. SORUMLULUK REDDİ (Disclaimer)
    // ========================================
    [
        'slug' => 'sorumluluk-reddi',
        'title' => 'Sorumluluk Reddi',
        'meta_title' => 'Kaya Casino Sorumluluk Reddi',
        'meta_description' => 'Kaya Casino sorumluluk reddi beyanı. Platformun kullanım koşulları, yasal uyarılar ve sorumluluk sınırları hakkında bilgilendirme.',
        'sort_order' => 5,
        'content' => <<<'HTML'
<h1>Sorumluluk Reddi</h1>

<p>Bu sayfa, Kaya Casino platformunun kullanımına ilişkin yasal uyarıları, sorumluluk sınırlarını ve kullanıcı yükümlülüklerini içermektedir. Platformumuza erişerek ve hizmetlerimizi kullanarak aşağıdaki şartları kabul etmiş sayılırsınız. Lütfen bu beyanı dikkatlice okuyunuz.</p>

<h2>Genel Sorumluluk Beyanı</h2>

<p>Kaya Casino, kullanıcılarına eğlence amaçlı dijital casino hizmetleri sunmaktadır. Platform üzerindeki tüm oyunlar ve bahis işlemleri, kullanıcının kendi iradesi ve sorumluluğu dahilinde gerçekleştirilmektedir. Kaya Casino, kullanıcıların oyun sonuçlarından kaynaklanan finansal kayıplardan sorumlu tutulamaz.</p>

<div class="info-box"><strong>Bilgi:</strong> Online casino oyunları finansal risk içermektedir. Yalnızca kaybetmeyi göze aldığınız miktarlarla oynayın. Oyun bağımlılığı belirtileri fark ederseniz derhal profesyonel destek alın ve hesap kısıtlama araçlarımızdan yararlanın.</div>

<h2>Kullanıcı Yükümlülükleri</h2>

<p>Kaya Casino hizmetlerini kullanan her kullanıcı, aşağıdaki yükümlülükleri kabul etmiş sayılmaktadır:</p>

<ol>
<li><strong>Yaş Sınırı:</strong> Platformu kullanmak için 18 yaşını doldurmuş olmanız gerekmektedir. Kaya Casino, reşit olmayan bireylerin platforma erişimini engellemek için gerekli önlemleri almaktadır.</li>
<li><strong>Doğru Bilgi:</strong> Kayıt sırasında verilen tüm bilgilerin doğru ve güncel olması kullanıcının sorumluluğundadır.</li>
<li><strong>Hesap Güvenliği:</strong> Kullanıcı adı ve şifrenizin gizliliğini korumak sizin sorumluluğunuzdadır. Hesabınız üzerinden gerçekleştirilen tüm işlemlerden siz sorumlusunuz.</li>
<li><strong>Yasal Uyumluluk:</strong> Bulunduğunuz ülkenin online oyun ile ilgili yasalarına uygun hareket etmek sizin sorumluluğunuzdadır.</li>
<li><strong>Sorumlu Oyun:</strong> Oyun aktivitelerinizi kontrol altında tutmak ve bütçenizi aşmamak sizin sorumluluğunuzdadır.</li>
</ol>

<h2>Hizmet Sınırlamaları</h2>

<p>Kaya Casino, platformun kesintisiz ve hatasız çalışacağını garanti etmemektedir. Teknik bakım, altyapı güncellemeleri veya öngörülemeyen teknik sorunlar nedeniyle hizmette geçici kesintiler yaşanabilir. Platform, bu tür kesintilerden kaynaklanan doğrudan veya dolaylı zararlardan sorumlu tutulamaz.</p>

<table>
<thead>
<tr><th>Konu</th><th>Sorumluluk Durumu</th></tr>
</thead>
<tbody>
<tr><td>Oyun Sonuçları ve Finansal Kayıplar</td><td>Kullanıcı sorumluluğunda</td></tr>
<tr><td>Hesap Güvenliği ve Şifre Koruması</td><td>Kullanıcı sorumluluğunda</td></tr>
<tr><td>Teknik Altyapı ve Veri Güvenliği</td><td>Kaya Casino sorumluluğunda</td></tr>
<tr><td>Yasal Düzenlemelere Uyum</td><td>Ortak sorumluluk</td></tr>
<tr><td>Üçüncü Taraf Bağlantıları</td><td>İlgili üçüncü taraf sorumluluğunda</td></tr>
</tbody>
</table>

<h2>Fikri Mülkiyet Hakları</h2>

<p>Kaya Casino platformundaki tüm içerikler, tasarımlar, logolar, yazılımlar ve diğer fikri mülkiyet unsurları, Kaya Casino ve/veya lisans sağlayıcılarının mülkiyetindedir. Bu içeriklerin izinsiz kopyalanması, dağıtılması veya ticari amaçla kullanılması kesinlikle yasaktır ve yasal işlem başlatılmasına neden olabilir.</p>

<h2>Sorumlu Oyun Politikası</h2>

<p>Kaya Casino, sorumlu oyun ilkelerini benimsemekte ve kullanıcılarını bu konuda bilinçlendirmeye büyük önem vermektedir. Platformumuzda oyun bağımlılığını önlemeye yönelik çeşitli araçlar sunulmaktadır:</p>

<ol>
<li><strong>Yatırım Limiti:</strong> Günlük, haftalık veya aylık yatırım limiti belirleyebilirsiniz.</li>
<li><strong>Kayıp Limiti:</strong> Maksimum kayıp tutarınızı önceden belirleyerek bütçenizi kontrol altında tutabilirsiniz.</li>
<li><strong>Oturum Süresi:</strong> Oyun oturumlarınız için süre kısıtlaması ayarlayabilirsiniz.</li>
<li><strong>Kendi Kendini Hariç Tutma:</strong> Belirli bir süre veya kalıcı olarak hesabınızı askıya alabilirsiniz.</li>
</ol>

<h2>Değişiklik Hakkı</h2>

<p>Kaya Casino, bu sorumluluk reddi beyanını önceden bildirimde bulunmaksızın güncelleme hakkını saklı tutmaktadır. Kullanıcıların bu sayfayı düzenli olarak kontrol etmeleri tavsiye edilir. Platformu kullanmaya devam etmeniz, güncellenmiş şartları kabul ettiğiniz anlamına gelmektedir.</p>

<p>Herhangi bir sorunuz olması durumunda <a href="/iletisim">iletişim</a> sayfamızdan müşteri destek ekibimize ulaşabilirsiniz. Kişisel verilerinizin korunması hakkında bilgi almak için <a href="/gizlilik-politikasi">gizlilik politikası</a> sayfamızı inceleyebilirsiniz. Platformumuz hakkında genel bilgi edinmek için <a href="/hakkimizda">hakkımızda</a> sayfamızı ziyaret edebilirsiniz.</p>
HTML,
    ],

];

// ─── INSERT PAGES ───

echo "Seeding pages for kayacasino.top...\n\n";

foreach ($pages as $pageData) {
    $existing = Page::on('tenant')->where('slug', $pageData['slug'])->first();

    if ($existing) {
        echo "  ⊘ Page '{$pageData['slug']}' already exists, skipping\n";
        continue;
    }

    Page::on('tenant')->create(array_merge($pageData, ['is_published' => true]));
    echo "  ✓ Created page: {$pageData['slug']}\n";
}

echo "\nDone! All pages processed for kayacasino.top.\n";
