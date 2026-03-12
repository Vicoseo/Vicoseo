<?php

/**
 * Lizabet Sites — Static Homepage Content Seed
 * Creates unique, SEO-optimized landing page content for 4 Lizabet domains.
 * Each site targets different search intent. No sliders, no carousels.
 * Layout: static image + headline + text sections (premium landing page style).
 *
 * Also removes display_type='slider' promotions so PromotionSlider doesn't render.
 *
 * Usage:
 *   cd /var/www/multi-tenant-cms/backend
 *   php artisan tinker --execute="require 'seed_lizabet_homepages.php';"
 */

use App\Models\Site;
use App\Models\SitePromotion;
use Illuminate\Support\Facades\DB;

$imgBase = '/storage/promotions/lizabet';

// ============================================================
// 1) lizabetcasino.com — Main Casino Brand
// ============================================================
$pages['lizabetcasino.com'] = [
    'title' => 'Lizabet Casino — Canlı Casino, Slot Oyunları ve Bonus Fırsatları 2026',
    'meta_title' => 'Lizabet Casino 2026 — Canlı Casino, Slot ve %50 Hoşgeldin Bonusu',
    'meta_description' => 'Lizabet Casino 2026 güncel giriş. %50 hoşgeldin bonusu, binlerce slot oyunu, canlı casino masaları ve hızlı kripto çekim. Güvenli ve lisanslı platform.',
    'content' => <<<HTML
<article>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/50-hosgeldin-SLIDER.jpg" alt="Lizabet Casino %50 Hoşgeldin Bonusu 2026" width="3190" height="900" loading="eager" />
</figure>
<h2>Lizabet Casino — %50 Hoşgeldin Bonusu ile Oynamaya Başlayın</h2>
<p>Lizabet Casino, 2026 yılında Türkiye'nin en güvenilir ve kapsamlı online casino platformlarından biri olarak hizmet vermektedir. Yeni üyeler için sunulan %50 hoşgeldin bonusu, casino dünyasına güçlü bir başlangıç yapmanızı sağlar.</p>
<p>İlk yatırımınızın yarısı kadar ekstra bakiye ile slot, canlı casino ve masa oyunlarında şansınızı deneyebilirsiniz. Bonus, hesabınıza otomatik olarak tanımlanır ve çevrim şartları piyasanın en makul oranlarındadır.</p>
<p>Lizabet'in geniş oyun yelpazesi, hızlı ödeme altyapısı ve profesyonel müşteri desteği ile casino deneyiminizi bir üst seviyeye taşıyabilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/20-casino-discount-PROMOTION.jpg" alt="Lizabet Casino %20 Casino Discount Kampanyası" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Casino Nedir?</h2>
<p>Lizabet Casino, uluslararası lisanslı bir online casino platformudur. Pragmatic Play, Evolution Gaming, NetEnt, Microgaming ve Play'n GO gibi dünyanın en prestijli oyun sağlayıcılarıyla çalışmaktadır.</p>
<p>Platform, yüzlerce slot oyunu, canlı krupiye masaları, masa oyunları ve anlık kazanç oyunlarını tek çatı altında toplar. Türkçe arayüz, 7/24 canlı destek ve hızlı ödeme seçenekleri Lizabet'i Türk oyuncular için ideal bir tercih haline getirir.</p>
<p>Güvenlik konusunda SSL şifreleme, iki faktörlü doğrulama ve lisanslı RNG altyapısı ile kullanıcı bilgileri ve oyun sonuçları tam koruma altındadır.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/500tl-deneme-SLIDER.jpg" alt="Lizabet 500TL Deneme Bonusu 2026" width="3190" height="900" loading="lazy" />
</figure>
<h2>500TL Deneme Bonusu — Risksiz Başlangıç</h2>
<p>Lizabet Casino, yeni üyelerine 500TL değerinde deneme bonusu sunarak platformu risksiz keşfetme imkanı sağlar. Bu bonus sayesinde herhangi bir yatırım yapmadan slot oyunlarını ve casino masalarını deneyebilirsiniz.</p>
<p>Deneme bonusu, üyelik işleminin ardından otomatik olarak hesabınıza tanımlanır. Bonusu kullanarak kazandığınız tutarlar, çevrim şartlarının tamamlanmasının ardından çekilebilir bakiyeye dönüşür.</p>
<p>Bu fırsat, Lizabet'in oyun kalitesini ve platform altyapısını hiçbir risk almadan denemenin en kolay yoludur.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/ertesi-gun-freespin-PROMOTION.jpg" alt="Lizabet Canlı Casino Ertesi Gün Freespin" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Canlı Casino — Gerçek Krupiyelerle Oynayın</h2>
<p>Lizabet canlı casino bölümü, HD kalitesinde yayınlanan profesyonel masalarda gerçek krupiyelerle oynama imkanı sunar. Evolution Gaming ve Pragmatic Play Live altyapısıyla Lightning Roulette, Crazy Time, Mega Ball ve Dream Catcher gibi popüler oyun şovları mevcuttur.</p>
<p>Klasik rulet, blackjack ve baccarat masalarının yanı sıra Türkçe konuşan krupiyelerle özel masalar da bulunmaktadır. Düşük limitli masalardan VIP odalara kadar geniş bir yelpaze sunulmaktadır.</p>
<p>Canlı casino deneyimi, gerçek bir kumarhanede oynama hissini evinizin konforunda yaşamanızı sağlar. Profesyonel krupiyeler, gerçek zamanlı istatistikler ve interaktif sohbet özellikleri ile sosyal bir oyun ortamı oluşturulmuştur.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/50-kripto-slot-SLIDER.jpg" alt="Lizabet Slot Oyunları %50 Kripto Slot Bonusu" width="3190" height="900" loading="lazy" />
</figure>
<h2>Lizabet Slot Oyunları — Binlerce Seçenek</h2>
<p>Lizabet'te Gates of Olympus, Sweet Bonanza, Big Bass Bonanza, Starlight Princess, Book of Dead ve daha binlerce slot oyunu sizi bekliyor. Her oyunun RTP oranı, volatilite seviyesi ve bonus özellikleri platform üzerinde detaylı olarak açıklanmaktadır.</p>
<p>Kripto para ile yatırım yapan oyuncular için %50 Kripto Slot Bonusu ile kazanç potansiyelinizi ikiye katlayabilirsiniz. Bitcoin, Ethereum ve USDT ile hızlı yatırım yaparak anında oyuna başlayabilirsiniz.</p>
<p>Pragmatic Play, NetEnt, Play'n GO, Microgaming ve Endorphina gibi önde gelen sağlayıcıların en yeni ve en popüler slotları düzenli olarak platforma eklenmektedir.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/30-kripto-kayip-PROMOTION.jpg" alt="Lizabet %30 Kripto Kayıp Bonusu" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Kripto ile Oynayın, Kayıplarınızı Geri Alın</h2>
<p>Lizabet Casino, kripto para kullanıcılarına özel %30 kayıp bonusu sunmaktadır. Bitcoin, Ethereum veya USDT ile yaptığınız yatırımlarda oluşan kayıplarınızın %30'u hesabınıza bonus olarak iade edilir.</p>
<p>Bu kampanya, kripto yatırımcıları için hem güvenli hem de avantajlı bir oyun deneyimi sağlar. Hızlı işlem süreleri, düşük komisyon oranları ve anonim yatırım imkanı ile kripto bahis deneyiminin tadını çıkarabilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/liza-sans-carki-SLIDER.jpg" alt="Liza Şans Çarkı Günlük Ödüller" width="3190" height="900" loading="lazy" />
</figure>
<h2>Liza Şans Çarkı — Her Gün Yeni Ödüller</h2>
<p>Lizabet'in özel şans çarkı etkinliği ile her gün ekstra ödüller kazanabilirsiniz. Şans çarkını çevirerek freespin, bonus bakiye, cashback ve sürpriz hediyeler elde etme şansı yakalayın.</p>
<p>Şans çarkı her gün yenilenir ve tüm aktif üyeler için ücretsiz çevirme hakkı sunar. VIP üyeler günde birden fazla çevirme hakkına sahip olabilir.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/sadakat-PROMOTION.jpg" alt="Lizabet Sadakat ve VIP Programı" width="1080" height="1080" loading="lazy" />
</figure>
<h2>VIP ve Sadakat Programı</h2>
<p>Lizabet Sadakat Programı, düzenli oyuncuları ödüllendiren çok seviyeli bir sistem sunar. Her oyununuzda puan kazanır, bu puanları bonus bakiye, freespin ve özel hediyelerle değiştirebilirsiniz.</p>
<p>VIP seviyesine ulaşan oyuncular kişisel hesap yöneticisi, özel bonus oranları, hızlı çekim önceliği ve davetiye ile etkinliklere katılma gibi ayrıcalıklardan yararlanır.</p>
<p>Sadakat programı otomatik olarak aktif edilir. Oynadıkça seviyeniz yükselir ve ayrıcalıklarınız artar.</p>
</section>

<section class="lp-section">
<h2>Güvenli Ödeme Yöntemleri</h2>
<p>Lizabet Casino, geniş ödeme yöntemi yelpazesi ile hızlı ve güvenli finansal işlemler sunar. Banka havalesi, kredi kartı, Papara, kripto para (Bitcoin, Ethereum, USDT) ve e-cüzdan seçenekleri mevcuttur.</p>
<p>Yatırım işlemleri anlık olarak gerçekleştirilir. Çekim talepleri ortalama 15 dakika içinde işleme alınır. Minimum yatırım ve çekim tutarları kullanıcı dostu limitlerdedir.</p>
<p>Tüm finansal işlemler 256-bit SSL şifreleme ile korunmaktadır. Lizabet, kullanıcı bilgilerinin güvenliği konusunda uluslararası standartlara uygun şekilde faaliyet göstermektedir.</p>
</section>

<section class="lp-section">
<h2>Sıkça Sorulan Sorular</h2>
<h3>Lizabet Casino güvenilir mi?</h3>
<p>Evet, Lizabet uluslararası lisanslı bir platformdur. Tüm oyunlar RNG sertifikalı sağlayıcılar tarafından sunulmakta olup oyun sonuçları tamamen rastgele ve adildir.</p>
<h3>Deneme bonusu nasıl alınır?</h3>
<p>Üyelik işlemini tamamladıktan sonra 500TL deneme bonusu hesabınıza otomatik olarak tanımlanır. Herhangi bir yatırım gerekmez.</p>
<h3>Hangi kripto paralar kabul ediliyor?</h3>
<p>Lizabet Bitcoin (BTC), Ethereum (ETH), Tether (USDT) ve Litecoin (LTC) ile yatırım ve çekim işlemlerini desteklemektedir.</p>
<h3>Canlı casino masalarına minimum ne kadar bahisle katılabilirim?</h3>
<p>Canlı casino masalarında minimum bahis tutarı masa türüne göre değişmektedir. Düşük limitli masalarda 5TL'den başlayan bahislerle oyuna katılabilirsiniz.</p>
</section>

</article>
HTML
];

// ============================================================
// 2) lizabetgiris.site — Access / Login Guide
// ============================================================
$pages['lizabetgiris.site'] = [
    'title' => 'Lizabet Giriş — 2026 Güncel Giriş Adresi ve Yeni Link',
    'meta_title' => 'Lizabet Giriş 2026 — Güncel Giriş Adresi, Yeni Link ve Erişim Rehberi',
    'meta_description' => 'Lizabet güncel giriş adresi 2026. Yeni erişim linki, mobil giriş rehberi, kayıt adımları ve güvenlik ipuçları. Lizabet\'e hızlı ve güvenli erişim.',
    'content' => <<<HTML
<article>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/50-hosgeldin-SLIDER.jpg" alt="Lizabet Güncel Giriş Adresi 2026" width="3190" height="900" loading="eager" />
</figure>
<h2>Lizabet Giriş — 2026 Güncel Erişim Adresi</h2>
<p>Lizabet platformuna erişim sağlamak için güncel giriş adresini kullanmanız gerekmektedir. Türkiye'de erişim kısıtlamaları nedeniyle Lizabet giriş adresi belirli aralıklarla güncellenmektedir. Bu sayfa, her zaman en güncel ve çalışan erişim linkini sunmaktadır.</p>
<p>Lizabet, kullanıcılarının kesintisiz erişim sağlaması için yeni giriş adreslerini sosyal medya kanalları, Telegram grupları ve resmi iletişim kanalları üzerinden düzenli olarak duyurmaktadır.</p>
<p>Aşağıdaki rehber ile Lizabet'e nasıl giriş yapacağınızı, hesap oluşturma adımlarını ve güvenli erişim yöntemlerini öğrenebilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/500tl-deneme-PROMOTION.jpg" alt="Lizabet Giriş Yaparak 500TL Deneme Bonusu Alın" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet'e Nasıl Giriş Yapılır?</h2>
<p>Lizabet'e giriş yapmak için şu adımları takip edin: İlk olarak bu sayfadaki güncel giriş linkine tıklayın. Ardından kullanıcı adınız ve şifreniz ile hesabınıza giriş yapın. Daha önce hesap oluşturmadıysanız, kayıt formunu doldurarak birkaç dakika içinde üyeliğinizi tamamlayabilirsiniz.</p>
<p>Yeni üyeler, kayıt işleminin ardından 500TL deneme bonusu ile platforma başlayabilir. Bu bonus, herhangi bir yatırım gerektirmeden otomatik olarak hesabınıza tanımlanır.</p>
<p>Giriş sırasında sorun yaşamanız durumunda, sayfanın alt kısmındaki alternatif erişim yöntemlerini kullanabilir veya canlı destek hattından yardım alabilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/15-cevrimsiz-SLIDER.jpg" alt="Lizabet Giriş Adresi Neden Değişir Bilgi Görseli" width="3190" height="900" loading="lazy" />
</figure>
<h2>Lizabet Giriş Adresi Neden Değişir?</h2>
<p>Türkiye'de online bahis ve casino platformlarına yönelik erişim engellemeleri uygulanmaktadır. Bu engellemeler nedeniyle Lizabet, kullanıcılarına kesintisiz hizmet sunabilmek için belirli aralıklarla giriş adresini güncellemektedir.</p>
<p>Adres değişikliği yalnızca domain adını etkiler. Hesap bilgileriniz, bakiyeniz, bonus durumunuz ve oyun geçmişiniz tamamen korunur. Yeni adres üzerinden mevcut kullanıcı adınız ve şifrenizle giriş yapabilirsiniz.</p>
<p>Güncel adresi kaçırmamak için Lizabet'in Telegram kanalını takip etmenizi ve bu sayfayı yer imlerinize eklemenizi öneririz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/20-kripto-yatirim-PROMOTION.jpg" alt="Lizabet Güncel Çalışan Adres 2026" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Güncel Çalışan Adres — Mart 2026</h2>
<p>Lizabet'in Mart 2026 itibarıyla güncel ve aktif giriş adresi bu sayfa üzerinden erişilebilir durumdadır. Adres değişikliği yapıldığında bu sayfa otomatik olarak güncellenmektedir.</p>
<p>Kripto para ile giriş yapan kullanıcılar, %20 ekstra yatırım bonusundan faydalanabilir. Bitcoin, Ethereum ve USDT ile yapılan yatırımlarda ek avantajlar sunulmaktadır.</p>
<p>Giriş yaptıktan sonra casino, bahis ve canlı oyun bölümlerinin tamamına anında erişim sağlayabilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/30-kripto-kayip-SLIDER.jpg" alt="Lizabet Telegram ve Sosyal Medya Kanalları" width="3190" height="900" loading="lazy" />
</figure>
<h2>Lizabet Telegram ve Sosyal Medya Kanalları</h2>
<p>Lizabet, güncel giriş adreslerini ve kampanya duyurularını Telegram kanalı üzerinden paylaşmaktadır. Telegram kanalına katılarak adres değişikliklerinden anında haberdar olabilirsiniz.</p>
<p>Bunun yanı sıra Lizabet'in sosyal medya hesapları üzerinden de güncel bilgilere, kampanyalara ve etkinlik duyurularına ulaşabilirsiniz. Twitter ve Instagram hesapları düzenli olarak güncellenmektedir.</p>
<p>Resmi kanallar dışındaki kaynaklardan paylaşılan linklere dikkat ediniz. Güvenliğiniz için yalnızca onaylı kaynaklardan giriş yapmanızı tavsiye ederiz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/50-kripto-slot-PROMOTION.jpg" alt="Lizabet Kayıt ve Giriş Rehberi Adım Adım" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Kayıt ve Giriş — Adım Adım Rehber</h2>
<p><strong>Adım 1:</strong> Güncel giriş adresine tıklayarak Lizabet ana sayfasına ulaşın. Sağ üst köşedeki "Kayıt Ol" butonuna tıklayın.</p>
<p><strong>Adım 2:</strong> Kayıt formunda kişisel bilgilerinizi (ad, soyad, e-posta, telefon) doğru şekilde doldurun. Güçlü bir şifre belirleyin.</p>
<p><strong>Adım 3:</strong> Hesabınız oluşturulduktan sonra kullanıcı adınız ve şifreniz ile giriş yapın. 500TL deneme bonusu otomatik olarak hesabınıza tanımlanacaktır.</p>
<p><strong>Adım 4:</strong> İlk yatırımınızı yaparak hoşgeldin bonusundan yararlanın ve oyun dünyasına adım atın.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/25-ortaklik-SLIDER.jpg" alt="Lizabet Mobil Giriş Telefondan Erişim" width="3190" height="900" loading="lazy" />
</figure>
<h2>Lizabet Mobil Giriş — Telefondan Erişim</h2>
<p>Lizabet, mobil cihazlarda tam uyumlu çalışan responsive bir tasarıma sahiptir. Herhangi bir uygulama indirmenize gerek kalmadan, telefonunuzun tarayıcısı üzerinden güncel giriş adresine erişebilirsiniz.</p>
<p>iOS ve Android cihazlarda Chrome, Safari ve diğer popüler tarayıcılar ile sorunsuz çalışır. Mobil arayüz, masaüstü versiyonun tüm özelliklerini sunmaktadır: slot oyunları, canlı casino, bahis ve finansal işlemler.</p>
<p>Mobil giriş için ana sayfayı telefonunuzun ana ekranına ekleyerek uygulama benzeri bir deneyim elde edebilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/sadakat-PROMOTION.jpg" alt="Lizabet Giriş Güvenlik İpuçları" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Giriş Güvenlik İpuçları</h2>
<p>Lizabet hesabınızın güvenliğini sağlamak için aşağıdaki adımları uygulamanızı öneririz. Güçlü ve benzersiz bir şifre kullanın. Şifrenizi düzenli aralıklarla değiştirin ve başkalarıyla paylaşmayın.</p>
<p>Giriş yaparken adres çubuğundaki SSL sertifikasını (kilit simgesi) kontrol edin. Yalnızca resmi kanallardan paylaşılan linkleri kullanın. Şüpheli e-posta veya mesajlardaki linklere tıklamaktan kaçının.</p>
<p>İki faktörlü doğrulama (2FA) özelliğini aktif ederek hesabınıza ekstra güvenlik katmanı ekleyebilirsiniz. Bu özellik, yetkisiz girişleri engeller.</p>
</section>

<section class="lp-section">
<h2>Giriş Yaptıktan Sonra Bonus Fırsatları</h2>
<p>Lizabet'e başarılı bir şekilde giriş yaptıktan sonra çeşitli bonus fırsatlarından yararlanabilirsiniz. Hoşgeldin bonusu, deneme bonusu, yatırım bonusu ve kripto bonusları aktif kampanyalar arasında yer almaktadır.</p>
<p>Düzenli oyuncular için haftalık kayıp bonusu, günlük şans çarkı ve sadakat programı gibi sürekli kampanyalar mevcuttur. Bonus detaylarına platform içindeki "Promosyonlar" bölümünden ulaşabilirsiniz.</p>
</section>

<section class="lp-section">
<h2>Lizabet Giriş — Sıkça Sorulan Sorular</h2>
<h3>Lizabet giriş adresi neden değişiyor?</h3>
<p>Türkiye'deki erişim kısıtlamaları nedeniyle giriş adresi belirli aralıklarla güncellenmektedir. Hesap bilgileriniz ve bakiyeniz değişmez.</p>
<h3>Eski adresim çalışmıyor, ne yapmalıyım?</h3>
<p>Bu sayfadaki güncel linki kullanarak giriş yapabilirsiniz. Telegram kanalımızı takip ederek yeni adresleri anında öğrenebilirsiniz.</p>
<h3>Mobil telefon ile giriş yapabilir miyim?</h3>
<p>Evet, Lizabet tüm mobil cihazlarla tam uyumlu çalışır. Herhangi bir uygulama indirmenize gerek yoktur.</p>
<h3>Giriş yaparken güvenliğimi nasıl sağlarım?</h3>
<p>Güçlü şifre kullanın, 2FA aktif edin ve yalnızca resmi kanallardan paylaşılan giriş linklerini tercih edin.</p>
</section>

</article>
HTML
];

// ============================================================
// 3) lizabahis.site — Sports Betting Focus
// ============================================================
$pages['lizabahis.site'] = [
    'title' => 'Lizabet Bahis — Canlı Bahis, Yüksek Oranlar ve Spor Bonusları 2026',
    'meta_title' => 'Lizabet Bahis 2026 — Canlı Bahis, Spor Bonusları ve Yüksek Oranlar',
    'meta_description' => 'Lizabet spor bahisleri 2026. Canlı bahis, kombine bonusu, yüksek oranlar ve 500TL deneme bonusu. Futbol, basketbol ve tüm popüler liglerde bahis yapın.',
    'content' => <<<HTML
<article>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/50-hosgeldin-SLIDER.jpg" alt="Lizabet Spor Bahisleri %50 Hoşgeldin Bonusu 2026" width="3190" height="900" loading="eager" />
</figure>
<h2>Lizabet Bahis — Spor Bahisleri ve Yüksek Oranlar</h2>
<p>Lizabet, 2026 yılında Türkiye'nin en kapsamlı spor bahis platformlarından biri olarak futboldan basketbola, tenisten voleyboluna kadar 30'dan fazla spor dalında bahis imkanı sunmaktadır.</p>
<p>Yeni üyeler %50 hoşgeldin bonusu ile spor bahislerine güçlü bir başlangıç yapabilir. Yüksek oranlar, geniş bahis marketleri ve anlık maç takibi ile profesyonel bir bahis deneyimi yaşayabilirsiniz.</p>
<p>Canlı bahis bölümünde maçları gerçek zamanlı takip ederek anında bahis yapabilir, maç içi istatistikleri değerlendirebilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/20-kombine-PROMOTION.jpg" alt="Lizabet Canlı Bahis ve Maç İçi Bahis" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Canlı Bahis — Maç İçi Bahis Fırsatları</h2>
<p>Lizabet canlı bahis bölümü, her gün yüzlerce maça anlık bahis yapma imkanı sunar. Futbol, basketbol, tenis, voleybol ve e-spor karşılaşmalarında maç devam ederken bahis yapabilirsiniz.</p>
<p>Canlı bahis arayüzü, gerçek zamanlı skor takibi, istatistik tabloları ve animasyonlu maç görselleştirmeleri içerir. Maçın gidişatını anlık olarak değerlendirerek stratejik bahis kararları verebilirsiniz.</p>
<p>Cash-out özelliği sayesinde bahsinizi maç bitmeden kapatabilir, kazancınızı güvence altına alabilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/20-kombine-SLIDER.jpg" alt="Lizabet %20 Kombine Bonusu Kampanyası" width="3190" height="900" loading="lazy" />
</figure>
<h2>%20 Kombine Bonusu ile Kazancınızı Artırın</h2>
<p>Lizabet'in kombine bahis bonusu, birden fazla maçı tek kupona ekleyen bahisçilere %20 ekstra kazanç sağlar. 3 ve üzeri maçlık kombinelerde bonus otomatik olarak uygulanır.</p>
<p>Kombine bonusu, futbol, basketbol, tenis ve diğer spor dallarındaki maçlar için geçerlidir. Yüksek oranlarla birleşen bu bonus, kazanç potansiyelinizi önemli ölçüde artırmaktadır.</p>
<p>Bonus tutarı, kuponun toplam kazancına eklenerek hesabınıza yansıtılır. Kombine bahis stratejileri ve ipuçları için platform içi rehberlerden yararlanabilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/20-spor-kayip-PROMOTION.jpg" alt="Lizabet Popüler Ligler ve Bahis Seçenekleri" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Popüler Ligler ve Bahis Seçenekleri</h2>
<p>Lizabet'te Süper Lig, Premier Lig, La Liga, Serie A, Bundesliga, Ligue 1, Şampiyonlar Ligi ve Avrupa Ligi başta olmak üzere dünyanın en prestijli futbol liglerine bahis yapabilirsiniz.</p>
<p>Basketbolda NBA, EuroLeague ve BSL; teniste Grand Slam turnuvaları; voleybol ve hentbolda Türkiye ve Avrupa ligleri bahis marketleri arasında yer almaktadır.</p>
<p>Her maç için maç sonucu, çifte şans, alt/üst, handikap, ilk yarı/maç sonu, golcü bahisleri ve onlarca farklı bahis marketi sunulmaktadır. Kayıp bonusu sayesinde spor bahislerinde kaybettiğiniz tutarın bir kısmı geri iade edilir.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/25-gece-kusu-SLIDER.jpg" alt="Lizabet Gece Kuşu Kayıp Bonusu" width="3190" height="900" loading="lazy" />
</figure>
<h2>Gece Kuşu Kayıp Bonusu — Gece Bahislerine Özel</h2>
<p>Lizabet, gece saatlerinde bahis yapan kullanıcılarına özel %25 kayıp bonusu sunmaktadır. Gece 00:00 ile 08:00 arasında yapılan spor bahislerinde oluşan kayıplarınızın %25'i hesabınıza iade edilir.</p>
<p>Bu kampanya, NBA, NHL ve Güney Amerika ligleri gibi gece saatlerinde oynanan maçlara bahis yapan kullanıcılar için ideal bir fırsattır. Gece kuşu bonusu ertesi gün otomatik olarak hesabınıza tanımlanır.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/500tl-deneme-PROMOTION.jpg" alt="Lizabet Bahis Stratejileri ve İpuçları" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Bahis Stratejileri ve İpuçları</h2>
<p>Başarılı bir bahis deneyimi için istatistikleri analiz etmek ve stratejik kararlar vermek önemlidir. Lizabet, kullanıcılarına detaylı maç istatistikleri, form tabloları ve kafa kafaya karşılaştırma verileri sunmaktadır.</p>
<p>Kombine bahislerde risk dağılımı, bankroll yönetimi ve değer bahis kavramı gibi temel stratejileri öğrenmek kazanç şansınızı artırabilir. Duygusal kararlar yerine istatistiksel analize dayalı bahis yapmanızı tavsiye ederiz.</p>
<p>Platform içindeki bahis rehberleri ve uzman analizleri ile bilgi birikiminizi geliştirebilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/20-kripto-yatirim-SLIDER.jpg" alt="Lizabet Kripto ile Bahis Hızlı Yatırım" width="3190" height="900" loading="lazy" />
</figure>
<h2>Kripto ile Bahis — Hızlı Yatırım ve Çekim</h2>
<p>Lizabet, kripto para ile bahis yapma imkanı sunan öncü platformlardan biridir. Bitcoin, Ethereum ve USDT ile saniyeler içinde yatırım yaparak anında bahis oynamaya başlayabilirsiniz.</p>
<p>Kripto yatırımlarda %20 ekstra bonus ve düşük çekim komisyonları ile avantajlı bir deneyim sunar. Çekim talepleri kripto ağ onayının ardından dakikalar içinde cüzdanınıza ulaşır.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/cifte-sans-PROMOTION.jpg" alt="Lizabet Mobil Bahis Her Yerden Bahis" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Mobil Bahis — Her Yerden Bahis Yapın</h2>
<p>Lizabet'in mobil uyumlu arayüzü ile telefonunuzdan veya tabletinizden kesintisiz bahis deneyimi yaşayabilirsiniz. Canlı bahis, maç takibi, kupon oluşturma ve finansal işlemler mobil cihazlarda sorunsuz çalışır.</p>
<p>Uygulama indirmenize gerek yoktur. Mobil tarayıcınız üzerinden giriş yaparak tüm özelliklere erişebilirsiniz. Ana ekranınıza ekleyerek uygulama benzeri bir kullanım deneyimi elde edebilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/sadakat-PROMOTION.jpg" alt="Lizabet Sadakat Programı Spor Bahisleri" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Sadakat Programı</h2>
<p>Düzenli bahis yapan kullanıcılar Lizabet Sadakat Programı ile ekstra ödüller kazanır. Her bahsinizde puan biriktirerek bonus bakiye, freebet ve özel kampanyalara erişim sağlayabilirsiniz.</p>
<p>VIP seviyesine ulaşan bahisçiler kişisel hesap yöneticisi, özel oranlar ve öncelikli çekim hakkı gibi ayrıcalıklardan yararlanır.</p>
</section>

<section class="lp-section">
<h2>Spor Bahisleri — Sıkça Sorulan Sorular</h2>
<h3>Lizabet'te minimum bahis tutarı ne kadar?</h3>
<p>Minimum bahis tutarı spor dalına ve bahis türüne göre değişmektedir. Genel olarak 1TL'den başlayan tutarlarla bahis yapabilirsiniz.</p>
<h3>Canlı bahiste cash-out nasıl kullanılır?</h3>
<p>Canlı bahis kupanınızda "Cash Out" butonu aktif olduğunda, teklif edilen tutarı kabul ederek bahsinizi erken kapatabilirsiniz.</p>
<h3>Kombine bonusu nasıl hesaplanır?</h3>
<p>3 ve üzeri maçlık kombinelerde toplam kazancınıza %20 ekstra bonus eklenir. Bonus tutarı otomatik olarak hesabınıza yansır.</p>
<h3>Kripto ile bahis yapabilir miyim?</h3>
<p>Evet, Bitcoin, Ethereum ve USDT ile yatırım yaparak tüm spor bahislerinde oynayabilirsiniz.</p>
</section>

</article>
HTML
];

// ============================================================
// 4) girislizabet.site — Informational / Trust / Reviews
// ============================================================
$pages['girislizabet.site'] = [
    'title' => 'Lizabet Rehberi — Güvenilir Mi? Yorumlar, Bonuslar ve Ödeme Yöntemleri 2026',
    'meta_title' => 'Lizabet İnceleme 2026 — Güvenilir Mi? Yorumlar, Bonuslar ve Ödeme Rehberi',
    'meta_description' => 'Lizabet güvenilir mi? 2026 güncel inceleme: kullanıcı yorumları, ödeme yöntemleri, bonus kampanyaları ve lisans bilgileri. Detaylı Lizabet rehberi.',
    'content' => <<<HTML
<article>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/50-hosgeldin-SLIDER.jpg" alt="Lizabet Rehberi Güvenilir Casino ve Bahis Platformu 2026" width="3190" height="900" loading="eager" />
</figure>
<h2>Lizabet Rehberi — Güvenilir Casino ve Bahis Platformu</h2>
<p>Lizabet, uluslararası lisanslı bir online casino ve bahis platformudur. 2026 yılında Türk kullanıcılara sunduğu geniş oyun yelpazesi, hızlı ödeme altyapısı ve profesyonel müşteri desteği ile sektörde güçlü bir konuma sahiptir.</p>
<p>Bu rehber, Lizabet hakkında merak edilen tüm konuları — güvenilirlik, ödeme yöntemleri, bonuslar, kullanıcı yorumları ve erişim bilgileri — detaylı şekilde ele almaktadır.</p>
<p>Platformu kullanmaya başlamadan önce bu kapsamlı incelemeyi okuyarak bilinçli bir karar verebilirsiniz.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/500tl-deneme-PROMOTION.jpg" alt="Lizabet Platform İncelemesi ve 500TL Deneme Bonusu" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Nedir? Detaylı Platform İncelemesi</h2>
<p>Lizabet, online casino oyunları ve spor bahisleri alanında faaliyet gösteren kapsamlı bir platformdur. Pragmatic Play, Evolution Gaming, NetEnt gibi dünyaca ünlü sağlayıcılarla çalışarak binlerce oyun seçeneği sunmaktadır.</p>
<p>Spor bahisleri bölümünde 30'dan fazla spor dalında maç öncesi ve canlı bahis imkanı mevcuttur. Yeni üyeler 500TL deneme bonusu ile platformu risksiz deneyebilir.</p>
<p>Türkçe arayüz, 7/24 canlı destek ve hızlı ödeme işlemleri Lizabet'in öne çıkan özelliklerindendir. Platform, mobil cihazlarla tam uyumlu çalışmakta olup uygulama indirmeye gerek kalmadan tarayıcı üzerinden erişilebilir.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/1m-anlik-cekim-SLIDER.jpg" alt="Lizabet Güvenilir Mi Lisans ve Güvenlik Analizi" width="3190" height="900" loading="lazy" />
</figure>
<h2>Lizabet Güvenilir Mi? Lisans ve Güvenlik Analizi</h2>
<p>Lizabet, uluslararası oyun otoritesi tarafından lisanslanmış bir platformdur. Tüm oyunlar bağımsız denetim kuruluşları tarafından test edilmekte ve RNG (Rastgele Sayı Üreteci) sertifikasına sahip bulunmaktadır.</p>
<p>Güvenlik altyapısı olarak 256-bit SSL şifreleme teknolojisi kullanılmaktadır. Kullanıcı bilgileri, finansal veriler ve oyun sonuçları tam koruma altındadır. İki faktörlü doğrulama (2FA) seçeneği ile hesap güvenliği ek katmanda sağlanmaktadır.</p>
<p>Lizabet, sorumlu oyun politikası çerçevesinde kullanıcılara bahis limiti belirleme, hesap dondurma ve kendini dışlama gibi araçlar sunmaktadır. Bu özellikler, kontrollü ve güvenli bir oyun deneyimi sağlar.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/cifte-sans-PROMOTION.jpg" alt="Lizabet Kullanıcı Yorumları ve Değerlendirmeleri" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Kullanıcı Yorumları ve Değerlendirmeleri</h2>
<p>Lizabet kullanıcıları platformu genel olarak olumlu değerlendirmektedir. Öne çıkan pozitif yorumlar arasında hızlı çekim süreleri, geniş oyun çeşitliliği ve müşteri desteğinin kalitesi yer almaktadır.</p>
<p>Kullanıcılar özellikle kripto para ile yapılan çekim işlemlerinin hızından ve komisyon oranlarının düşüklüğünden memnuniyet bildirmektedir. Canlı casino bölümünün kalitesi ve oyun sağlayıcı çeşitliliği de sıkça övülen konular arasındadır.</p>
<p>Bazı kullanıcılar erişim adresi değişikliklerinin sıklığından yakınsa da, Telegram kanalı ve resmi destek hattı üzerinden bilgilendirmelerin düzenli yapıldığı belirtilmektedir.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/15-cevrimsiz-SLIDER.jpg" alt="Lizabet Ödeme Yöntemleri Yatırım ve Çekim" width="3190" height="900" loading="lazy" />
</figure>
<h2>Lizabet Ödeme Yöntemleri — Yatırım ve Çekim Rehberi</h2>
<p>Lizabet, geniş ödeme yöntemi seçenekleri ile kullanıcılarına esnek finansal işlem imkanı sunmaktadır. Desteklenen yöntemler arasında banka havalesi, Papara, kripto para (Bitcoin, Ethereum, USDT, Litecoin) ve e-cüzdan çözümleri yer almaktadır.</p>
<p>Yatırım işlemleri genellikle anlık olarak hesaba yansır. Çekim talepleri ortalama 15 dakika içinde işleme alınmaktadır. Kripto para ile yapılan çekimler ağ onay süresine bağlı olarak birkaç dakika içinde tamamlanır.</p>
<p>Çevrimsiz yatırım bonusu ile yatırdığınız tutarın %15'i ekstra olarak hesabınıza eklenir ve herhangi bir çevrim şartı uygulanmaz. Bu, sektörde nadir bulunan avantajlı bir kampanyadır.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/5-haftalik-jest-PROMOTION.jpg" alt="Lizabet Bonusları Tüm Kampanyalar Açıklamalı" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Bonusları — Tüm Kampanyalar Açıklamalı</h2>
<p>Lizabet, yeni ve mevcut üyelerine çeşitli bonus kampanyaları sunmaktadır. Hoşgeldin bonusu (%50), deneme bonusu (500TL), çevrimsiz yatırım bonusu (%15), kombine bonusu (%20) ve haftalık jest bonusu (%5) aktif kampanyalar arasındadır.</p>
<p>Kripto kullanıcılarına özel %20 yatırım bonusu, %30 kayıp bonusu ve %50 slot bonusu gibi ek kampanyalar da mevcuttur. Her kampanyanın çevrim şartları ve geçerlilik süreleri promosyonlar sayfasında detaylı olarak açıklanmaktadır.</p>
<p>Haftalık jest bonusu, düzenli oyuncuları ödüllendiren %5 ekstra bakiye kampanyasıdır. Bu bonus her hafta otomatik olarak hesabınıza tanımlanır.</p>
</section>

<section class="lp-section">
<figure class="lp-banner">
<img src="{$imgBase}/liza-sans-carki-SLIDER.jpg" alt="Liza Şans Çarkı Nasıl Çalışır" width="3190" height="900" loading="lazy" />
</figure>
<h2>Liza Şans Çarkı Nasıl Çalışır?</h2>
<p>Lizabet'in popüler şans çarkı etkinliği, tüm aktif üyelere günlük ücretsiz çevirme hakkı sunar. Şans çarkını çevirerek freespin, bonus bakiye, cashback ve sürpriz hediyeler kazanabilirsiniz.</p>
<p>Çarkta yer alan ödüller her gün güncellenir. VIP üyeler ek çevirme hakkına sahip olabilir. Kazanılan ödüller anında hesabınıza tanımlanır ve belirtilen çevrim şartlarının ardından çekilebilir.</p>
</section>

<section class="lp-section">
<figure class="lp-visual">
<img src="{$imgBase}/paylas-kazan-PROMOTION.jpg" alt="Lizabet Mobil Kullanım Rehberi" width="1080" height="1080" loading="lazy" />
</figure>
<h2>Lizabet Mobil Kullanım Rehberi</h2>
<p>Lizabet, responsive web tasarımı sayesinde tüm mobil cihazlarda sorunsuz çalışmaktadır. iPhone, Android telefon ve tabletlerde ayrı bir uygulama indirmenize gerek kalmadan tarayıcınız üzerinden erişim sağlayabilirsiniz.</p>
<p>Mobil platformda casino oyunları, spor bahisleri, canlı bahis, finansal işlemler ve müşteri desteği dahil tüm özellikler masaüstü versiyonla aynı performansı sunar.</p>
<p>Hızlı erişim için web sitesini telefonunuzun ana ekranına eklemenizi öneririz. Bu sayede tek dokunuşla platforma ulaşabilirsiniz.</p>
</section>

<section class="lp-section">
<h2>Lizabet Güncel Erişim ve İletişim</h2>
<p>Lizabet'e erişim sağlamak için güncel giriş adresini kullanmanız gerekmektedir. Türkiye'de erişim kısıtlamaları nedeniyle adres belirli aralıklarla güncellenmektedir. Güncel adresi Telegram kanalından ve bu siteden takip edebilirsiniz.</p>
<p>Müşteri desteğine 7/24 canlı sohbet, e-posta ve sosyal medya kanalları üzerinden ulaşabilirsiniz. Ortalama yanıt süresi 2 dakikanın altındadır.</p>
</section>

<section class="lp-section">
<h2>Lizabet Hakkında Sıkça Sorulan Sorular</h2>
<h3>Lizabet güvenilir mi?</h3>
<p>Evet, Lizabet uluslararası lisanslı bir platformdur. SSL şifreleme, RNG sertifikalı oyunlar ve sorumlu oyun politikası ile güvenli bir ortam sunmaktadır.</p>
<h3>Lizabet'te hangi ödeme yöntemleri var?</h3>
<p>Banka havalesi, Papara, kripto para (Bitcoin, Ethereum, USDT), kredi kartı ve e-cüzdan seçenekleri mevcuttur.</p>
<h3>Çekim işlemleri ne kadar sürer?</h3>
<p>Çekim talepleri ortalama 15 dakika içinde işleme alınır. Kripto çekimleri ağ onay süresine bağlı olarak birkaç dakika içinde tamamlanır.</p>
<h3>Lizabet'te deneme bonusu var mı?</h3>
<p>Evet, yeni üyeler kayıt işleminin ardından 500TL deneme bonusu alabilir. Bu bonus herhangi bir yatırım gerektirmez.</p>
</section>

</article>
HTML
];

// ============================================================
// Execute: Update pages and remove slider promotions
// ============================================================

echo "=== Updating Lizabet Homepage Content ===\n\n";

foreach ($pages as $domain => $data) {
    $site = Site::where('domain', $domain)->first();
    if (!$site) {
        echo "SKIP: {$domain} not found\n";
        continue;
    }

    // Switch to tenant DB
    config(['database.connections.tenant.database' => $site->db_name]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    // Update homepage content
    $updated = DB::connection('tenant')->table('pages')
        ->where('slug', 'anasayfa')
        ->update([
            'title'            => $data['title'],
            'meta_title'       => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'content'          => $data['content'],
        ]);

    echo "{$domain}: homepage content " . ($updated ? 'UPDATED' : 'unchanged') . "\n";

    // Remove slider promotions (so PromotionSlider JS component doesn't render)
    $deleted = SitePromotion::where('site_id', $site->id)
        ->where('display_type', 'slider')
        ->delete();

    echo "{$domain}: {$deleted} slider promotions removed\n";

    // Verify remaining cards
    $remaining = SitePromotion::where('site_id', $site->id)->count();
    echo "{$domain}: {$remaining} card promotions remain\n\n";
}

echo "Done! All 4 Lizabet homepages updated.\n";
echo "Slider promotions removed — PromotionSlider will not render.\n";
echo "Card promotions kept — PromotionCards grid remains at bottom.\n";
