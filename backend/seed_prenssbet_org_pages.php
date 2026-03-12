<?php
/**
 * Prenssbet.org Pages Seed (homepage + 15 pages)
 * Required by seed_prenssbet_org.php
 */

$imgBase = '/storage/uploads/promotions/prensbet-org';
$now = now();

// Delete existing pages except yasal-bilgilendirme
DB::connection('tenant')->table('pages')->where('slug', '!=', 'yasal-bilgilendirme')->delete();

// ─── HOMEPAGE ───
$homepageContent = <<<'HTML'
<h1>Prensbet 2026 – Güvenilir Bahis ve Casino Platformu</h1>
<p>Prensbet, Türkiye'nin en güvenilir ve kapsamlı online bahis platformlarından biri olarak 2026 yılında da kullanıcılarına üstün hizmet sunmaya devam etmektedir. Geniş spor bahisleri yelpazesi, zengin casino oyun koleksiyonu, cazip bonus kampanyaları ve güvenli ödeme altyapısıyla öne çıkan Prensbet, her seviyedeki bahis severin ihtiyaçlarına cevap vermektedir. Curaçao lisansı altında faaliyet gösteren platform, uluslararası güvenlik standartlarına uygun şekilde hizmet sunmaktadır.</p>

<h2>Prensbet Spor Bahisleri – Geniş Branş Yelpazesi ve Yüksek Oranlar</h2>
<p>Prensbet spor bahisleri bölümü, dünya genelindeki binlerce spor etkinliğine bahis yapma imkânı sunmaktadır. Futbol, basketbol, tenis, voleybol, hentbol, buz hokeyi, beyzbol ve daha birçok branşta maç öncesi ve canlı bahis seçenekleri mevcuttur. Yüksek oran politikası sayesinde kazanç potansiyelinizi maksimize edebilirsiniz.</p>

<h3>Canlı Bahis Deneyimi</h3>
<p>Prensbet canlı bahis bölümü, maçları anlık olarak takip ederken bahis yapma imkânı sunar. Gelişmiş istatistik paneli, canlı skor takibi ve hızlı bahis kuponu oluşturma özellikleriyle profesyonel bir canlı bahis deneyimi yaşarsınız. Futbol, basketbol, tenis ve voleybol başta olmak üzere onlarca spor dalında canlı bahis seçenekleri mevcuttur.</p>
<ul>
<li><strong>Anlık Oran Güncellemeleri:</strong> Maç akışına göre oranlar saniyeler içinde güncellenir</li>
<li><strong>Canlı İstatistikler:</strong> Top kontrolü, şut istatistikleri, korner sayıları ve daha fazlası</li>
<li><strong>Hızlı Kupon:</strong> Tek tıkla bahis yapma özelliği ile fırsatları kaçırmayın</li>
<li><strong>Cash Out:</strong> Maç bitmeden kuponunuzu nakde çevirme imkânı</li>
</ul>

<h3>Öne Çıkan Spor Bahis Avantajları</h3>
<ul>
<li>30'dan fazla spor dalı ve espor seçeneği</li>
<li>Anlık maç takibi, istatistik tabloları ve canlı skor entegrasyonu</li>
<li>Yüksek oranlar sayesinde kazanç potansiyelini artırmak kolay</li>
<li>Kombine kuponlarda ekstra bonus fırsatları</li>
<li>Özel maçlarda boost edilmiş oran kampanyaları</li>
</ul>

<h2>Prensbet Casino – Slot, Masa Oyunları ve Canlı Casino</h2>
<p>Prensbet casino bölümü, dünyanın önde gelen oyun sağlayıcılarının binlerce oyununu tek çatı altında sunmaktadır. Pragmatic Play, Evolution Gaming, NetEnt, Microgaming, Play'n GO ve daha birçok premium sağlayıcının oyunlarına erişebilirsiniz.</p>

<h3>Slot Oyunları</h3>
<p>Prensbet'te Gates of Olympus, Sweet Bonanza, The Dog House Megaways, Big Bass Bonanza, Starlight Princess ve yüzlerce popüler slot oyunu bulunmaktadır. Klasik 3 makaralı slotlardan modern megaways mekanikli oyunlara, jackpot slotlarından bonus buy özellikli oyunlara kadar geniş bir koleksiyon mevcuttur. Yüksek RTP oranlarına sahip oyunlar ile kazanç şansınızı artırabilirsiniz.</p>

<h3>Canlı Casino</h3>
<p>Gerçek krupiyelerle oynanan canlı casino oyunları, Prensbet'in en popüler bölümlerinden biridir. Evolution Gaming ve Pragmatic Play Live altyapısıyla sunulan canlı rulet, canlı blackjack, canlı baccarat ve canlı poker masalarında gerçek casino atmosferini evinizden yaşayabilirsiniz. Lightning Roulette, Crazy Time, Mega Ball gibi game show formatındaki oyunlar da mevcuttur.</p>

<h3>Masa Oyunları ve Diğer Kategoriler</h3>
<p>Klasik masa oyunları severler için rulet, blackjack, baccarat ve poker çeşitleri bulunmaktadır. Ayrıca Aviator, Spaceman, JetX gibi crash oyunları, sanal sporlar ve mini oyunlar da Prensbet'te yerini almaktadır.</p>

<h2>Prensbet Bonus Kampanyaları – Kazancınızı Katlayın</h2>
<p>Prensbet, yeni ve mevcut üyelerine çeşitli bonus kampanyaları sunarak kazanç potansiyellerini artırmaktadır. Hoş geldin bonusundan deneme bonusuna, kayıp bonusundan VIP ayrıcalıklarına kadar birçok avantajlı fırsattan yararlanabilirsiniz.</p>

<h3>%100 Hoş Geldin Bonusu</h3>
<img src="/storage/uploads/promotions/prensbet-org/hosgeldin-bonusu.jpg" alt="Prensbet %100 Hoş Geldin Bonusu" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>Prensbet'e ilk kez üye olan kullanıcılar, ilk yatırımlarında %100 hoş geldin bonusu kazanmaktadır. Casino ve spor bahisleri için geçerli olan bu bonus, yeni üyelerin platformu keşfetmeleri için mükemmel bir fırsat sunmaktadır. Minimum yatırım tutarı ve çevrim şartları kampanya sayfasında detaylı olarak belirtilmektedir.</p>

<h3>1000TL Deneme Bonusu</h3>
<img src="/storage/uploads/promotions/prensbet-org/1000tl-deneme-bonusu.jpg" alt="Prensbet 1000TL Deneme Bonusu" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>İlk üyeliğinize özel 1000TL deneme bonusu ile Prensbet'i risksiz keşfedin. Yeni üyelerimize sunulan bu özel fırsat sayesinde platformdaki oyunları ve bahis seçeneklerini deneyimleyebilirsiniz. Deneme bonusu ile elde edilen kazançlar, çevrim şartları tamamlandığında çekilebilir bakiyeye dönüşmektedir.</p>

<h3>%30 Haftalık Kayıp Bonusu</h3>
<img src="/storage/uploads/promotions/prensbet-org/haftalik-kayip-bonusu.jpg" alt="Prensbet %30 Haftalık Kayıp Bonusu" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>Her hafta kayıplarınızın %30'unu geri alarak oyun keyfinizi sürdürün. Prensbet haftalık kayıp bonusu, hem casino hem de spor bahisleri için geçerlidir. Otomatik olarak hesabınıza tanımlanan bu bonus, kayıplarınızı minimize etmenize yardımcı olur.</p>

<h3>Kripto Freespin Kampanyası</h3>
<img src="/storage/uploads/promotions/prensbet-org/kripto-freespin.jpg" alt="Prensbet Kripto Freespin" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>Kripto para ile yatırım yapan kullanıcılara özel freespin hediyesi! Bitcoin, Ethereum, USDT ve diğer kripto paralarla yaptığınız yatırımlarda ekstra freespin kazanın. Kripto yatırımların hızlı işlem süresi ve düşük komisyon avantajlarıyla birleşen bu kampanya, dijital varlık sahipleri için ideal bir fırsattır.</p>

<h3>Kumbara Puan Sistemi</h3>
<img src="/storage/uploads/promotions/prensbet-org/kumbara-puan.jpg" alt="Prensbet Kumbara Puan Sistemi" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>Prensbet kumbara puan sistemi ile her oynadığınızda puan biriktirin, biriken puanlarınızı bonus bakiyeye dönüştürün. Casino oyunları ve spor bahislerinde yaptığınız her işlem size puan kazandırır. Puanlarınız belirli bir eşiğe ulaştığında otomatik olarak bonus hesabınıza aktarılır.</p>

<h3>VIP Club</h3>
<img src="/storage/uploads/promotions/prensbet-org/prensbet-vip.jpg" alt="Prensbet VIP Club" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>Prensbet VIP Club üyeleri özel bonuslar, yüksek çekim limitleri, kişisel hesap yöneticisi ve öncelikli destek hizmetinden yararlanmaktadır. VIP seviyeniz yükseldikçe ayrıcalıklarınız da artar. Doğum günü bonusu, özel turnuva davetleri ve sürpriz hediyeler VIP üyelerimizi beklemektedir.</p>

<h3>Genel Bonus Kuralları</h3>
<img src="/storage/uploads/promotions/prensbet-org/genel-bonus-kurallari.jpg" alt="Prensbet Genel Bonus Kuralları" width="800" loading="lazy" style="max-width:800px;width:100%;border-radius:12px">
<p>Tüm bonus kampanyalarının genel kuralları ve çevrim şartları hakkında detaylı bilgi için bonus kuralları sayfamızı inceleyebilirsiniz. Her kampanyanın kendine özgü koşulları bulunmakta olup, bonus almadan önce ilgili şartları okumanız önerilir.</p>

<h2>Neden Prensbet Tercih Edilmeli?</h2>
<ul>
<li><strong>Lisanslı ve Güvenilir:</strong> Curaçao eGaming lisansı ile yasal çerçevede hizmet</li>
<li><strong>Geniş Oyun Yelpazesi:</strong> 5000+ casino oyunu ve 30+ spor dalı</li>
<li><strong>Yüksek Oranlar:</strong> Sektörün en rekabetçi bahis oranları</li>
<li><strong>Hızlı Ödeme:</strong> Para çekme işlemleri dakikalar içinde tamamlanır</li>
<li><strong>7/24 Destek:</strong> Canlı destek, Telegram ve e-posta ile kesintisiz yardım</li>
<li><strong>Mobil Uyumlu:</strong> Tüm cihazlardan sorunsuz erişim</li>
<li><strong>Cazip Bonuslar:</strong> Düzenli kampanyalar ve VIP ayrıcalıkları</li>
<li><strong>Türkçe Arayüz:</strong> Tamamen Türkçe platform ve destek hizmeti</li>
</ul>

<h2>Güvenlik ve Veri Koruma</h2>
<p>Prensbet, kullanıcı güvenliğini en üst düzeyde tutmaktadır. 256-bit SSL şifreleme teknolojisi ile tüm kişisel ve finansal verileriniz koruma altındadır. İki faktörlü kimlik doğrulama (2FA) seçeneği, hesap güvenliğinizi ekstra bir katmanla güçlendirir. Platform, uluslararası veri koruma standartlarına uygun olarak çalışmakta ve kullanıcı bilgilerini üçüncü taraflarla paylaşmamaktadır.</p>

<h2>Ödeme Yöntemleri – Hızlı ve Güvenli İşlemler</h2>
<p>Prensbet, çeşitli ödeme yöntemleriyle hızlı ve güvenli para yatırma-çekme işlemleri sunmaktadır:</p>
<ul>
<li><strong>Papara:</strong> Anında yatırım, hızlı çekim – en popüler yöntem</li>
<li><strong>Kripto Para:</strong> Bitcoin, Ethereum, USDT, Litecoin – düşük komisyon, hızlı işlem</li>
<li><strong>Banka Havalesi/EFT:</strong> Tüm Türk bankalarından güvenli transfer</li>
<li><strong>Cepbank:</strong> ATM üzerinden kolay yatırım</li>
<li><strong>QR Kod:</strong> Mobil bankacılık ile hızlı ödeme</li>
</ul>
<p>Minimum yatırım tutarı ödeme yöntemine göre değişmekle birlikte, çekim işlemleri genellikle 15-30 dakika içinde tamamlanmaktadır.</p>

<h2>Prensbet Güncel Giriş Adresi 2026</h2>
<p>Prensbet, erişim engellerine karşı düzenli olarak giriş adresini güncellemektedir. Güncel giriş adresi <strong>prenssbet.org</strong> üzerinden platforma kesintisiz erişim sağlayabilirsiniz. Adres değişikliklerinden haberdar olmak için Telegram kanalımızı takip edebilir veya güncel adres bildirimlerini e-posta ile alabilirsiniz. DNS ayarlarınızı Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) olarak değiştirerek erişim sorunlarını çözebilirsiniz.</p>

<div class="faq-section">
<h2>Sıkça Sorulan Sorular</h2>

<div class="faq-item">
<h3>Prensbet güvenilir mi?</h3>
<p>Evet, Prensbet Curaçao eGaming lisansına sahip, güvenilir bir bahis platformudur. SSL şifreleme, 2FA güvenlik ve lisanslı altyapı ile kullanıcı güvenliği ön plandadır.</p>
</div>

<div class="faq-item">
<h3>Prensbet'e nasıl üye olunur?</h3>
<p>Güncel giriş adresine giderek "Kayıt Ol" butonuna tıklayın. Ad, soyad, e-posta ve telefon bilgilerinizi girerek birkaç dakika içinde üyeliğinizi tamamlayabilirsiniz.</p>
</div>

<div class="faq-item">
<h3>Prensbet deneme bonusu var mı?</h3>
<p>Evet, Prensbet yeni üyelerine 1000TL deneme bonusu sunmaktadır. İlk üyeliğinizde canlı destek hattından bonus talebinde bulunabilirsiniz.</p>
</div>

<div class="faq-item">
<h3>Para çekme işlemi ne kadar sürer?</h3>
<p>Prensbet'te para çekme işlemleri ödeme yöntemine bağlı olarak 15 dakika ile 24 saat arasında tamamlanmaktadır. Papara ve kripto ile çekimler genellikle 15-30 dakika içinde gerçekleşir.</p>
</div>

<div class="faq-item">
<h3>Prensbet mobil uyumlu mu?</h3>
<p>Evet, Prensbet tamamen mobil uyumlu responsive tasarıma sahiptir. Android ve iOS cihazlardan tarayıcı üzerinden sorunsuz erişim sağlayabilirsiniz.</p>
</div>

<div class="faq-item">
<h3>Hangi spor dallarına bahis yapılabilir?</h3>
<p>Futbol, basketbol, tenis, voleybol, hentbol, buz hokeyi, beyzbol, MMA, boks, espor ve daha 30'dan fazla spor dalında maç öncesi ve canlı bahis yapılabilir.</p>
</div>

<div class="faq-item">
<h3>Prensbet'te hangi casino oyunları var?</h3>
<p>5000'den fazla slot oyunu, canlı casino masaları (rulet, blackjack, baccarat, poker), crash oyunları (Aviator, Spaceman), sanal sporlar ve mini oyunlar mevcuttur.</p>
</div>
</div>
HTML;

DB::connection('tenant')->table('pages')->updateOrInsert(
    ['slug' => 'anasayfa'],
    [
        'title' => 'Prensbet 2026 – Güvenilir Bahis ve Casino Platformu',
        'content' => $homepageContent,
        'meta_title' => 'Prensbet Giriş 2026 | Güncel Adres, Bonus ve Kapsamlı Rehber',
        'meta_description' => 'Prensbet güncel giriş adresi 2026 ✓ 1000TL deneme bonusu, %100 hoş geldin bonusu, canlı casino, spor bahisleri ve slot oyunları.',
        'is_published' => 1, 'sort_order' => 0,
        'created_at' => $now, 'updated_at' => $now,
    ]
);
echo "✓ Homepage created\n";

// ─── ADDITIONAL PAGES ───
$pages = [
    [1, 'hakkimizda', 'Prensbet Hakkımızda', 'Prensbet Hakkımızda – Şirket Bilgisi ve Vizyon', 'Prensbet hakkında detaylı bilgi, şirket vizyonu, misyonu ve lisans bilgileri.', <<<'HTML'
<h1>Prensbet Hakkımızda</h1>
<p>Prensbet, online bahis ve casino sektöründe lider konumda olan, Curaçao eGaming lisansı altında faaliyet gösteren güvenilir bir platformdur. Kullanıcı memnuniyetini ön planda tutan Prensbet, yılların deneyimini teknolojik altyapısıyla birleştirerek üstün hizmet sunmaktadır.</p>

<h2>Vizyonumuz</h2>
<p>Türkiye ve dünya genelinde online bahis ve casino alanında en güvenilir, en yenilikçi ve en kullanıcı dostu platform olmayı hedefliyoruz. Teknolojik gelişmeleri yakından takip ederek kullanıcılarımıza her zaman en iyi deneyimi sunmayı amaçlıyoruz.</p>

<h2>Misyonumuz</h2>
<p>Kullanıcılarımıza güvenli, adil ve eğlenceli bir bahis deneyimi sunmak temel misyonumuzdur. Sorumlu oyun ilkelerini benimseyerek, kullanıcılarımızın sağlıklı bir oyun alışkanlığı geliştirmelerine destek oluyoruz.</p>

<h2>Lisans ve Güvenlik</h2>
<p>Prensbet, Curaçao hükümeti tarafından verilen eGaming lisansı ile yasal çerçevede faaliyet göstermektedir. 256-bit SSL şifreleme teknolojisi, iki faktörlü kimlik doğrulama ve düzenli güvenlik denetimleri ile kullanıcı verilerinin korunması sağlanmaktadır.</p>

<h2>Oyun Sağlayıcılarımız</h2>
<p>Pragmatic Play, Evolution Gaming, NetEnt, Microgaming, Play'n GO, Yggdrasil, Red Tiger, Betsoft ve daha birçok lider oyun sağlayıcısı ile iş birliği yaparak 5000'den fazla oyun seçeneği sunuyoruz. Tüm oyunlar bağımsız test kuruluşları tarafından denetlenmektedir.</p>

<h2>Müşteri Hizmetleri</h2>
<p>7/24 aktif canlı destek ekibimiz, Türkçe dil desteğiyle kullanıcılarımızın her türlü sorusuna yanıt vermektedir. Canlı sohbet, e-posta ve Telegram kanalları üzerinden bize ulaşabilirsiniz.</p>
HTML],

    [2, 'iletisim', 'Prensbet İletişim', 'Prensbet İletişim – 7/24 Destek ve İletişim Kanalları', 'Prensbet iletişim bilgileri, canlı destek, Telegram, e-posta ve sosyal medya kanalları.', <<<'HTML'
<h1>Prensbet İletişim</h1>
<p>Prensbet müşteri hizmetleri ekibi, 7 gün 24 saat kesintisiz olarak hizmet vermektedir. Herhangi bir sorunuz, öneriniz veya şikayetiniz için aşağıdaki kanallardan bize ulaşabilirsiniz.</p>

<h2>İletişim Kanalları</h2>
<ul>
<li><strong>Canlı Destek:</strong> Web sitesi üzerinden 7/24 anlık destek – en hızlı iletişim yöntemi</li>
<li><strong>Telegram:</strong> Güncel adres bilgileri ve hızlı destek için Telegram kanalımızı takip edin</li>
<li><strong>E-posta:</strong> Detaylı sorularınız için e-posta ile iletişime geçebilirsiniz</li>
</ul>

<h2>Sıkça Sorulan Konular</h2>
<p>İletişime geçmeden önce SSS sayfamızı incelemenizi öneririz. Birçok sorunun yanıtını SSS bölümünde bulabilirsiniz. Para yatırma/çekme, bonus kullanımı, hesap doğrulama ve teknik sorunlar gibi konularda detaylı rehberlerimiz mevcuttur.</p>

<h2>Geri Bildirim</h2>
<p>Platformumuzu geliştirmek için kullanıcı geri bildirimlerine büyük önem veriyoruz. Önerilerinizi ve deneyimlerinizi bizimle paylaşarak Prensbet'in daha iyi bir platform olmasına katkıda bulunabilirsiniz.</p>
HTML],

    [3, 'gizlilik-politikasi', 'Prensbet Gizlilik Politikası', 'Prensbet Gizlilik Politikası – Veri Koruma ve KVKK', 'Prensbet gizlilik politikası, kişisel verilerin korunması ve KVKK uyum bilgileri.', <<<'HTML'
<h1>Prensbet Gizlilik Politikası</h1>
<p>Prensbet olarak kullanıcılarımızın gizliliğine büyük önem veriyoruz. Bu gizlilik politikası, kişisel verilerinizin nasıl toplandığını, kullanıldığını, saklandığını ve korunduğunu açıklamaktadır.</p>

<h2>Toplanan Veriler</h2>
<p>Platformumuza kayıt olduğunuzda ad, soyad, e-posta adresi, telefon numarası ve doğum tarihi gibi kişisel bilgileriniz toplanmaktadır. Ayrıca ödeme işlemleri sırasında finansal bilgileriniz güvenli şekilde işlenmektedir.</p>

<h2>Verilerin Kullanımı</h2>
<p>Toplanan kişisel veriler; hesap yönetimi, ödeme işlemleri, müşteri desteği, güvenlik önlemleri ve yasal yükümlülüklerin yerine getirilmesi amacıyla kullanılmaktadır. Verileriniz pazarlama amaçlı olarak yalnızca onayınız dahilinde kullanılır.</p>

<h2>Veri Güvenliği</h2>
<p>256-bit SSL şifreleme, güvenlik duvarları ve düzenli güvenlik denetimleri ile verileriniz en üst düzeyde korunmaktadır. Yetkisiz erişim, değiştirme veya ifşa edilmeye karşı kapsamlı güvenlik önlemleri uygulanmaktadır.</p>

<h2>Çerez Politikası</h2>
<p>Web sitemiz, kullanıcı deneyimini iyileştirmek amacıyla çerezler kullanmaktadır. Çerezler; oturum yönetimi, tercih hatırlama ve analiz amaçlı olarak kullanılmaktadır. Tarayıcı ayarlarınızdan çerez tercihlerinizi yönetebilirsiniz.</p>

<h2>Üçüncü Taraf Paylaşımı</h2>
<p>Kişisel verileriniz, yasal zorunluluklar dışında üçüncü taraflarla paylaşılmamaktadır. Ödeme işlemcileri ve oyun sağlayıcıları ile yapılan veri paylaşımı, hizmet sunumu için zorunlu minimum düzeyde tutulmaktadır.</p>

<h2>Haklarınız</h2>
<p>Kişisel verilerinize erişim, düzeltme, silme ve işlemeyi durdurma haklarına sahipsiniz. Bu haklarınızı kullanmak için müşteri hizmetlerimize başvurabilirsiniz.</p>
HTML],

    [4, 'bonuslar', 'Prensbet Bonusları ve Kampanyalar 2026', 'Prensbet Bonusları 2026 – Hoş Geldin, Deneme ve Kayıp Bonusu', 'Prensbet bonus kampanyaları 2026: %100 hoş geldin, 1000TL deneme, %30 kayıp bonusu, kripto freespin ve VIP ayrıcalıkları.', <<<'HTML'
<h1>Prensbet Bonusları ve Kampanyalar 2026</h1>
<p>Prensbet, yeni ve mevcut üyelerine sunduğu cazip bonus kampanyalarıyla sektörde öne çıkmaktadır. Hoş geldin bonusundan deneme bonusuna, haftalık kayıp bonusundan VIP ayrıcalıklarına kadar birçok fırsattan yararlanabilirsiniz.</p>

<h2>%100 Hoş Geldin Bonusu</h2>
<p>İlk yatırımınıza %100 bonus kazanın! Casino ve spor bahisleri için geçerli hoş geldin bonusu ile Prensbet deneyiminize güçlü bir başlangıç yapın. Minimum 100TL yatırım ile bonus hak kazanılır.</p>

<h2>1000TL Deneme Bonusu</h2>
<p>Yeni üyelere özel 1000TL deneme bonusu! Üyeliğinizi tamamladıktan sonra canlı destek hattından bonus talebinde bulunun. Deneme bonusu ile platformu risksiz keşfedin.</p>

<h2>%100 Geri İade Bonusu</h2>
<p>İlk yatırımınızda kayıp yaşamanız durumunda %100 geri iade bonusu ile kaybınız telafi edilir. Bu kampanya yeni üyelerimizin platforma güvenle başlamasını sağlar.</p>

<h2>%30 Haftalık Kayıp Bonusu</h2>
<p>Her hafta kayıplarınızın %30'unu geri alın! Otomatik olarak hesabınıza tanımlanan haftalık kayıp bonusu, oyun keyfinizi sürdürmenize yardımcı olur.</p>

<h2>%15 Çevrimsiz Aviator Bonusu</h2>
<p>Aviator ve diğer crash oyunlarına özel %15 çevrimsiz yatırım bonusu! Çevrim şartı olmadan doğrudan oyununuza bonus ekleyin.</p>

<h2>Kripto Freespin Kampanyası</h2>
<p>Kripto para ile yatırım yapanlara özel freespin hediyesi! Bitcoin, Ethereum ve USDT yatırımlarınızda ekstra freespin kazanın.</p>

<h2>Kumbara Puan Sistemi</h2>
<p>Her oynadığınızda puan biriktirin, puanlarınızı bonus bakiyeye çevirin. Casino ve spor bahislerinde yapılan her işlem puan kazandırır.</p>

<h2>5000TL Üstü Yatırım Bonusu</h2>
<p>5000TL ve üzeri yatırımlarda %10 nakit bonus + %30 kayıp bonusu avantajından yararlanın. Yüksek yatırım yapan kullanıcılara özel ayrıcalıklar.</p>

<h2>VIP Club Ayrıcalıkları</h2>
<p>Prensbet VIP Club üyeleri kişisel hesap yöneticisi, özel bonuslar, yüksek çekim limitleri, doğum günü bonusu ve öncelikli destek hizmetinden yararlanır.</p>

<h2>Genel Bonus Kuralları</h2>
<ul>
<li>Her bonus kampanyasının kendine özgü çevrim şartları bulunmaktadır</li>
<li>Aynı anda birden fazla bonus aktif edilemez</li>
<li>Bonus kullanım süresi kampanyaya göre değişmektedir</li>
<li>Minimum yatırım tutarları kampanya bazında belirlenmektedir</li>
<li>Bonus kötüye kullanımı tespit edildiğinde bonus ve kazançlar iptal edilebilir</li>
</ul>
HTML],

    [5, 'canli-bahis', 'Prensbet Canlı Bahis Rehberi', 'Prensbet Canlı Bahis 2026 – Yüksek Oranlar ve Geniş Branşlar', 'Prensbet canlı bahis rehberi: futbol, basketbol, tenis ve 30+ spor dalında yüksek oranlar, anlık istatistikler.', <<<'HTML'
<h1>Prensbet Canlı Bahis Rehberi</h1>
<p>Prensbet canlı bahis bölümü, dünya genelindeki spor etkinliklerine anlık bahis yapma imkânı sunmaktadır. Gelişmiş canlı bahis altyapısı, yüksek oranlar ve geniş market seçenekleri ile profesyonel bir deneyim yaşayabilirsiniz.</p>

<h2>Canlı Bahis Spor Dalları</h2>
<p>Futbol, basketbol, tenis, voleybol, hentbol, buz hokeyi, masa tenisi, badminton, beyzbol ve daha birçok spor dalında canlı bahis seçenekleri mevcuttur. Ayrıca CS:GO, Dota 2, League of Legends ve Valorant gibi espor branşlarında da canlı bahis yapabilirsiniz.</p>

<h2>Canlı Bahis Özellikleri</h2>
<ul>
<li><strong>Anlık Oranlar:</strong> Maç akışına göre saniyeler içinde güncellenen oranlar</li>
<li><strong>Canlı İstatistikler:</strong> Detaylı maç istatistikleri ve grafik gösterimi</li>
<li><strong>Cash Out:</strong> Maç bitmeden kuponunuzu nakde çevirme imkânı</li>
<li><strong>Hızlı Kupon:</strong> Tek tıkla bahis yapma özelliği</li>
<li><strong>Kombine Kupon:</strong> Birden fazla maçı birleştirerek yüksek kazanç potansiyeli</li>
</ul>

<h2>Canlı Bahis Stratejileri</h2>
<p>Başarılı canlı bahis için maç öncesi araştırma yapın, takım formlarını ve istatistiklerini inceleyin. Bütçe yönetimine dikkat edin ve duygusal kararlar vermekten kaçının. Canlı bahiste sabırlı olmak ve doğru anı beklemek büyük önem taşımaktadır.</p>

<h2>Maç Öncesi Bahis</h2>
<p>Canlı bahis dışında, maç başlamadan önce de geniş oran seçenekleriyle bahis yapabilirsiniz. Maç öncesi bahislerde genellikle daha yüksek oranlar sunulmakta ve daha geniş market seçenekleri bulunmaktadır.</p>
HTML],

    [6, 'casino-oyunlari', 'Prensbet Casino Oyunları', 'Prensbet Casino Oyunları 2026 – Slot, Rulet, Blackjack Rehberi', 'Prensbet casino oyunları: 5000+ slot, canlı rulet, blackjack, poker, Aviator ve crash oyunları.', <<<'HTML'
<h1>Prensbet Casino Oyunları</h1>
<p>Prensbet casino bölümü, dünyanın en popüler oyun sağlayıcılarının binlerce oyununu tek çatı altında sunmaktadır. Slot oyunlarından masa oyunlarına, canlı casinodan crash oyunlarına kadar geniş bir yelpaze mevcuttur.</p>

<h2>Slot Oyunları</h2>
<p>5000'den fazla slot oyunu ile Prensbet, slot severler için cennet gibidir. Gates of Olympus, Sweet Bonanza, The Dog House Megaways, Big Bass Bonanza, Starlight Princess, Sugar Rush ve daha birçok popüler slot mevcuttur. Klasik, video, megaways, jackpot ve bonus buy slotlar arasından seçim yapabilirsiniz.</p>

<h2>Canlı Casino</h2>
<p>Evolution Gaming ve Pragmatic Play Live altyapısıyla sunulan canlı casino masalarında gerçek krupiyelerle oynayın. Lightning Roulette, Crazy Time, XXXTreme Lightning Roulette, Mega Ball, Monopoly Live ve daha fazlası.</p>

<h2>Masa Oyunları</h2>
<p>Rulet, blackjack, baccarat ve poker çeşitleri ile klasik casino deneyimini yaşayın. Avrupa Ruleti, Amerikan Ruleti, French Roulette, Multi-Hand Blackjack ve Texas Hold'em Poker gibi popüler varyasyonlar mevcuttur.</p>

<h2>Crash ve Mini Oyunlar</h2>
<p>Aviator, Spaceman, JetX, Plinko, Mines ve daha birçok heyecanlı crash ve mini oyun Prensbet'te sizi bekliyor. Hızlı kazanç fırsatları sunan bu oyunlar, kısa sürede eğlenceli bir deneyim yaşatır.</p>

<h2>Oyun Sağlayıcıları</h2>
<p>Pragmatic Play, Evolution Gaming, NetEnt, Microgaming, Play'n GO, Yggdrasil, Red Tiger, Betsoft, Hacksaw Gaming, Push Gaming ve daha birçok lider sağlayıcının oyunları Prensbet'te.</p>
HTML],

    [7, 'sss', 'Prensbet Sıkça Sorulan Sorular', 'Prensbet SSS – Sıkça Sorulan Sorular ve Cevaplar', 'Prensbet hakkında sıkça sorulan sorular: üyelik, bonus, para yatırma, güvenlik ve daha fazlası.', <<<'HTML'
<h1>Prensbet Sıkça Sorulan Sorular</h1>

<h2>Genel Sorular</h2>

<h3>Prensbet güvenilir mi?</h3>
<p>Evet, Prensbet Curaçao eGaming lisansına sahip, güvenilir bir online bahis ve casino platformudur. 256-bit SSL şifreleme, iki faktörlü kimlik doğrulama ve bağımsız denetimlerle kullanıcı güvenliği sağlanmaktadır.</p>

<h3>Prensbet'e nasıl üye olunur?</h3>
<p>Güncel giriş adresine giderek "Kayıt Ol" butonuna tıklayın. Ad, soyad, e-posta, telefon numarası ve doğum tarihi bilgilerinizi girerek üyeliğinizi tamamlayın. E-posta veya SMS doğrulamasından sonra hesabınız aktif olacaktır.</p>

<h3>Prensbet güncel giriş adresi nedir?</h3>
<p>Prensbet güncel giriş adresi prenssbet.org üzerinden platforma erişim sağlayabilirsiniz. Güncel adres değişikliklerini Telegram kanalımızdan takip edebilirsiniz.</p>

<h2>Bonus Soruları</h2>

<h3>Deneme bonusu nasıl alınır?</h3>
<p>Üyeliğinizi tamamladıktan sonra canlı destek hattına başvurarak 1000TL deneme bonusu talebinde bulunabilirsiniz. Bonus hesabınıza birkaç dakika içinde tanımlanır.</p>

<h3>Hoş geldin bonusu çevrim şartı nedir?</h3>
<p>%100 hoş geldin bonusunun çevrim şartı kampanya dönemine göre değişmektedir. Güncel çevrim şartları için bonus kuralları sayfamızı inceleyebilir veya canlı destekten bilgi alabilirsiniz.</p>

<h3>Birden fazla bonus kullanılabilir mi?</h3>
<p>Aynı anda birden fazla bonus aktif edilemez. Mevcut bonusunuzun çevrimi tamamlandıktan veya iptal edildikten sonra yeni bonus talebinde bulunabilirsiniz.</p>

<h2>Ödeme Soruları</h2>

<h3>Minimum para yatırma tutarı nedir?</h3>
<p>Minimum yatırım tutarı ödeme yöntemine göre değişmektedir. Papara ile minimum 50TL, kripto ile minimum 100TL, banka havalesi ile minimum 100TL yatırım yapılabilmektedir.</p>

<h3>Para çekme ne kadar sürer?</h3>
<p>Papara ve kripto ile çekimler genellikle 15-30 dakika, banka havalesi ile çekimler 1-24 saat içinde tamamlanmaktadır. İlk çekim işleminde hesap doğrulama gerekebilir.</p>

<h3>Hangi kripto paralar kabul ediliyor?</h3>
<p>Bitcoin (BTC), Ethereum (ETH), Tether (USDT), Litecoin (LTC) ve Ripple (XRP) ile yatırım ve çekim yapılabilmektedir.</p>

<h2>Teknik Sorular</h2>

<h3>Mobil uygulama var mı?</h3>
<p>Prensbet'in ayrı bir mobil uygulaması bulunmamaktadır ancak mobil tarayıcıdan tam uyumlu erişim sağlanabilir. "Ana ekrana ekle" seçeneği ile uygulama benzeri deneyim elde edebilirsiniz.</p>

<h3>Siteye erişemiyorum ne yapmalıyım?</h3>
<p>DNS ayarlarınızı Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) olarak değiştirin. VPN kullanarak erişim sağlayabilirsiniz. Güncel adres için Telegram kanalımızı kontrol edin.</p>
HTML],

    [8, 'sorumluluk-reddi', 'Prensbet Sorumluluk Reddi', 'Prensbet Sorumluluk Reddi – Sorumlu Bahis Rehberi', 'Prensbet sorumluluk reddi, sorumlu bahis ilkeleri ve oyun bağımlılığı destek bilgileri.', <<<'HTML'
<h1>Prensbet Sorumluluk Reddi</h1>
<p>Prensbet olarak sorumlu oyun ilkelerini benimsiyoruz ve kullanıcılarımızın sağlıklı bir oyun alışkanlığı geliştirmelerine destek oluyoruz.</p>

<h2>Sorumlu Bahis İlkeleri</h2>
<ul>
<li>Bahis ve casino oyunlarını bir eğlence aracı olarak görün, gelir kaynağı olarak değil</li>
<li>Kaybetmeyi göze alabileceğiniz tutarlarla oynayın</li>
<li>Kendinize günlük, haftalık ve aylık bütçe limitleri belirleyin</li>
<li>Kayıpların peşinden koşmayın</li>
<li>Alkol veya stres altındayken bahis yapmayın</li>
<li>Düzenli aralar verin</li>
</ul>

<h2>Yaş Sınırı</h2>
<p>Prensbet, 18 yaşından küçüklerin platformu kullanmasına izin vermemektedir. Hesap açılışı sırasında yaş doğrulaması yapılmaktadır ve gerektiğinde ek kimlik doğrulama istenebilir.</p>

<h2>Hesap Limitleri</h2>
<p>Kullanıcılar, hesap ayarlarından yatırım limiti, kayıp limiti ve oturum süresi limiti belirleyebilir. Bu limitler, sorumlu oyun alışkanlığınızı desteklemek için tasarlanmıştır.</p>

<h2>Kendini Dışlama</h2>
<p>Oyun alışkanlığınız üzerinde kontrol kaybettiğinizi hissediyorsanız, belirli bir süre veya kalıcı olarak hesabınızı askıya alabilirsiniz. Müşteri hizmetlerimizle iletişime geçerek kendini dışlama talebinde bulunabilirsiniz.</p>

<h2>Yardım ve Destek</h2>
<p>Oyun bağımlılığı konusunda profesyonel yardım almak için yerel destek hatlarına başvurabilirsiniz. Prensbet olarak kullanıcılarımızın sağlığını ve refahını önemsiyoruz.</p>

<h2>Yasal Uyarı</h2>
<p>Bu sitedeki içerikler yalnızca bilgilendirme amaçlıdır. Online bahis ve casino oyunları finansal risk içermektedir. Kullanıcılar, bulundukları ülkedeki yasal düzenlemelere uymakla yükümlüdür.</p>
HTML],

    [9, 'prensbet-giris', 'Prensbet Giriş Rehberi 2026', 'Prensbet Giriş 2026 – Güncel Adres ve Hızlı Erişim Rehberi', 'Prensbet güncel giriş adresi 2026, adım adım giriş rehberi, DNS ayarları ve VPN ile erişim.', <<<'HTML'
<h1>Prensbet Giriş Rehberi 2026</h1>
<p>Prensbet platformuna güvenli ve hızlı erişim sağlamak için bu rehberi takip edebilirsiniz. Güncel giriş adresi, alternatif erişim yöntemleri ve sorun çözüm önerileri bu sayfada detaylı olarak açıklanmaktadır.</p>

<h2>Güncel Giriş Adresi</h2>
<p>Prensbet'in güncel giriş adresi <strong>prenssbet.org</strong> olarak belirlenmiştir. Platform, erişim engellerine karşı düzenli olarak adres güncellemesi yapmaktadır. En güncel adres bilgisine bu sayfa ve Telegram kanalımız üzerinden ulaşabilirsiniz.</p>

<h2>Giriş Yapma Adımları</h2>
<ol>
<li>Güncel Prensbet giriş adresine gidin: prenssbet.org</li>
<li>"Giriş Yap" butonuna tıklayın</li>
<li>Kullanıcı adınızı veya e-posta adresinizi girin</li>
<li>Şifrenizi yazın ve giriş yapın</li>
<li>İki faktörlü doğrulama aktifse onay kodunu girin</li>
</ol>

<h2>Erişim Sorunları ve Çözümleri</h2>
<h3>DNS Değişikliği</h3>
<p>Google DNS (8.8.8.8, 8.8.4.4) veya Cloudflare DNS (1.1.1.1, 1.0.0.1) kullanarak erişim sorunlarını çözebilirsiniz. Windows, Mac, Android ve iOS cihazlarınızda DNS ayarlarını değiştirebilirsiniz.</p>

<h3>VPN Kullanımı</h3>
<p>Güvenilir bir VPN uygulaması kullanarak Prensbet'e erişim sağlayabilirsiniz. Ücretsiz ve ücretli VPN seçenekleri mevcuttur.</p>

<h3>Tarayıcı Cache Temizleme</h3>
<p>Tarayıcınızın önbelleğini ve çerezlerini temizleyerek erişim sorunlarını giderebilirsiniz.</p>

<h2>Şifremi Unuttum</h2>
<p>Giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayarak kayıtlı e-posta adresinize şifre sıfırlama linki gönderebilirsiniz.</p>

<h2>Güvenli Giriş İpuçları</h2>
<ul>
<li>Yalnızca resmi giriş adreslerini kullanın</li>
<li>Güçlü ve benzersiz bir şifre belirleyin</li>
<li>İki faktörlü doğrulamayı aktif edin</li>
<li>Halka açık Wi-Fi ağlarında giriş yapmaktan kaçının</li>
<li>Tarayıcınıza şifre kaydetmemeyi tercih edin</li>
</ul>
HTML],

    [10, 'prensbet-bonus', 'Prensbet Bonus Rehberi 2026', 'Prensbet Bonus Rehberi 2026 – Çevrim Şartları ve Kampanya Detayları', 'Prensbet bonus rehberi: çevrim şartları, bonus türleri, kullanım koşulları ve kampanya detayları.', <<<'HTML'
<h1>Prensbet Bonus Rehberi 2026</h1>
<p>Prensbet'in sunduğu tüm bonus kampanyalarının detaylı açıklamaları, çevrim şartları ve kullanım koşulları bu rehberde yer almaktadır.</p>

<h2>Bonus Türleri</h2>
<h3>Hoş Geldin Bonusu</h3>
<p>İlk yatırımınıza %100 bonus. Casino ve spor bahisleri için geçerlidir. Minimum yatırım tutarı ve çevrim şartları uygulanır.</p>

<h3>Deneme Bonusu</h3>
<p>Yeni üyelere 1000TL deneme bonusu. Yatırım şartsız, canlı destekten talep edilir. Çevrim şartı tamamlandığında kazançlar çekilebilir.</p>

<h3>Yatırım Bonusu</h3>
<p>Her yatırımda ekstra bonus kazanma fırsatı. Ödeme yöntemine ve yatırım tutarına göre bonus oranı değişir.</p>

<h3>Kayıp Bonusu</h3>
<p>Haftalık kayıpların %30'u bonus olarak iade edilir. Otomatik tanımlama veya canlı destekten talep ile alınabilir.</p>

<h2>Çevrim Şartları Rehberi</h2>
<p>Her bonus kampanyasının kendine özgü çevrim şartları bulunmaktadır. Çevrim şartı, bonus tutarının belirli bir kat oranında bahis yapılması gerektiği anlamına gelir. Örneğin, 5x çevrim şartı olan 100TL bonus için 500TL değerinde bahis yapılması gerekir.</p>

<h2>Bonus Kullanım Kuralları</h2>
<ul>
<li>Aynı anda tek bonus aktif edilebilir</li>
<li>Her kampanyanın geçerlilik süresi vardır</li>
<li>Bonus kötüye kullanımı tespitinde kazançlar iptal edilebilir</li>
<li>Çevrim esnasında maksimum bahis limitlerine uyulmalıdır</li>
<li>Bazı oyunlar çevrim hesaplamasına farklı oranlarda dahil olabilir</li>
</ul>
HTML],

    [11, 'prensbet-mobil-giris', 'Prensbet Mobil Giriş Rehberi', 'Prensbet Mobil Giriş 2026 – Android ve iOS Erişim Rehberi', 'Prensbet mobil giriş rehberi: Android ve iOS cihazlardan erişim, mobil özellikler ve performans ipuçları.', <<<'HTML'
<h1>Prensbet Mobil Giriş Rehberi</h1>
<p>Prensbet, mobil uyumlu responsive tasarımı sayesinde akıllı telefonlar ve tabletler üzerinden kusursuz bir kullanım deneyimi sunmaktadır.</p>

<h2>Android Cihazlardan Erişim</h2>
<p>Chrome, Firefox veya Brave tarayıcısından güncel Prensbet adresine giderek giriş yapabilirsiniz. "Ana ekrana ekle" seçeneği ile masaüstüne kısayol oluşturarak uygulama benzeri deneyim elde edebilirsiniz.</p>

<h2>iOS Cihazlardan Erişim</h2>
<p>iPhone ve iPad kullanıcıları Safari tarayıcısından Prensbet'e erişebilir. Paylaş menüsünden "Ana Ekrana Ekle" seçeneğiyle kısayol oluşturabilirsiniz.</p>

<h2>Mobil Özellikler</h2>
<ul>
<li>Tüm casino oyunlarına mobil erişim (5000+ oyun)</li>
<li>Canlı bahis ve canlı casino tam desteği</li>
<li>Mobil para yatırma ve çekme işlemleri</li>
<li>Dokunmatik optimizeli arayüz</li>
<li>Hızlı yükleme süreleri</li>
</ul>

<h2>Mobil Performans İpuçları</h2>
<ul>
<li>Tarayıcınızı güncel tutun</li>
<li>Wi-Fi bağlantısı tercih edin (özellikle canlı casino için)</li>
<li>Arka planda çalışan gereksiz uygulamaları kapatın</li>
<li>Düzenli olarak tarayıcı cache temizliği yapın</li>
</ul>
HTML],

    [12, 'prensbet-casino', 'Prensbet Casino Rehberi', 'Prensbet Casino Rehberi 2026 – Strateji, Oyunlar ve İpuçları', 'Prensbet casino rehberi: masa oyunları stratejileri, slot ipuçları, RTP bilgileri ve oyun tavsiyeleri.', <<<'HTML'
<h1>Prensbet Casino Rehberi</h1>
<p>Prensbet casino bölümünde başarılı olmanız için kapsamlı rehberimizi hazırladık. Oyun stratejileri, RTP bilgileri ve uzman tavsiyeleri bu sayfada.</p>

<h2>Slot Oyunları Stratejisi</h2>
<p>Slot oyunlarında RTP (Return to Player) oranına dikkat edin. %96 üzeri RTP'ye sahip oyunlar uzun vadede daha avantajlıdır. Volatilite seviyesini bütçenize göre seçin: düşük volatilite sık kazanç, yüksek volatilite büyük kazanç potansiyeli sunar.</p>

<h2>Rulet Stratejileri</h2>
<p>Avrupa Ruleti, Amerikan Ruletine göre daha düşük kasa avantajına sahiptir (2.7% vs 5.26%). Martingale, Fibonacci ve D'Alembert gibi bahis stratejilerini deneyebilirsiniz ancak hiçbir stratejinin garantili kazanç sunmadığını unutmayın.</p>

<h2>Blackjack Temel Strateji</h2>
<p>Blackjack'te temel strateji tablosunu kullanarak kasa avantajını minimize edebilirsiniz. Soft ve hard ellerde doğru kararlar vermek büyük fark yaratır. Sigortadan kaçının ve split kurallarını iyi öğrenin.</p>

<h2>Bütçe Yönetimi</h2>
<p>Casino oyunlarında en önemli kural, bütçe yönetimidir. Kaybetmeyi göze alabileceğiniz bir bütçe belirleyin, kazanç hedefi koyun ve hedefe ulaştığınızda durun. Kayıpların peşinden koşmak en büyük hatadır.</p>

<h2>Demo Mod Kullanımı</h2>
<p>Gerçek para yatırmadan önce oyunları demo modunda deneyerek mekaniklerini ve bonus özelliklerini öğrenin. Bu, hem eğlenceli hem de eğitici bir deneyim sunar.</p>
HTML],

    [13, 'prensbet-kayit', 'Prensbet Kayıt ve Üyelik Rehberi', 'Prensbet Kayıt 2026 – Adım Adım Üyelik Rehberi', 'Prensbet kayıt ve üyelik rehberi: adım adım hesap açma, kimlik doğrulama ve hoş geldin bonusu.', <<<'HTML'
<h1>Prensbet Kayıt ve Üyelik Rehberi</h1>
<p>Prensbet'e üye olmak hızlı ve kolaydır. Bu rehberde adım adım kayıt işlemini, hesap doğrulamayı ve ilk yatırım bonusunu nasıl alacağınızı öğrenebilirsiniz.</p>

<h2>Kayıt Adımları</h2>
<ol>
<li><strong>Siteye Girin:</strong> Güncel Prensbet giriş adresini (prenssbet.org) ziyaret edin</li>
<li><strong>Kayıt Ol:</strong> Ana sayfadaki "Kayıt Ol" butonuna tıklayın</li>
<li><strong>Bilgilerinizi Girin:</strong> Ad, soyad, doğum tarihi, e-posta ve telefon numaranızı yazın</li>
<li><strong>Kullanıcı Adı:</strong> Benzersiz bir kullanıcı adı seçin</li>
<li><strong>Şifre:</strong> En az 8 karakter, büyük harf, küçük harf ve rakam içeren güçlü bir şifre belirleyin</li>
<li><strong>Doğrulama:</strong> E-posta veya SMS ile hesabınızı doğrulayın</li>
</ol>

<h2>Hesap Doğrulama</h2>
<p>Para çekme işlemi yapabilmek için hesabınızın doğrulanması gerekmektedir. Kimlik belgesi (nüfus cüzdanı/ehliyet/pasaport) ve adres belgesi (fatura/banka ekstresi) fotoğraflarını yükleyin. Doğrulama genellikle 24 saat içinde tamamlanır.</p>

<h2>Kayıt Sonrası Yapılacaklar</h2>
<ul>
<li>Canlı destekten 1000TL deneme bonusu talebinde bulunun</li>
<li>İlk yatırımınızı yaparak %100 hoş geldin bonusu kazanın</li>
<li>İki faktörlü doğrulamayı aktif edin</li>
<li>Hesap limitlerini belirleyin (sorumlu oyun)</li>
</ul>

<h2>Kayıt Şartları</h2>
<ul>
<li>18 yaşından büyük olmak zorunludur</li>
<li>Her kullanıcı yalnızca bir hesap açabilir</li>
<li>Gerçek ve doğru bilgiler vermek gereklidir</li>
<li>Platform kullanım koşulları kabul edilmelidir</li>
</ul>
HTML],

    [14, 'prensbet-para-yatirma', 'Prensbet Para Yatırma ve Çekme', 'Prensbet Para Yatırma 2026 – Hızlı Ödeme Yöntemleri Rehberi', 'Prensbet para yatırma ve çekme rehberi: Papara, kripto, banka havalesi, limitler ve işlem süreleri.', <<<'HTML'
<h1>Prensbet Para Yatırma ve Çekme Rehberi</h1>
<p>Prensbet, çeşitli ödeme yöntemleriyle hızlı ve güvenli para yatırma-çekme işlemleri sunmaktadır. Bu rehberde tüm ödeme yöntemlerinin detaylarını bulabilirsiniz.</p>

<h2>Para Yatırma Yöntemleri</h2>

<h3>Papara</h3>
<p>En popüler yatırım yöntemi. Anında hesabınıza yansır. Minimum 50TL, komisyonsuz. Papara hesabınızdan kolayca transfer yapabilirsiniz.</p>

<h3>Kripto Para</h3>
<p>Bitcoin, Ethereum, USDT, Litecoin ve Ripple ile yatırım yapabilirsiniz. Düşük komisyon ve hızlı işlem süresi avantajı. Minimum 100TL karşılığı kripto.</p>

<h3>Banka Havalesi/EFT</h3>
<p>Tüm Türk bankalarından havale ve EFT ile yatırım yapılabilir. Mesai saatleri içinde EFT anlık, havale 1-2 saat sürebilir. Minimum 100TL.</p>

<h3>QR Kod ile Ödeme</h3>
<p>Mobil bankacılık uygulamanız ile QR kod okutarak hızlı yatırım yapın. Anında hesabınıza yansır.</p>

<h2>Para Çekme Yöntemleri</h2>
<ul>
<li><strong>Papara:</strong> 15-30 dakika içinde çekim tamamlanır</li>
<li><strong>Kripto:</strong> 15-60 dakika içinde cüzdanınıza ulaşır</li>
<li><strong>Banka Havalesi:</strong> 1-24 saat içinde hesabınıza yansır</li>
</ul>

<h2>Önemli Bilgiler</h2>
<ul>
<li>İlk çekimde hesap doğrulama gereklidir</li>
<li>Yatırım yapılan yöntemle çekim önceliği uygulanır</li>
<li>Minimum çekim tutarı yönteme göre değişir</li>
<li>Aktif bonus varken çekim yapılamayabilir</li>
<li>Günlük ve aylık çekim limitleri VIP seviyesine göre belirlenir</li>
</ul>
HTML],

    [15, 'prensbet-slot', 'Prensbet Slot Oyunları Rehberi', 'Prensbet Slot Oyunları 2026 – En Çok Kazandıran Slotlar', 'Prensbet slot oyunları rehberi: popüler slotlar, RTP oranları, bonus buy özellikleri ve kazanma stratejileri.', <<<'HTML'
<h1>Prensbet Slot Oyunları Rehberi</h1>
<p>Prensbet'te 5000'den fazla slot oyunu bulunmaktadır. Bu rehberde en popüler slot oyunları, RTP bilgileri ve kazanma stratejileri hakkında detaylı bilgi alabilirsiniz.</p>

<h2>En Popüler Slot Oyunları</h2>

<h3>Gates of Olympus</h3>
<p>Pragmatic Play'in efsanevi slot oyunu. %96.5 RTP, yüksek volatilite. Zeus temasıyla çarpan özelliği sayesinde büyük kazançlar mümkün. Bonus buy özelliği mevcut.</p>

<h3>Sweet Bonanza</h3>
<p>Şeker temalı popüler slot. %96.49 RTP, orta-yüksek volatilite. Tumble mekanizması ve çarpan özelliği ile eğlenceli kazanç fırsatları sunar.</p>

<h3>The Dog House Megaways</h3>
<p>117.649 kazanma yolu ile megaways mekanizması. %96.55 RTP, yüksek volatilite. Yapışkan wild özelliği ile büyük kazanç potansiyeli.</p>

<h3>Big Bass Bonanza</h3>
<p>Balık tutma temalı eğlenceli slot. %96.71 RTP, orta-yüksek volatilite. Money symbol toplama mekanizması ile büyük ödüller.</p>

<h3>Starlight Princess</h3>
<p>Anime temalı Pragmatic Play slotu. %96.5 RTP, yüksek volatilite. Gates of Olympus benzeri çarpan mekanizması ile büyük kazanç fırsatları.</p>

<h2>Slot Terimleri</h2>
<ul>
<li><strong>RTP:</strong> Return to Player - oyuncuya geri ödeme oranı</li>
<li><strong>Volatilite:</strong> Kazanç sıklığı ve büyüklüğü göstergesi</li>
<li><strong>Megaways:</strong> Değişken sembol sayısı ile yüksek kazanma yolu</li>
<li><strong>Wild:</strong> Diğer sembollerin yerine geçen joker sembol</li>
<li><strong>Scatter:</strong> Free spin özelliğini tetikleyen sembol</li>
<li><strong>Bonus Buy:</strong> Free spin turuna doğrudan satın alarak girme</li>
</ul>

<h2>Slot Stratejileri</h2>
<ul>
<li>Yüksek RTP'li oyunları tercih edin (%96 üzeri)</li>
<li>Bütçenize uygun volatilite seviyesi seçin</li>
<li>Demo modda oyunları tanıyın</li>
<li>Bonus buy özelliğini dikkatli kullanın</li>
<li>Kazanç hedefi belirleyin ve hedefe ulaşınca durun</li>
</ul>
HTML],
];

foreach ($pages as [$sort, $slug, $title, $metaTitle, $metaDesc, $content]) {
    DB::connection('tenant')->table('pages')->updateOrInsert(
        ['slug' => $slug],
        [
            'title' => $title, 'content' => $content,
            'meta_title' => $metaTitle, 'meta_description' => $metaDesc,
            'is_published' => 1, 'sort_order' => $sort,
            'created_at' => $now, 'updated_at' => $now,
        ]
    );
}
echo "✓ 15 additional pages created\n";
