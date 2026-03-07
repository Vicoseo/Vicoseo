<?php

/**
 * Kaya Casino — 4 Extra Blog Posts
 * Usage: php artisan tinker --execute="$(tail -n +3 seed_kayacasino_extra_posts.php)"
 */

use App\Models\Site;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

$site = Site::where('domain', 'kayacasino.top')->firstOrFail();
Config::set('database.connections.tenant.database', $site->db_name);
DB::connection('tenant')->reconnect();

$catMap = [];
foreach (Category::on('tenant')->get() as $c) { $catMap[$c->slug] = $c->id; }

$posts = [

[
'slug' => 'kayacasino-giris',
'title' => 'Kaya Casino Giriş – Güncel Adres ve Erişim Rehberi 2026',
'category_id' => $catMap['erisim'],
'meta_title' => 'Kaya Casino Giriş 2026 – Güncel Giriş Adresi ve Erişim',
'meta_description' => 'Kaya Casino güncel giriş adresi 2026. Sorunsuz erişim için DNS, VPN rehberi ve yeni adres bilgileri.',
'excerpt' => 'Kaya Casino güncel giriş adresi ve sorunsuz erişim için bilmeniz gereken tüm yöntemler.',
'published_at' => '2026-03-06 09:00:00',
'featured_image' => '/storage/promotions/kayacasino/100ho-geldinbonusu-PROMOTIONS.jpg',
'content' => <<<'HTML'
<h2>Kaya Casino Güncel Giriş Adresi 2026</h2>

<p>Kaya Casino platformuna erişim, güncel adres bilgisi ile oldukça kolaydır. Alan adı değişiklikleri nedeniyle zaman zaman yeni giriş adresleri yayınlanmaktadır. Bu yazıda <strong>kayacasino.top</strong> üzerinden platforma nasıl giriş yapacağınızı detaylıca anlatıyoruz.</p>

<h2>Giriş Adımları</h2>

<ol>
<li><strong>Güncel adresi ziyaret edin:</strong> Tarayıcınıza kayacasino.top yazın.</li>
<li><strong>Giriş butonuna tıklayın:</strong> Sağ üst köşedeki "Giriş Yap" butonunu kullanın.</li>
<li><strong>Bilgilerinizi girin:</strong> Kullanıcı adı ve şifrenizi yazın.</li>
<li><strong>Hesabınıza erişin:</strong> Giriş yaptıktan sonra tüm oyun ve bonus seçeneklerine ulaşabilirsiniz.</li>
</ol>

<h2>Erişim Sorunu Yaşıyorsanız</h2>

<table>
<thead><tr><th>Sorun</th><th>Çözüm</th><th>Süre</th></tr></thead>
<tbody>
<tr><td>Sayfa açılmıyor</td><td>DNS ayarlarını değiştirin (1.1.1.1 veya 8.8.8.8)</td><td>1-2 dakika</td></tr>
<tr><td>Bağlantı zaman aşımı</td><td>VPN kullanarak bağlanın</td><td>2-3 dakika</td></tr>
<tr><td>Eski adres çalışmıyor</td><td>Telegram kanalından güncel adresi alın</td><td>Anlık</td></tr>
<tr><td>Şifremi unuttum</td><td>"Şifremi Unuttum" bağlantısını kullanın</td><td>5 dakika</td></tr>
</tbody>
</table>

<h2>Güvenli Giriş İçin İpuçları</h2>

<p>Kaya Casino hesabınıza güvenli giriş yapabilmek için şu önlemleri almanızı öneriyoruz:</p>

<ul>
<li>Her zaman URL çubuğundaki adresi kontrol edin ve HTTPS kilit simgesinin aktif olduğundan emin olun.</li>
<li>Giriş bilgilerinizi kimseyle paylaşmayın.</li>
<li>İki faktörlü doğrulamayı (2FA) aktifleştirerek hesap güvenliğinizi artırın.</li>
<li>Halka açık Wi-Fi ağlarında giriş yapmaktan kaçının veya VPN kullanın.</li>
</ul>

<h2>Güncel Adres Nasıl Takip Edilir?</h2>

<p>Kaya Casino adres değişikliklerini kaçırmamak için <a href="/kayacasino-telegram" title="Kaya Casino Telegram">resmi Telegram kanalını</a> takip etmenizi öneriyoruz. Adres güncellemeleri anında bu kanaldan duyurulmaktadır.</p>

<div class="info-box">
<strong>Hatırlatma:</strong> Kaya Casino resmi temsilcileri hiçbir zaman şifrenizi veya ödeme bilgilerinizi mesaj yoluyla istemez. Şüpheli bağlantılara tıklamayın.
</div>

<h3>Kaya Casino giriş adresi neden değişiyor?</h3>
<p>Online platformlar çeşitli düzenlemeler nedeniyle alan adı değişikliği yapmak durumunda kalabilmektedir. Yeni adres yayınlandığında mevcut hesap bilgileriniz ve bakiyeniz aynen korunur.</p>

<h3>Giriş yaparken hata alıyorum, ne yapmalıyım?</h3>
<p>Önce internet bağlantınızı kontrol edin, ardından tarayıcı önbelleğini temizleyin. Sorun devam ederse DNS değiştirmeyi veya VPN kullanmayı deneyin.</p>
HTML
],

[
'slug' => 'kayacasino-bonus',
'title' => 'Kaya Casino Bonus Rehberi – Güncel Kampanyalar 2026',
'category_id' => $catMap['bonus'],
'meta_title' => 'Kaya Casino Bonus 2026 – Hoş Geldin ve Yatırım Bonusları',
'meta_description' => 'Kaya Casino bonus rehberi. Hoş geldin bonusu, yatırım bonusu, free spin ve VIP kampanyaları.',
'excerpt' => 'Kaya Casino güncel bonus kampanyaları, çevrim şartları ve VIP avantajlarının detaylı rehberi.',
'published_at' => '2026-03-06 10:00:00',
'featured_image' => '/storage/promotions/kayacasino/25yatirim-PROMOTIONS.jpg',
'content' => <<<'HTML'
<h2>Kaya Casino Güncel Bonus Kampanyaları</h2>

<p>Kaya Casino, yeni ve mevcut üyelerine yönelik çeşitli bonus fırsatları sunmaktadır. Platform, sektörde rekabetçi çevrim şartları ve yüksek bonus oranlarıyla dikkat çekmektedir.</p>

<h2>Aktif Bonus Türleri</h2>

<table>
<thead><tr><th>Bonus Türü</th><th>Oran</th><th>Maks. Tutar</th><th>Çevrim</th><th>Geçerli Oyunlar</th></tr></thead>
<tbody>
<tr><td>Hoş Geldin Bonusu</td><td>%100</td><td>3.000 TL</td><td>15x</td><td>Tüm slotlar</td></tr>
<tr><td>Yatırım Bonusu</td><td>%25</td><td>1.500 TL</td><td>10x</td><td>Slot + Canlı Casino</td></tr>
<tr><td>Casino Discount</td><td>%30</td><td>2.000 TL</td><td>1x</td><td>Canlı Casino</td></tr>
<tr><td>Slot Yatırım</td><td>%50</td><td>2.500 TL</td><td>12x</td><td>Slotlar</td></tr>
<tr><td>Haftalık Ekstra</td><td>%5</td><td>500 TL</td><td>5x</td><td>Tüm oyunlar</td></tr>
<tr><td>Kayıp İadesi</td><td>%15</td><td>1.000 TL</td><td>3x</td><td>Tüm oyunlar</td></tr>
</tbody>
</table>

<h2>Bonus Nasıl Alınır?</h2>

<ol>
<li><strong>Hesabınıza giriş yapın</strong> ve yatırım sayfasına gidin.</li>
<li><strong>Yatırım tutarınızı belirleyin:</strong> Minimum yatırım şartını karşılayın.</li>
<li><strong>Bonus talebinizi iletin:</strong> Canlı destek üzerinden veya bonus sayfasından aktifleştirin.</li>
<li><strong>Çevrim şartını tamamlayın:</strong> Belirtilen çevrim sayısı kadar bahis yapın.</li>
<li><strong>Kazancınızı çekin:</strong> Çevrim tamamlandıktan sonra çekim talebi oluşturun.</li>
</ol>

<h2>VIP Bonus Avantajları</h2>

<p>VIP üyeler standart bonuslara ek olarak kişiye özel kampanyalardan yararlanmaktadır:</p>

<ul>
<li><strong>Bronze:</strong> Standart bonuslara ek %5 bonus</li>
<li><strong>Silver:</strong> Haftalık kayıp iadesi + özel free spin</li>
<li><strong>Gold:</strong> Kişiye özel bonus oranları + VIP turnuva</li>
<li><strong>Platinum:</strong> Limitsiz bonus + anında çekim + VIP etkinlik</li>
</ul>

<div class="info-box">
<strong>Önemli:</strong> Bonus kurallarını almadan önce mutlaka okuyun. Detaylı bilgi için <a href="/kayacasino-bonuslari" title="Kaya Casino Bonusları">bonus sayfamızı</a> ziyaret edin.
</div>

<h3>Birden fazla bonus aynı anda kullanılabilir mi?</h3>
<p>Hayır, aktif bonus varken yeni bonus alınamaz. Mevcut bonusu tamamladıktan veya iptal ettikten sonra yeni bonus talep edebilirsiniz.</p>

<h3>Free spin kazançları nasıl çekilir?</h3>
<p>Free spin kazançları bonus bakiyesine eklenir ve belirlenen çevrim şartı tamamlandıktan sonra çekilebilir hale gelir.</p>
HTML
],

[
'slug' => 'kayacasino-slot',
'title' => 'Kaya Casino Slot Oyunları – En Popüler Slotlar 2026',
'category_id' => $catMap['oyun'],
'meta_title' => 'Kaya Casino Slot 2026 – En Çok Kazandıran Slot Oyunları',
'meta_description' => 'Kaya Casino slot oyunları rehberi. Gates of Olympus, Sweet Bonanza, Big Bass ve RTP bilgileri.',
'excerpt' => 'Kaya Casino platformundaki en popüler slot oyunları, RTP oranları ve kazanma stratejileri.',
'published_at' => '2026-03-06 11:00:00',
'featured_image' => '/storage/games/kayacasino/kaya_gates_of_olympus_daily_slot.jpg',
'content' => <<<'HTML'
<h2>Kaya Casino Slot Oyunları Rehberi</h2>

<p>Kaya Casino, Pragmatic Play, NetEnt, Microgaming ve Play'n GO gibi dünyaca ünlü sağlayıcıların 500'den fazla slot oyununu tek platformda sunmaktadır.</p>

<h2>En Popüler Slot Oyunları</h2>

<table>
<thead><tr><th>Oyun</th><th>Sağlayıcı</th><th>RTP</th><th>Volatilite</th><th>Maks. Kazanç</th></tr></thead>
<tbody>
<tr><td>Gates of Olympus</td><td>Pragmatic Play</td><td>%96.50</td><td>Yüksek</td><td>5.000x</td></tr>
<tr><td>Sweet Bonanza</td><td>Pragmatic Play</td><td>%96.51</td><td>Orta-Yüksek</td><td>21.175x</td></tr>
<tr><td>Big Bass Bonanza</td><td>Pragmatic Play</td><td>%96.71</td><td>Yüksek</td><td>2.100x</td></tr>
<tr><td>The Dog House</td><td>Pragmatic Play</td><td>%96.51</td><td>Yüksek</td><td>6.750x</td></tr>
<tr><td>Book of Dead</td><td>Play'n GO</td><td>%96.21</td><td>Yüksek</td><td>5.000x</td></tr>
<tr><td>Starburst</td><td>NetEnt</td><td>%96.09</td><td>Düşük</td><td>500x</td></tr>
<tr><td>Sugar Rush</td><td>Pragmatic Play</td><td>%96.50</td><td>Yüksek</td><td>5.000x</td></tr>
</tbody>
</table>

<h2>Slot Oyunu Türleri</h2>

<h3>Klasik Slotlar</h3>
<p>3 makaralı, basit mekanikli nostalji oyunları. Az sayıda ödeme çizgisi ve sade tasarımlarıyla hızlı oyun deneyimi sunar.</p>

<h3>Video Slotlar</h3>
<p>5 veya daha fazla makaralı, zengin bonus özellikleri ve animasyonlarla dolu modern oyunlar. Gates of Olympus ve Sweet Bonanza bu kategorinin en popüler örnekleridir.</p>

<h3>Megaways Slotlar</h3>
<p>Her spinde değişen sembol sayısıyla 117.649'e kadar kazanma yolu sunan yenilikçi mekanik. Büyük kazanç potansiyeli arayanlar için idealdir.</p>

<h3>Jackpot Slotlar</h3>
<p>Birikimli ödül havuzuyla devasa kazançlar sunan oyunlar. Her bahisten bir miktar jackpot havuzuna eklenir.</p>

<h2>Slot Oynama Stratejileri</h2>

<ol>
<li><strong>RTP oranını kontrol edin:</strong> %96 üzeri RTP'li oyunları tercih edin.</li>
<li><strong>Demo modda pratik yapın:</strong> Gerçek para yatırmadan önce oyunu tanıyın.</li>
<li><strong>Bütçe limiti belirleyin:</strong> Oturum başına harcama sınırı koyun.</li>
<li><strong>Bonus özelliklerini öğrenin:</strong> Ante Bet, Bonus Buy gibi özellikleri doğru kullanın.</li>
<li><strong>Volatiliteyi anlayın:</strong> Yüksek volatilite = nadir ama büyük kazanç.</li>
</ol>

<div class="info-box">
<strong>İpucu:</strong> <a href="/kayacasino-slot-oyunlari" title="Slot Oyunları">Slot oyunları sayfamızda</a> tüm oyunların detaylı incelemelerini bulabilirsiniz.
</div>

<h3>Hangi slot oyunu daha çok kazandırır?</h3>
<p>Uzun vadede yüksek RTP'li oyunlar daha fazla geri ödeme yapar. Büyük çarpan istiyorsanız Sweet Bonanza (21.175x) en yüksek potansiyele sahiptir.</p>

<h3>Bonus Buy özelliği avantajlı mı?</h3>
<p>Bonus Buy, bonus turunu direkt satın almanızı sağlar. Genellikle 100x bahis tutarına mal olur. Uzun vadede aynı RTP'yi sunar ama oyun süresini kısaltır.</p>
HTML
],

[
'slug' => 'kayacasino-yeni-adres',
'title' => 'Kaya Casino Yeni Adres – Mart 2026 Güncel Giriş Bilgileri',
'category_id' => $catMap['erisim'],
'meta_title' => 'Kaya Casino Yeni Adres Mart 2026 – Güncel Giriş Linki',
'meta_description' => 'Kaya Casino yeni giriş adresi Mart 2026. Güncel link, erişim yöntemleri ve adres takip rehberi.',
'excerpt' => 'Kaya Casino Mart 2026 güncel yeni giriş adresi ve kesintisiz erişim için takip yöntemleri.',
'published_at' => '2026-03-07 08:00:00',
'featured_image' => '/storage/promotions/kayacasino/ekstra-PROMOTIONS.jpg',
'content' => <<<'HTML'
<h2>Kaya Casino Yeni Giriş Adresi – Mart 2026</h2>

<p>Kaya Casino, Mart 2026 itibarıyla <strong>kayacasino.top</strong> adresi üzerinden hizmet vermektedir. Platform düzenli olarak alan adı güncellemesi yapmakta ve kullanıcılarını resmi kanallar üzerinden bilgilendirmektedir.</p>

<h2>Güncel Adres Bilgileri</h2>

<table>
<thead><tr><th>Bilgi</th><th>Detay</th></tr></thead>
<tbody>
<tr><td>Güncel Adres</td><td>kayacasino.top</td></tr>
<tr><td>SSL Güvenliği</td><td>256-bit SSL aktif</td></tr>
<tr><td>Son Güncelleme</td><td>Mart 2026</td></tr>
<tr><td>Durum</td><td>Aktif ve erişilebilir</td></tr>
</tbody>
</table>

<h2>Adres Değişikliği Neden Olur?</h2>

<p>Online platformlar çeşitli teknik ve yasal düzenlemeler nedeniyle alan adı değişikliği yapmak durumunda kalabilmektedir. Bu değişiklikler sırasında:</p>

<ul>
<li>Hesap bilgileriniz ve bakiyeniz tamamen korunur.</li>
<li>Oyun geçmişiniz ve bonus durumunuz değişmez.</li>
<li>Eski adres belirli süre yeni adrese yönlendirme yapar.</li>
<li>VIP seviyeniz ve tüm ayrıcalıklarınız aynen devam eder.</li>
</ul>

<h2>Yeni Adresi Takip Etme Yöntemleri</h2>

<ol>
<li><strong>Telegram kanalı:</strong> <a href="/kayacasino-telegram" title="Kaya Casino Telegram">Kaya Casino Telegram</a> kanalını takip edin. Adres değişiklikleri anında burada duyurulur.</li>
<li><strong>E-posta bildirimi:</strong> Kayıtlı e-posta adresinize güncel adres bilgisi gönderilmektedir.</li>
<li><strong>Sosyal medya:</strong> Resmi Instagram ve X hesaplarından duyuruları takip edin.</li>
<li><strong>Canlı destek:</strong> Mevcut adres üzerinden canlı destek ile iletişime geçerek yeni adresi öğrenebilirsiniz.</li>
</ol>

<h2>Yeni Adrese Geçiş Rehberi</h2>

<ol>
<li>Yeni adresi güvenilir kaynaktan aldığınızdan emin olun.</li>
<li>Tarayıcınıza yeni adresi yazın ve HTTPS kilit simgesini kontrol edin.</li>
<li>Mevcut kullanıcı adı ve şifrenizle giriş yapın.</li>
<li>Bakiyenizin ve bonus durumunuzun doğru olduğunu kontrol edin.</li>
<li>Tarayıcı yer imlerinizi yeni adresle güncelleyin.</li>
</ol>

<div class="info-box">
<strong>Güvenlik uyarısı:</strong> Yalnızca resmi kanallardan aldığınız adresleri kullanın. Detaylı giriş rehberi için <a href="/kayacasino-giris" title="Kaya Casino Giriş">giriş sayfamızı</a> ziyaret edin.
</div>

<h3>Yeni adreste tekrar kayıt olmam gerekir mi?</h3>
<p>Hayır. Yeni adres aynı altyapıyı kullanır. Mevcut hesap bilgilerinizle giriş yapabilirsiniz.</p>

<h3>Eski adres ne kadar süre çalışır?</h3>
<p>Eski adres genellikle birkaç hafta boyunca yeni adrese otomatik yönlendirme yapar. Bu süre sonunda eski adres devre dışı kalır.</p>
HTML
],

];

$inserted = 0;
$updated = 0;
foreach ($posts as $data) {
    $existing = Post::on('tenant')->where('slug', $data['slug'])->first();
    if ($existing) {
        $existing->update(array_merge($data, ['is_published' => true]));
        echo "Updated: {$data['slug']}\n";
        $updated++;
    } else {
        Post::on('tenant')->create(array_merge($data, ['is_published' => true]));
        echo "Created: {$data['slug']}\n";
        $inserted++;
    }
}
echo "\nDone. Inserted: $inserted, Updated: $updated, Total: " . Post::on('tenant')->count() . "\n";
