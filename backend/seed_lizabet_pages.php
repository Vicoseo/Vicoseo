<?php

/**
 * Lizabet — Homepage Seed for 4 Lizabet Sites
 * Creates 'anasayfa' page in each tenant database with themed content
 *
 * Usage:
 *   cd /var/www/multi-tenant-cms/backend
 *   php artisan tinker --execute="require 'seed_lizabet_pages.php';"
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

$sites = [
    [
        'domain' => 'girislizabet.site',
        'db'     => 'tenant_girislizabet_site',
        'theme'  => 'giris',
    ],
    [
        'domain' => 'lizabahis.site',
        'db'     => 'tenant_lizabahis_site',
        'theme'  => 'spor',
    ],
    [
        'domain' => 'lizabetcasino.com',
        'db'     => 'tenant_lizabetcasino_com',
        'theme'  => 'casino',
    ],
    [
        'domain' => 'lizabetgiris.site',
        'db'     => 'tenant_lizabetgiris_site',
        'theme'  => 'bonus',
    ],
];

$now = now();

function getHomepage(string $theme): array
{
    $pages = [
        'giris' => [
            'title'            => 'Lizabet Giriş - Güncel Giriş Adresi 2026',
            'meta_title'       => 'Lizabet Giriş 2026 - Güncel ve Güvenli Giriş Adresi',
            'meta_description' => 'Lizabet güncel giriş adresi 2026. Lizabet\'e hızlı, güvenli ve kesintisiz erişim sağlayın. Yeni giriş linki burada.',
            'content'          => '<article>
<h2>Lizabet Giriş - Güncel Adres ve Erişim Rehberi 2026</h2>

<p>Lizabet, Türkiye\'nin en güvenilir online bahis ve casino platformlarından biri olarak kullanıcılarına kesintisiz hizmet sunmaktadır. BTK kaynaklı erişim engellerine karşı güncel giriş adresi düzenli olarak güncellenmektedir. Bu sayfada Lizabet\'in en son giriş bilgilerine ulaşabilirsiniz.</p>

<p>Platform, gelişmiş güvenlik altyapısı ve SSL şifreleme teknolojisi ile kullanıcı verilerini koruma altına almaktadır. Güncel giriş adresi üzerinden hesabınıza güvenle erişim sağlayabilirsiniz.</p>

<h2>Lizabet Giriş Adresi Nasıl Bulunur?</h2>

<p>Lizabet giriş adresi zaman zaman değişiklik gösterebilmektedir. Bu durumda aşağıdaki yöntemlerle güncel adrese ulaşabilirsiniz:</p>
<ul>
<li><strong>Resmi Telegram Kanalı:</strong> Lizabet\'in Telegram kanalını takip ederek anlık adres güncellemelerinden haberdar olun</li>
<li><strong>Sosyal Medya Hesapları:</strong> Instagram ve X (Twitter) hesaplarından güncel linkler paylaşılmaktadır</li>
<li><strong>DNS Değişikliği:</strong> Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) kullanarak erişim sağlayabilirsiniz</li>
<li><strong>VPN Kullanımı:</strong> Güvenilir bir VPN uygulaması ile bağlantı kurabilirsiniz</li>
</ul>

<h2>Lizabet Mobil Giriş</h2>

<p>Lizabet mobil uyumlu tasarımı sayesinde akıllı telefonunuz veya tabletiniz üzerinden de kolayca giriş yapabilirsiniz. Herhangi bir uygulama indirmenize gerek kalmadan, mobil tarayıcınız üzerinden güncel adrese erişerek tüm özelliklere ulaşabilirsiniz. Android ve iOS cihazlarla tam uyumlu çalışmaktadır.</p>

<h2>Güvenli Giriş İpuçları</h2>

<p>Lizabet hesabınızın güvenliğini sağlamak için şu adımları takip edin:</p>
<ul>
<li>Güçlü ve benzersiz bir şifre belirleyin</li>
<li>İki faktörlü doğrulamayı (2FA) aktif edin</li>
<li>Yalnızca resmi giriş adreslerini kullanın</li>
<li>Şüpheli bağlantılara tıklamayın</li>
<li>Bilgilerinizi üçüncü taraflarla paylaşmayın</li>
</ul>

<h2>Lizabet Üyelik ve Kayıt</h2>

<p>Lizabet\'e üye olmak oldukça kolaydır. Güncel giriş adresine girdikten sonra "Kayıt Ol" butonuna tıklayarak birkaç dakika içinde hesabınızı oluşturabilirsiniz. Kayıt sırasında kişisel bilgilerinizi doğru ve eksiksiz girmeniz, ilerleyen aşamalarda sorun yaşamamanız açısından önemlidir.</p>

<h2>Sıkça Sorulan Sorular</h2>

<p><strong>Lizabet giriş adresi neden değişiyor?</strong><br>
BTK (Bilgi Teknolojileri ve İletişim Kurumu) tarafından uygulanan erişim engellemeleri nedeniyle platform zaman zaman yeni alan adına geçiş yapmaktadır. Bu durum, platformun hizmet kalitesini etkilemez.</p>

<p><strong>Lizabet\'e VPN olmadan nasıl girilir?</strong><br>
DNS ayarlarınızı Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) olarak değiştirerek VPN kullanmadan erişim sağlayabilirsiniz. Ayrıca güncel giriş adresini resmi sosyal medya kanallarından takip edebilirsiniz.</p>

<p><strong>Lizabet mobil girişte sorun yaşıyorum, ne yapmalıyım?</strong><br>
Tarayıcınızın önbelleğini temizleyin, farklı bir tarayıcı deneyin veya mobil verilerinizi kapatıp açın. Sorun devam ederse DNS ayarlarınızı değiştirmeyi deneyin.</p>

<p><strong>Lizabet hesabıma giriş yapamıyorum, şifremi unuttum ne yapmalıyım?</strong><br>
Giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayarak kayıtlı e-posta adresinize şifre sıfırlama linki gönderebilirsiniz.</p>
</article>',
        ],

        'spor' => [
            'title'            => 'Lizabet Bahis - Spor Bahisleri ve Canlı Bahis 2026',
            'meta_title'       => 'Lizabet Bahis 2026 - Spor Bahisleri, Canlı Bahis ve Yüksek Oranlar',
            'meta_description' => 'Lizabet spor bahisleri 2026. Canlı bahis, yüksek oranlar, kombine kuponlar ve 30\'dan fazla spor dalında bahis imkanı.',
            'content'          => '<article>
<h2>Lizabet Spor Bahisleri - Canlı Bahis ve Yüksek Oranlar</h2>

<p>Lizabet, spor bahisleri alanında Türkiye\'nin en kapsamlı platformlarından birini sunmaktadır. 30\'dan fazla spor dalında maç öncesi ve canlı bahis seçenekleriyle kullanıcılarına eşsiz bir deneyim yaşatmaktadır. Futboldan basketbola, tenisten voleybola kadar geniş bir yelpazede yüksek oranlarla bahis yapabilirsiniz.</p>

<p>Platform, anlık oran güncellemeleri ve canlı maç takibi özelliğiyle kullanıcılarına dinamik bir bahis ortamı sunmaktadır. İstatistik tabloları ve canlı skor entegrasyonu sayesinde bilinçli kararlar alarak kazanç potansiyelinizi artırabilirsiniz.</p>

<h2>Canlı Bahis Deneyimi</h2>

<p>Lizabet canlı bahis bölümü, maçlar devam ederken bahis yapmanıza olanak tanır. Anlık oran değişimleri, istatistik grafikleri ve canlı yayın desteği ile maçı takip ederken stratejik hamleler yapabilirsiniz. Futbol, basketbol, tenis ve daha birçok spor dalında canlı bahis seçenekleri mevcuttur.</p>

<h2>Kombine Bahis Avantajları</h2>

<p>Lizabet kombine bahis sistemi, birden fazla maçı tek kuponda birleştirerek yüksek kazanç elde etme fırsatı sunar. Kombine bonus oranları sayesinde kupon kazancınız katlanarak artar. Platform, özel kombine kampanyalarıyla kullanıcılarına ekstra avantajlar sağlamaktadır.</p>

<h2>Popüler Spor Dalları</h2>

<ul>
<li><strong>Futbol:</strong> Süper Lig, Premier Lig, La Liga, Bundesliga ve daha fazlası</li>
<li><strong>Basketbol:</strong> NBA, EuroLeague, BSL ve uluslararası ligler</li>
<li><strong>Tenis:</strong> Grand Slam turnuvaları, ATP ve WTA müsabakaları</li>
<li><strong>Voleybol:</strong> Sultanlar Ligi, CEV Champions League</li>
<li><strong>E-Spor:</strong> CS2, League of Legends, Dota 2, Valorant</li>
</ul>

<h2>Bahis Türleri</h2>

<p>Lizabet\'te maç sonucu, alt/üst, handikap, ilk yarı sonucu, karşılıklı gol, toplam köşe vuruşu gibi onlarca farklı bahis marketinde şansınızı deneyebilirsiniz. Özel bahis seçenekleri ile standart marketlerin ötesinde kazanç fırsatları yakalayabilirsiniz.</p>

<h2>Sıkça Sorulan Sorular</h2>

<p><strong>Lizabet\'te minimum bahis tutarı ne kadardır?</strong><br>
Minimum bahis tutarı spor dalına ve maç türüne göre değişkenlik gösterebilir. Genel olarak 1 TL gibi düşük tutarlardan başlayarak bahis yapabilirsiniz.</p>

<p><strong>Lizabet canlı bahiste maç yayını var mı?</strong><br>
Evet, Lizabet birçok maç için canlı yayın hizmeti sunmaktadır. Hesabınıza bakiye yükledikten sonra canlı yayınları ücretsiz izleyebilirsiniz.</p>

<p><strong>Kombine bahiste maksimum kaç maç eklenebilir?</strong><br>
Lizabet kombine kuponlarında genellikle 20\'ye kadar maç ekleyebilirsiniz. Kombine bonus oranları eklenen maç sayısına göre artış gösterir.</p>

<p><strong>E-Spor bahisleri Lizabet\'te mevcut mu?</strong><br>
Evet, Lizabet\'te CS2, League of Legends, Dota 2, Valorant ve daha birçok e-spor oyununda bahis yapabilirsiniz. Canlı e-spor bahisleri de mevcuttur.</p>
</article>',
        ],

        'casino' => [
            'title'            => 'Lizabet Casino - Slot Oyunları ve Canlı Casino 2026',
            'meta_title'       => 'Lizabet Casino 2026 - Slot, Canlı Rulet, Blackjack ve Poker',
            'meta_description' => 'Lizabet casino oyunları 2026. Yüzlerce slot, canlı rulet, blackjack, poker ve masa oyunları. Dünyaca ünlü oyun sağlayıcılar.',
            'content'          => '<article>
<h2>Lizabet Casino - Slot Oyunları ve Canlı Casino Deneyimi</h2>

<p>Lizabet casino bölümü, dünyaca ünlü oyun sağlayıcılarının en geniş portföyünü tek çatı altında sunan profesyonel bir platformdur. Pragmatic Play, Evolution Gaming, NetEnt, Microgaming ve daha birçok premium sağlayıcının oyunlarıyla eşsiz bir casino deneyimi yaşayabilirsiniz.</p>

<p>Yüzlerce slot makinesi, onlarca masa oyunu ve profesyonel krupiyeler eşliğinde canlı casino masaları Lizabet\'te sizi bekliyor. Her oyun türü için detaylı bilgi ve strateji rehberleri ile kazanma şansınızı artırabilirsiniz.</p>

<h2>Slot Oyunları</h2>

<p>Lizabet slot bölümünde Gates of Olympus, Sweet Bonanza, Book of Dead, Starlight Princess, Big Bass Bonanza ve daha yüzlerce popüler slot oyununu bulabilirsiniz. Her oyunun RTP oranı, volatilite seviyesi ve bonus özellikleri hakkında detaylı bilgiye erişebilirsiniz.</p>

<h2>Canlı Casino</h2>

<p>Lizabet canlı casino bölümü, HD kalitesinde yayınlanan profesyonel masalarda gerçek krupiyelerle oynama imkanı sunar. Lightning Roulette, Crazy Time, Mega Ball, Dream Catcher gibi popüler oyun şovlarının yanı sıra klasik rulet, blackjack ve baccarat masaları da mevcuttur.</p>

<h2>Oyun Sağlayıcılar</h2>

<ul>
<li><strong>Pragmatic Play:</strong> Gates of Olympus, Sweet Bonanza, Big Bass serisi</li>
<li><strong>Evolution Gaming:</strong> Lightning Roulette, Crazy Time, Monopoly Live</li>
<li><strong>NetEnt:</strong> Starburst, Gonzo\'s Quest, Dead or Alive</li>
<li><strong>Play\'n GO:</strong> Book of Dead, Reactoonz, Rich Wilde serisi</li>
<li><strong>Microgaming:</strong> Mega Moolah, Immortal Romance, Thunderstruck</li>
</ul>

<h2>Masa Oyunları</h2>

<p>Klasik masa oyunları arasında Avrupa Ruleti, Amerikan Ruleti, Blackjack, Baccarat ve çeşitli Poker varyasyonları yer almaktadır. Her masa oyunu için temel stratejiler ve kurallar platform içinde açıklanmaktadır.</p>

<h2>Sıkça Sorulan Sorular</h2>

<p><strong>Lizabet casino oyunları güvenilir mi?</strong><br>
Evet, Lizabet\'te yer alan tüm casino oyunları lisanslı ve RNG (Rastgele Sayı Üreteci) sertifikalı oyun sağlayıcılar tarafından sunulmaktadır. Oyun sonuçları tamamen rastgele ve adildir.</p>

<p><strong>Canlı casino oyunlarına minimum ne kadar bahisle katılabilirim?</strong><br>
Canlı casino masalarında minimum bahis tutarı oyun türüne ve masaya göre değişmektedir. Düşük limitli masalardan VIP masalara kadar geniş bir seçenek mevcuttur.</p>

<p><strong>Lizabet\'te hangi slot oyunları en popüler?</strong><br>
Gates of Olympus, Sweet Bonanza, Big Bass Bonanza ve Starlight Princess Lizabet kullanıcıları arasında en çok tercih edilen slot oyunlarıdır.</p>

<p><strong>Casino bonuslarını slot oyunlarında kullanabilir miyim?</strong><br>
Evet, Lizabet casino bonusları slot oyunlarında kullanılabilir. Ancak her bonus kampanyasının kendine özgü çevrim şartları bulunmaktadır.</p>
</article>',
        ],

        'bonus' => [
            'title'            => 'Lizabet Bonusları - Güncel Kampanyalar ve Promosyonlar 2026',
            'meta_title'       => 'Lizabet Bonusları 2026 - Hoş Geldin, Yatırım ve Kayıp Bonusları',
            'meta_description' => 'Lizabet güncel bonus kampanyaları 2026. Hoşgeldin bonusu, deneme bonusu, yatırım bonusu, kayıp bonusu ve özel promosyonlar.',
            'content'          => '<article>
<h2>Lizabet Bonusları - Güncel Kampanyalar ve Promosyonlar 2026</h2>

<p>Lizabet, yeni ve mevcut üyelerine sunduğu geniş bonus yelpazesi ile sektörde fark yaratmaktadır. Hoş geldin bonusundan deneme bonusuna, yatırım bonusundan kayıp bonusuna kadar pek çok avantajlı kampanya ile kazancınızı katlayabilirsiniz.</p>

<p>Her bonus kampanyasının kendine özgü çevrim şartları ve kullanım koşulları bulunmaktadır. Aşağıda Lizabet\'in güncel bonus kampanyalarını detaylı olarak inceleyebilirsiniz.</p>

<h2>Hoşgeldin Bonusu</h2>

<p>Lizabet\'e ilk kez üye olan kullanıcılar, platformun sunduğu cazip hoşgeldin bonusundan faydalanabilir. İlk yatırımınızda %50\'ye varan bonus ile oyun deneyiminize avantajlı bir başlangıç yapabilirsiniz. Hoşgeldin bonusu, spor bahisleri ve casino oyunlarında geçerlidir.</p>

<h2>Deneme Bonusu</h2>

<p>Lizabet deneme bonusu, yeni üyelerin platformu risk almadan keşfetmelerini sağlayan özel bir kampanyadır. 500 TL\'ye varan deneme bonusu ile yatırım yapmadan önce platformun sunduğu hizmetleri deneyebilirsiniz.</p>

<h2>Yatırım Bonusları</h2>

<p>Her yatırımınızda ekstra bakiye kazanmanızı sağlayan yatırım bonusları Lizabet\'in en popüler kampanyalarındandır. Kripto yatırım bonusu, çevrimsiz yatırım bonusu ve özel gün bonusları ile kazancınızı artırabilirsiniz.</p>

<h2>Kayıp Bonusları</h2>

<p>Lizabet kayıp bonusu programı, haftalık veya aylık kayıplarınızın belirli bir yüzdesini geri almanızı sağlar. Spor kayıp bonusu, casino kayıp bonusu ve kripto kayıp bonusu gibi farklı kategorilerde kayıp iade kampanyaları mevcuttur.</p>

<h2>Özel Kampanyalar</h2>

<ul>
<li><strong>Kombine Bonusu:</strong> Kombine kuponlarınıza eklediğiniz maç sayısına göre artan bonus oranları</li>
<li><strong>Freespin Bonusu:</strong> Belirli slot oyunlarında ücretsiz dönüş hakkı kazanma fırsatı</li>
<li><strong>Şans Çarkı:</strong> Günlük çevirme hakkıyla sürpriz ödüller kazanma şansı</li>
<li><strong>Sadakat Programı:</strong> Düzenli oyuncuları ödüllendiren özel VIP kampanyaları</li>
<li><strong>Ortaklık Programı:</strong> Arkadaş davet ederek kazanma fırsatı</li>
</ul>

<h2>Çevrim Şartları</h2>

<p>Lizabet bonus kampanyalarında çevrim şartları, bonus türüne göre değişkenlik göstermektedir. Çevrimsiz bonuslarda herhangi bir çevrim şartı bulunmazken, standart bonuslarda genellikle bonus miktarının belirli bir katı kadar bahis yapmanız gerekmektedir. Detaylı çevrim bilgilerine her kampanyanın kendi sayfasından ulaşabilirsiniz.</p>

<h2>Sıkça Sorulan Sorular</h2>

<p><strong>Lizabet deneme bonusu nasıl alınır?</strong><br>
Lizabet\'e üye olduktan sonra canlı destek hattından veya bonus talep bölümünden deneme bonusu talebinde bulunabilirsiniz. Bonus hesabınıza otomatik olarak tanımlanacaktır.</p>

<p><strong>Lizabet bonuslarında çevrim şartı var mı?</strong><br>
Bonus türüne göre çevrim şartları değişmektedir. Çevrimsiz bonuslarda çevrim şartı yoktur; diğer bonuslarda ise kampanya detaylarında belirtilen çevrim şartları geçerlidir.</p>

<p><strong>Birden fazla bonusu aynı anda kullanabilir miyim?</strong><br>
Lizabet\'te aynı anda birden fazla bonus aktif olamaz. Mevcut bonusunuzu tamamladıktan veya iptal ettikten sonra yeni bonus alabilirsiniz.</p>

<p><strong>Kripto yatırımlarda bonus oranı daha mı yüksek?</strong><br>
Evet, Lizabet kripto para ile yapılan yatırımlarda standart bonus oranlarına ek avantajlar sunmaktadır. Bitcoin, USDT ve diğer kripto para birimleriyle yatırım yaparak daha yüksek bonus oranlarından faydalanabilirsiniz.</p>
</article>',
        ],
    ];

    return $pages[$theme];
}

// Insert pages into each tenant DB
foreach ($sites as $siteInfo) {
    $domain = $siteInfo['domain'];
    $dbName = $siteInfo['db'];
    $theme  = $siteInfo['theme'];

    echo "=== {$domain} ({$dbName}) ===\n";

    // Configure tenant DB connection
    Config::set('database.connections.tenant.database', $dbName);
    DB::purge('tenant');
    DB::reconnect('tenant');

    $page = getHomepage($theme);

    // Check if anasayfa already exists
    $existing = DB::connection('tenant')
        ->table('pages')
        ->where('slug', 'anasayfa')
        ->first();

    if ($existing) {
        DB::connection('tenant')
            ->table('pages')
            ->where('slug', 'anasayfa')
            ->update([
                'title'            => $page['title'],
                'content'          => $page['content'],
                'meta_title'       => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'sort_order'       => 0,
                'is_published'     => true,
                'updated_at'       => $now,
            ]);
        echo "  Updated existing anasayfa page\n";
    } else {
        DB::connection('tenant')
            ->table('pages')
            ->insert([
                'slug'             => 'anasayfa',
                'title'            => $page['title'],
                'content'          => $page['content'],
                'meta_title'       => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'meta_keywords'    => null,
                'sort_order'       => 0,
                'is_published'     => true,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        echo "  Inserted new anasayfa page\n";
    }
}

echo "\nDone! All Lizabet homepage pages seeded.\n";
