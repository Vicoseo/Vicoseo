<?php

use App\Models\Site;
use App\Models\Page;
use App\Services\TenantManager;

$site = Site::where('domain', 'risebets.click')->first();
if (!$site) { echo "Site bulunamadı!\n"; return; }

app(TenantManager::class)->setTenant($site);

$pages = [
    [
        'slug' => 'hakkimizda',
        'title' => 'RiseBet Hakkımızda',
        'meta_title' => 'RiseBet Hakkımızda | Güvenilir Bahis ve Casino Platformu',
        'meta_description' => 'RiseBet hakkında bilgi edinin. Vizyonumuz, misyonumuz, lisans bilgileri ve güvenlik altyapımız.',
        'sort_order' => 2,
        'content' => '<div class="page-content">
<h1>RiseBet Hakkımızda</h1>

<p><strong>RiseBet</strong>, online bahis ve casino sektöründe güvenilir, yenilikçi ve kullanıcı odaklı bir platform olarak hizmet vermektedir. Geniş spor bahis marketleri, zengin casino oyun koleksiyonu ve üstün müşteri hizmetleri ile sektörde öne çıkmaktadır.</p>

<h2>Vizyonumuz</h2>
<p>Türkiye ve çevre ülkelerdeki bahis ve casino tutkunlarına en kaliteli, en güvenli ve en eğlenceli dijital deneyimi sunmak. Teknolojiye yatırım yaparak kullanıcılarımıza her zaman bir adım ötede bir platform sunmayı hedefliyoruz.</p>

<h2>Misyonumuz</h2>
<ul>
<li>Kullanıcı güvenliğini her şeyin önünde tutmak</li>
<li>Adil ve şeffaf bir oyun ortamı sağlamak</li>
<li>Kesintisiz ve hızlı bir platform deneyimi sunmak</li>
<li>Sorumlu oyun ilkelerine bağlı kalmak</li>
<li>7/24 profesyonel müşteri desteği sağlamak</li>
</ul>

<h2>Lisans ve Güvenlik</h2>
<p>RiseBet, uluslararası oyun lisansı altında faaliyet göstermektedir. Platformumuz 256-bit SSL şifreleme teknolojisi ile korunmakta, tüm finansal işlemler PCI DSS standartlarına uygun olarak gerçekleştirilmektedir.</p>

<h2>Oyun ve Bahis Altyapısı</h2>
<p>Dünyaca ünlü oyun sağlayıcılarıyla iş birliği yaparak binlerce slot oyunu, canlı casino masası ve geniş spor bahis marketleri sunmaktayız. Pragmatic Play, Evolution, NetEnt, Betsoft ve daha birçok lider sağlayıcının oyunları platformumuzda yer almaktadır.</p>

<h2>Müşteri Memnuniyeti</h2>
<p>Kullanıcılarımızın memnuniyeti en büyük önceliğimizdir. 7/24 canlı destek, hızlı ödeme işlemleri ve düzenli bonus kampanyaları ile kullanıcı deneyimini sürekli geliştirmekteyiz.</p>

<h2>Sorumlu Oyun</h2>
<p>RiseBet olarak sorumlu oyun ilkelerine sıkı sıkıya bağlıyız. Kullanıcılarımıza yatırım limiti belirleme, hesap dondurma ve kendi kendini hariç tutma araçları sunmaktayız. Bahis ve casino oyunları eğlence amaçlıdır — bir gelir kaynağı olarak görülmemelidir.</p>

<h2>İletişim</h2>
<p>Sorularınız ve önerileriniz için 7/24 canlı destek hattımızdan veya e-posta yoluyla bizimle iletişime geçebilirsiniz.</p>
</div>'
    ],
    [
        'slug' => 'gizlilik-politikasi',
        'title' => 'Gizlilik Politikası',
        'meta_title' => 'RiseBet Gizlilik Politikası | Kişisel Veri Koruma',
        'meta_description' => 'RiseBet gizlilik politikası. Kişisel verilerinizin nasıl toplandığı, saklandığı ve korunduğu hakkında bilgi.',
        'sort_order' => 4,
        'content' => '<div class="page-content">
<h1>Gizlilik Politikası</h1>

<p>Bu gizlilik politikası, <strong>RiseBet</strong> platformunda kişisel verilerinizin nasıl toplandığını, kullanıldığını, saklandığını ve korunduğunu açıklamaktadır. Platformumuzu kullanarak bu politikayı kabul etmiş sayılırsınız.</p>

<h2>Toplanan Veriler</h2>
<p>Hizmetlerimizi sunabilmek için aşağıdaki kişisel verileri topluyoruz:</p>
<ul>
<li><strong>Kimlik bilgileri:</strong> Ad, soyad, doğum tarihi</li>
<li><strong>İletişim bilgileri:</strong> E-posta adresi, telefon numarası</li>
<li><strong>Hesap bilgileri:</strong> Kullanıcı adı, şifre (şifreli olarak saklanır)</li>
<li><strong>Finansal bilgiler:</strong> Ödeme yöntemi detayları, işlem geçmişi</li>
<li><strong>Teknik veriler:</strong> IP adresi, tarayıcı bilgisi, cihaz türü, çerez verileri</li>
<li><strong>Kullanım verileri:</strong> Oyun tercihleri, bahis geçmişi, site etkileşim verileri</li>
</ul>

<h2>Verilerin Kullanım Amaçları</h2>
<ul>
<li>Hesap oluşturma ve yönetimi</li>
<li>Finansal işlemlerin gerçekleştirilmesi</li>
<li>Kimlik doğrulama ve güvenlik kontrolü</li>
<li>Müşteri desteği sağlanması</li>
<li>Yasal yükümlülüklerin yerine getirilmesi</li>
<li>Hizmet kalitesinin iyileştirilmesi</li>
<li>Promosyon ve kampanya bildirimlerinin gönderilmesi (onay dahilinde)</li>
</ul>

<h2>Veri Güvenliği</h2>
<p>Kişisel verileriniz 256-bit SSL şifreleme ile korunmaktadır. Verilerinize yalnızca yetkili personel erişebilir. Düzenli güvenlik denetimleri yapılmakta ve veriler güvenli sunucularda saklanmaktadır.</p>

<h2>Çerez Politikası</h2>
<p>Platformumuz, kullanıcı deneyimini iyileştirmek için çerezler kullanmaktadır. Çerezler, oturum yönetimi, tercih hatırlama ve analitik amaçlarla kullanılır. Tarayıcı ayarlarınızdan çerez tercihlerinizi yönetebilirsiniz.</p>

<h2>Üçüncü Taraf Paylaşımı</h2>
<p>Kişisel verileriniz, yasal zorunluluklar dışında üçüncü taraflarla paylaşılmaz. Ödeme işlemleri için güvenilir ödeme sağlayıcılarıyla veri paylaşımı yapılabilir ve bu sağlayıcılar da aynı gizlilik standartlarına tabidir.</p>

<h2>Kullanıcı Hakları</h2>
<p>Kullanıcılarımız aşağıdaki haklara sahiptir:</p>
<ul>
<li>Kişisel verilerine erişim talep etme</li>
<li>Verilerin düzeltilmesini veya silinmesini isteme</li>
<li>Veri işlemeye itiraz etme</li>
<li>Pazarlama iletişimlerinden çıkma</li>
<li>Hesabın kalıcı olarak kapatılmasını talep etme</li>
</ul>

<h2>Politika Güncellemeleri</h2>
<p>Bu gizlilik politikası düzenli olarak güncellenebilir. Önemli değişiklikler e-posta yoluyla bildirilecektir. Son güncelleme tarihi sayfanın altında belirtilmektedir.</p>

<h2>İletişim</h2>
<p>Gizlilik politikamız hakkında sorularınız için 7/24 canlı destek hattımızdan bizimle iletişime geçebilirsiniz.</p>

<p><em>Son güncelleme: Mart 2026</em></p>
</div>'
    ],
    [
        'slug' => 'sartlar-ve-kosullar',
        'title' => 'Şartlar ve Koşullar',
        'meta_title' => 'RiseBet Şartlar ve Koşullar | Kullanım Koşulları',
        'meta_description' => 'RiseBet şartlar ve koşullar. Platform kullanım kuralları, hesap yönetimi, bonus politikası ve yasal bilgiler.',
        'sort_order' => 4,
        'content' => '<div class="page-content">
<h1>Şartlar ve Koşullar</h1>

<p>Bu şartlar ve koşullar, <strong>RiseBet</strong> platformunun kullanımını düzenlemektedir. Platformumuza kayıt olarak ve hizmetlerimizi kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız.</p>

<h2>1. Genel Kurallar</h2>
<ul>
<li>Platformu kullanmak için 18 yaşından büyük olmanız gerekmektedir.</li>
<li>Her kullanıcı yalnızca bir hesap açabilir.</li>
<li>Kayıt sırasında verilen bilgilerin doğru ve güncel olması zorunludur.</li>
<li>Hesabınızın güvenliği sizin sorumluluğunuzdadır.</li>
</ul>

<h2>2. Hesap Yönetimi</h2>
<ul>
<li>Hesap bilgileri üçüncü kişilerle paylaşılamaz.</li>
<li>Şüpheli aktivite tespit edilen hesaplar incelemeye alınabilir.</li>
<li>Birden fazla hesap açma girişimi tüm hesapların kapatılmasına neden olabilir.</li>
<li>Platform, gerekli gördüğünde kimlik doğrulaması (KYC) talep edebilir.</li>
</ul>

<h2>3. Para Yatırma ve Çekme</h2>
<ul>
<li>Yatırım ve çekim işlemleri belirtilen minimum ve maksimum limitler dahilinde yapılır.</li>
<li>Çekim talepleri güvenlik kontrolleri sonrası işleme alınır.</li>
<li>İlk çekim öncesinde hesap doğrulaması tamamlanmalıdır.</li>
<li>Yatırım yapılan yöntemle çekim yapılması tercih edilir.</li>
<li>Platform, şüpheli işlemlerde ek doğrulama talep edebilir.</li>
</ul>

<h2>4. Bonus ve Promosyonlar</h2>
<ul>
<li>Her bonus kampanyasının kendine özgü çevrim şartları ve koşulları vardır.</li>
<li>Aynı anda birden fazla bonus kullanılamaz.</li>
<li>Bonus suistimali tespit edilmesi halinde bonus ve kazançlar iptal edilebilir.</li>
<li>Platform, bonus kampanyalarını önceden haber vermeksizin değiştirme veya sonlandırma hakkını saklı tutar.</li>
</ul>

<h2>5. Bahis ve Oyun Kuralları</h2>
<ul>
<li>Tüm bahisler ve oyunlar platform tarafından belirlenen kurallara tabidir.</li>
<li>Teknik arıza durumunda bahisler iptal edilebilir veya geçersiz sayılabilir.</li>
<li>Platform, hatalı oranları düzeltme hakkını saklı tutar.</li>
<li>Casino oyunları RNG (Rastgele Sayı Üreteci) teknolojisiyle çalışır ve bağımsız kuruluşlar tarafından denetlenir.</li>
</ul>

<h2>6. Yasaklanan Davranışlar</h2>
<ul>
<li>Hile, dolandırıcılık veya kara para aklama girişimleri</li>
<li>Yazılım veya bot kullanarak otomatik bahis yapma</li>
<li>Başka kişilerin hesap bilgilerini kullanma</li>
<li>Platform altyapısına zarar verme girişimleri</li>
<li>Bonus ve promosyonların suistimali</li>
</ul>

<h2>7. Hesap Kapatma</h2>
<p>Kullanıcılar istedikleri zaman hesaplarını kapatma talebinde bulunabilir. Hesap kapatılmadan önce mevcut bakiye belirtilen yöntemle kullanıcıya aktarılır. Platform, kuralları ihlal eden hesapları tek taraflı olarak kapatma hakkına sahiptir.</p>

<h2>8. Sorumluluk Reddi</h2>
<p>RiseBet, teknik arızalar, internet bağlantı sorunları veya üçüncü taraf hizmet kesintilerinden kaynaklanan zararlardan sorumlu tutulamaz. Bahis ve casino oyunları risk içerir — kaybetmeyi göze alabileceğiniz miktarlarla oynayınız.</p>

<h2>9. Değişiklikler</h2>
<p>Bu şartlar ve koşullar önceden haber vermeksizin güncellenebilir. Güncellemeler yayınlandığı anda yürürlüğe girer. Kullanıcıların düzenli olarak bu sayfayı kontrol etmeleri önerilir.</p>

<p><em>Son güncelleme: Mart 2026</em></p>
</div>'
    ],
    [
        'slug' => 'risebet-bonus',
        'title' => 'RiseBet Bonus ve Kampanyalar: 2026 Güncel Rehber',
        'meta_title' => 'RiseBet Bonus Kampanyaları 2026 | Hoşgeldin & Yatırım Bonusu',
        'meta_description' => 'RiseBet bonus kampanyaları 2026. Hoşgeldin bonusu, deneme bonusu, yatırım bonusu, kayıp bonusu ve çevrim şartları rehberi.',
        'sort_order' => 13,
        'content' => '<div class="page-content">
<h1>RiseBet Bonus ve Kampanyalar: 2026 Güncel Rehber</h1>

<p><strong>RiseBet</strong>, kullanıcılarına çeşitli bonus kampanyaları sunarak bahis ve casino deneyimini zenginleştirmektedir. 2026 yılında aktif olan tüm kampanyaları ve çevrim şartlarını bu sayfada bulabilirsiniz.</p>

<h2>Aktif Bonus Kampanyaları</h2>

<table>
<thead><tr><th>Bonus Türü</th><th>Miktar</th><th>Çevrim</th><th>Geçerlilik</th></tr></thead>
<tbody>
<tr><td>Hoşgeldin Bonusu</td><td>%100 — 5.000 TL</td><td>10x</td><td>14 gün</td></tr>
<tr><td>Deneme Bonusu</td><td>250 TL</td><td>15x</td><td>7 gün</td></tr>
<tr><td>Yatırım Bonusu</td><td>%30 — 2.000 TL</td><td>8x</td><td>7 gün</td></tr>
<tr><td>Kayıp Bonusu</td><td>%15 — 3.000 TL</td><td>3x</td><td>3 gün</td></tr>
<tr><td>Arkadaş Davet</td><td>200 TL</td><td>5x</td><td>30 gün</td></tr>
<tr><td>Kripto Bonus</td><td>%50 — 4.000 TL</td><td>6x</td><td>10 gün</td></tr>
</tbody>
</table>

<h2>Hoşgeldin Bonusu</h2>
<p>RiseBet\'e yeni katılan kullanıcılara ilk yatırımlarının %100\'ü kadar bonus verilir. Minimum 100 TL yatırım yaparak maksimum 5.000 TL bonus alabilirsiniz.</p>
<ul>
<li>Çevrim şartı: 10x (bonus miktarı üzerinden)</li>
<li>Minimum oran: 1.50 (spor bahisleri)</li>
<li>Geçerlilik süresi: 14 gün</li>
<li>Slot oyunları %100 çevrime katkı sağlar</li>
</ul>

<h2>Deneme Bonusu</h2>
<p>Yatırımsız 250 TL deneme bonusu ile platformu risksiz keşfedebilirsiniz.</p>
<ul>
<li>Sadece yeni üyelere tanımlanır</li>
<li>15x çevrim şartı</li>
<li>Maksimum 500 TL çekilebilir</li>
<li>Yalnızca slot oyunlarında geçerli</li>
</ul>

<h2>Yatırım Bonusu</h2>
<p>Her yatırımınızda %30 bonus kazanın. Haftalık yatırım kampanyaları düzenli olarak güncellenir.</p>

<h2>Kayıp Bonusu</h2>
<p>Haftalık net kayıbınızın %15\'i bonus olarak geri döner. Sadece 3x çevrim şartı ile en kullanıcı dostu bonus türüdür.</p>

<h2>Çevrim Şartları Nasıl Çalışır?</h2>
<p>Çevrim şartı, bonusu nakde çevirebilmek için yapmanız gereken toplam bahis miktarını ifade eder. Örneğin 1.000 TL bonus ve 10x çevrim = 10.000 TL bahis yapmanız gerekir.</p>
<ul>
<li><strong>Spor bahisleri:</strong> Minimum 1.50 oran, tekli bahisler</li>
<li><strong>Slot oyunları:</strong> %100 çevrim katkısı</li>
<li><strong>Masa oyunları:</strong> %10-20 çevrim katkısı</li>
<li><strong>Canlı casino:</strong> %15-25 çevrim katkısı</li>
</ul>

<h2>Önemli Kurallar</h2>
<ul>
<li>Aynı anda iki aktif bonus kullanılamaz</li>
<li>Çevrim tamamlanmadan çekim yapılırsa bonus silinir</li>
<li>Bonus süreleri dolmadan çevrimi tamamlayın</li>
<li>Bonus istemiyorsanız "bonussuz yatırım" seçeneğini kullanın</li>
</ul>

<p><em>Kampanya koşulları önceden bildirilmeksizin değiştirilebilir. Güncel bilgiler için bu sayfayı düzenli kontrol edin.</em></p>
</div>'
    ],
    [
        'slug' => 'risebet-mobil-giris',
        'title' => 'RiseBet Mobil Giriş Rehberi: Hızlı ve Güvenli 2026',
        'meta_title' => 'RiseBet Mobil Giriş 2026 | Telefondan Erişim Rehberi',
        'meta_description' => 'RiseBet mobil giriş rehberi 2026. Telefondan erişim, PWA kurulumu, mobil bahis ve casino deneyimi.',
        'sort_order' => 14,
        'content' => '<div class="page-content">
<h1>RiseBet Mobil Giriş Rehberi: Hızlı ve Güvenli 2026</h1>

<p><strong>RiseBet</strong>, mobil optimize altyapısıyla akıllı telefon ve tabletlerden masaüstü deneyiminin tüm özelliklerini sunar. Bu rehberde mobil giriş adımlarını ve performans ipuçlarını bulabilirsiniz.</p>

<h2>Mobil Giriş Adımları</h2>
<ol>
<li>Mobil tarayıcınızı açın (Chrome, Safari, Firefox)</li>
<li>RiseBet güncel adresini adres çubuğuna yazın</li>
<li>"Giriş Yap" butonuna dokunun</li>
<li>Kullanıcı adı ve şifrenizi girin</li>
<li>Güvenlik doğrulamasını tamamlayın</li>
</ol>

<h2>Ana Ekrana Ekleme (PWA)</h2>
<h3>Android (Chrome)</h3>
<ol>
<li>RiseBet sitesini Chrome\'da açın</li>
<li>Üç nokta menüsüne dokunun</li>
<li>"Ana ekrana ekle" seçeneğini seçin</li>
<li>"Ekle"ye dokunun</li>
</ol>

<h3>iOS (Safari)</h3>
<ol>
<li>RiseBet sitesini Safari\'de açın</li>
<li>Paylaş butonuna dokunun</li>
<li>"Ana Ekrana Ekle" seçin</li>
<li>"Ekle"ye dokunun</li>
</ol>

<h2>Mobil Özellikler</h2>
<table>
<thead><tr><th>Özellik</th><th>Detay</th></tr></thead>
<tbody>
<tr><td>Responsive Tasarım</td><td>Tüm ekran boyutlarına otomatik uyum</td></tr>
<tr><td>PWA Desteği</td><td>Uygulama benzeri deneyim</td></tr>
<tr><td>Canlı Bahis</td><td>Anlık oran güncellemesi ve tek dokunuş bahis</td></tr>
<tr><td>Casino</td><td>Tüm slot ve masa oyunları HTML5 ile mobilde çalışır</td></tr>
<tr><td>Canlı Casino</td><td>HD kalitede canlı yayın</td></tr>
<tr><td>Para İşlemleri</td><td>Mobilde yatırma ve çekme tam destekli</td></tr>
</tbody>
</table>

<h2>Performans İpuçları</h2>
<ul>
<li>Wi-Fi tercih edin — daha stabil bağlantı sağlar</li>
<li>Güç tasarrufu modunu kapatın</li>
<li>Tarayıcı önbelleğini düzenli temizleyin</li>
<li>Arka plan uygulamalarını kapatarak RAM serbest bırakın</li>
<li>Ekran zaman aşımını uzatın</li>
</ul>

<h2>Mobil Güvenlik</h2>
<ul>
<li>Parmak izi veya Face ID aktifleştirin</li>
<li>Paylaşılan cihazlarda "beni hatırla" kullanmayın</li>
<li>Halka açık Wi-Fi\'da bahis işlemi yapmayın</li>
<li>İki faktörlü doğrulamayı aktifleştirin</li>
<li>Sadece resmi adresten giriş yapın</li>
</ul>

<h2>Sıkça Sorulan Sorular</h2>
<div class="faq">
<h3>RiseBet mobil uygulaması var mı?</h3>
<p>RiseBet mobil web ve PWA formatıyla hizmet vermektedir. Ayrı uygulama indirmenize gerek yoktur.</p>

<h3>Mobilde para yatırma güvenli mi?</h3>
<p>Evet, 256-bit SSL şifreleme ile tüm işlemler korunur.</p>

<h3>Hangi tarayıcı en iyi çalışır?</h3>
<p>Android\'de Chrome, iOS\'te Safari en optimize deneyimi sunar.</p>
</div>
</div>'
    ],
];

$created = 0;
foreach ($pages as $page) {
    $existing = Page::where('slug', $page['slug'])->first();
    if ($existing) {
        echo "ZATEN VAR: " . $page['slug'] . "\n";
        continue;
    }
    Page::create([
        'slug' => $page['slug'],
        'title' => $page['title'],
        'content' => $page['content'],
        'meta_title' => $page['meta_title'],
        'meta_description' => $page['meta_description'],
        'sort_order' => $page['sort_order'],
        'is_published' => true,
    ]);
    $created++;
    echo "OK: " . $page['slug'] . "\n";
}
echo "\n=== risebets.click Eksik Sayfalar ===\n";
echo "Oluşturulan: $created\n";
