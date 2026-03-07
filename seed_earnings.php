<?php
/**
 * Seed 12 new earnings entries per site for the "Kazanç" section.
 * Realistic slot/casino game win data with varying amounts and games.
 *
 * Usage: php seed_earnings.php
 */

$host = '127.0.0.1';
$user = 'cms_user';
$pass = 'Cms@Pr0d2026!xK9';
$mainDb = 'cms_main';

$pdo = new PDO("mysql:host=$host;dbname=$mainDb;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sites = $pdo->query("SELECT id, domain, name FROM sites WHERE is_active = 1 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
echo "Toplam " . count($sites) . " site bulundu.\n\n";

$now = date('Y-m-d H:i:s');

function getBrandName($domain) {
    $map = [
        'rise-bets.com' => 'Rise Bets', 'casival.me' => 'Casival',
        'ilkbahis.click' => 'İlkbahis', 'ilkbahis.link' => 'İlkbahis',
        'ilkbahisgiri.net' => 'İlkbahis Giriş', 'ilkbahisonline.com' => 'İlkbahis Online',
        'prensbet.me' => 'Prensbet', 'risebett.me' => 'Risebett',
        'rayzbet.net' => 'Rayzbet', 'prensbetgiris.online' => 'Prensbet Giriş',
        'prensbetgiris.site' => 'Prensbet Giriş',
        'prensbetgirisonline.one' => 'Prensbet Giriş Online',
        'prenssbet.com' => 'Prenssbet', 'prenssbet.net' => 'Prenssbet',
        'risebette.com' => 'Risebette', 'risebets.click' => 'Rise Bets',
        'pragmaticlive.click' => 'Pragmatic Live',
        'risespor.com' => 'Risespor',
    ];
    return $map[$domain] ?? ucfirst(explode('.', $domain)[0]);
}

/**
 * 12 new earnings templates - different games, amounts, bet sizes
 */
function getEarnings($brand) {
    return [
        [
            'title' => "Sweet Bonanza'da 125.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/sweet-bonanza.png',
            'content' => "<p>Sweet Bonanza slotunda şeker yağmuru turunda büyük çarpanlar art arda geldi. <strong>50 TL</strong> bahisle başlayan oyunda tumble özelliği sayesinde çarpanlar x32'ye kadar yükseldi ve toplam kazanç <strong>125.000 TL</strong> seviyesine ulaştı.</p>
<p>Pragmatic Play'in en popüler slotlarından Sweet Bonanza'da scatter sembolleri free spin turunu tetikledi. Ardından gelen şeker bombası çarpanları ile her düşüşte kazanç katlandı. Özellikle 3. ve 7. free spinde gelen x64 ve x128 çarpanlar toplam kazancı zirveye taşıdı.</p>
<p>{$brand} platformunda bu oyunda düşük bahisle ciddi kazanç elde edilebileceğini bir kez daha gördük. Sabırlı oyun ve doğru bahis seviyesi her zaman fark yaratıyor.</p>",
        ],
        [
            'title' => "Gates of Olympus ile 89.500 TL Kazanç!",
            'image' => '/storage/uploads/earnings/gates-of-olympus.png',
            'content' => "<p>Gates of Olympus slotunda Zeus'un şimşekleri tam isabet etti! <strong>25 TL</strong> bahisle oynanan free spin turunda çarpan değerleri x500'e kadar çıktı ve tek seferde <strong>89.500 TL</strong> kazanç elde edildi.</p>
<p>Pragmatic Play'in bu efsanevi oyununda tumble mekaniği ve rastgele çarpanlar (x2, x3, x5, x10, x25, x50, x100, x250, x500) birleşince inanılmaz sonuçlar ortaya çıkıyor. Bu elde özellikle son 3 free spinde üst üste gelen yüksek çarpanlar toplam kazancı uçurdu.</p>
<p>{$brand} üzerinden küçük bahisle başlayıp Olympus'un kapılarını aralayan bu el, stratejik oyunun önemini gösteriyor.</p>",
        ],
        [
            'title' => "Dog House Megaways'de 54.200 TL Kazanç!",
            'image' => '/storage/uploads/earnings/dog-house.png',
            'content' => "<p>Dog House Megaways slotunda yapışkan wild özelliği devreye girdi ve <strong>30 TL</strong> bahisle başlayan oyun <strong>54.200 TL</strong> kazançla sonuçlandı.</p>
<p>Pragmatic Play'in Megaways versiyonunda 117.649'a kadar kazanç yolu bulunuyor. Free spin turunda köpek kulübeleri wild olarak sabitlendi ve her spinde çarpan değerleri x2, x3 şeklinde artmaya devam etti. 5. ve 8. spinlerde tam ekran wild gelince kazanç patlaması yaşandı.</p>
<p>{$brand} platformunda Dog House serisinin ne kadar kazançlı olabileceğini bu el kanıtlıyor. Megaways mekaniği ile küçük bahislerle büyük potansiyel yakalanabiliyor.</p>",
        ],
        [
            'title' => "Starlight Princess ile 167.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/starlight-princess.png',
            'content' => "<p>Starlight Princess slotunda prenses temalı free spin turunda büyük sürpriz yaşandı. <strong>100 TL</strong> bahisle giren oyuncumuz, çarpan değerlerinin x256'ya kadar çıkmasıyla toplam <strong>167.000 TL</strong> kazanç elde etti.</p>
<p>Gates of Olympus'un kız kardeşi olan bu oyunda da tumble ve rastgele çarpan mekaniği bulunuyor. Yıldız çarpanları birleştiğinde inanılmaz sonuçlar çıkıyor. Bu elde 12 free spinin 8'inde çarpan geldi ve toplamda x1.670 çarpana ulaşıldı.</p>
<p>{$brand} üzerinde Starlight Princess'te sabırlı ve disiplinli oyunun karşılığını almak mümkün.</p>",
        ],
        [
            'title' => "Big Bass Bonanza'da 42.800 TL Kazanç!",
            'image' => '/storage/uploads/earnings/big-bass-bonanza.png',
            'content' => "<p>Big Bass Bonanza slotunda balıkçı free spin turunda dev bir balık yakalandı! <strong>20 TL</strong> bahisle başlayan oyunda balıkçı wild sembolü üst üste para ödüllü balıkları topladı ve kazanç <strong>42.800 TL</strong> seviyesine fırladı.</p>
<p>Pragmatic Play'in bu sevilen serisi, free spin turunda balıkçı sembolünün para değerli balıkları toplaması mekaniğine dayanıyor. Bu elde özellikle x10 ve x25 değerli balıkların art arda gelmesi, toplam kazancı yüksek seviyelere taşıdı.</p>
<p>{$brand} platformunda Big Bass serisi her zaman en çok oynanan oyunlar arasında. Düşük bahisle büyük kazanç potansiyeli sunuyor.</p>",
        ],
        [
            'title' => "Canlı Blackjack'te 31.500 TL Kazanç!",
            'image' => '/storage/uploads/earnings/live-blackjack.png',
            'content' => "<p>Canlı blackjack masasında temel strateji ile oynanan 45 dakikalık seansta toplam <strong>31.500 TL</strong> kazanç elde edildi. Oyuncu <strong>500 TL</strong> bankroll ile masaya oturdu ve disiplinli bir şekilde bahis miktarını yönetti.</p>
<p>Evolution Gaming'in sunduğu canlı blackjack masalarında temel strateji kartına sadık kalmak uzun vadede avantaj sağlıyor. Bu seansta 3 kez blackjack (21) geldi ve 2 kez de çift bahis (double down) başarılı oldu.</p>
<p>{$brand} canlı casino bölümünde profesyonel krupiyeler eşliğinde oynanan bu el, strateji ve disiplinin önemini bir kez daha gösteriyor.</p>",
        ],
        [
            'title' => "Canlı Rulet'te 28.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/live-roulette.png',
            'content' => "<p>Lightning Roulette masasında şans yıldırım gibi çarptı! <strong>200 TL</strong> bahisle oynanan turda Lightning çarpanı x500 geldi ve tek bir turda <strong>28.000 TL</strong> kazanç elde edildi.</p>
<p>Evolution Gaming'in Lightning Roulette'inde her turda 1 ile 5 arasında sayıya rastgele Lightning çarpanı (x50, x100, x200, x300, x400, x500) atanıyor. Straight Up bahsiniz bu sayılardan birine denk gelirse kazanç katlanıyor.</p>
<p>{$brand} canlı casino bölümünde Lightning Roulette en heyecan verici masalardan biri. Küçük bahislerle büyük kazanç fırsatı sunuyor.</p>",
        ],
        [
            'title' => "Madame Destiny Megaways'de 76.400 TL!",
            'image' => '/storage/uploads/earnings/madame-destiny.png',
            'content' => "<p>Madame Destiny Megaways slotunda falcı kadının kristal küresi parladı! <strong>40 TL</strong> bahisle free spin turuna girildikten sonra çarpanlar art arda yükseldi ve toplam kazanç <strong>76.400 TL</strong> oldu.</p>
<p>Pragmatic Play'in Megaways mekaniğini kullandığı bu oyunda 200.704 kazanç yolu bulunuyor. Free spin turunda artan çarpan özelliği sayesinde her kazançlı spin bir sonrakini daha değerli kılıyor.</p>
<p>{$brand} platformunda Madame Destiny serisi mistik teması ve yüksek kazanç potansiyeliyle her zaman popüler. Bu el, Megaways versiyonunun gücünü kanıtlıyor.</p>",
        ],
        [
            'title' => "Fruit Party'de 93.600 TL Kazanç!",
            'image' => '/storage/uploads/earnings/fruit-party.png',
            'content' => "<p>Fruit Party slotunda meyve festivali başladı! <strong>75 TL</strong> bahisle oynanan free spin turunda rastgele çarpanlar (x2, x4, x8, x16, x32, x64, x128) üst üste geldi ve toplam kazanç <strong>93.600 TL</strong> seviyesine ulaştı.</p>
<p>Pragmatic Play'in cluster pays mekaniğini kullanan Fruit Party'de aynı türden 5 veya daha fazla meyve yan yana geldiğinde kazanç oluşuyor. Tumble özelliği ile zincirleme kazançlar mümkün ve rastgele çarpanlar her şeyi değiştirebiliyor.</p>
<p>{$brand} üzerinde Fruit Party her zaman eğlenceli ve kazançlı bir seçenek. Bu elde x1.248 toplam çarpana ulaşıldı!</p>",
        ],
        [
            'title' => "Wild West Gold'da 58.900 TL Kazanç!",
            'image' => '/storage/uploads/earnings/wild-west-gold.png',
            'content' => "<p>Wild West Gold slotunda kovboy temalı free spin turunda yapışkan wild semboller art arda geldi. <strong>35 TL</strong> bahisle başlayan oyunda toplamda <strong>58.900 TL</strong> kazanç elde edildi.</p>
<p>Pragmatic Play'in bu klasik slot oyununda free spin turunda wild semboller sabitlenip çarpan kazanıyor (x2, x3, x5). Bu elde 5 tane yapışkan wild aynı anda ekranda kaldı ve çarpanları birbirleriyle çarpılınca toplam x1.683 çarpana ulaşıldı.</p>
<p>{$brand} platformunda Wild West Gold, yüksek volatilitesi ve büyük kazanç potansiyeliyle her zaman favoriler arasında yer alıyor.</p>",
        ],
        [
            'title' => "Sugar Rush ile 112.500 TL Kazanç!",
            'image' => '/storage/uploads/earnings/sugar-rush.png',
            'content' => "<p>Sugar Rush slotunda şeker patlaması yaşandı! <strong>60 TL</strong> bahisle giren oyuncumuz, aynı pozisyonda kazanç geldiğinde artan çarpan özelliği sayesinde toplam <strong>112.500 TL</strong> kazanç elde etti.</p>
<p>Pragmatic Play'in 7x7 grid üzerinde oynanan Sugar Rush'ında cluster pays ve pozisyonel çarpan mekaniği bulunuyor. Her kazanç aynı pozisyondaki çarpanı x2, x4, x8, x16... şeklinde artırıyor. Bu elde merkez bölgede x256 çarpana kadar çıkıldı.</p>
<p>{$brand} üzerinde Sugar Rush, tatlı teması ve devasa kazanç potansiyeliyle en popüler oyunlar arasında. Küçük bahisle büyük hayaller kuranlara ideal.</p>",
        ],
        [
            'title' => "Book of Dead'de 47.300 TL Kazanç!",
            'image' => '/storage/uploads/earnings/book-of-dead.png',
            'content' => "<p>Book of Dead slotunda Mısır'ın hazineleri ortaya çıktı! <strong>45 TL</strong> bahisle oynanan free spin turunda genişleyen özel sembol olarak en yüksek değerli Rich Wilde seçildi ve toplam kazanç <strong>47.300 TL</strong> oldu.</p>
<p>Play'n GO'nun efsanevi Book of Dead oyununda 10 free spin boyunca rastgele bir sembol genişleyen özel sembol olarak belirleniyor. Bu elde Rich Wilde seçildi ve 10 spinin 6'sında en az 3 Rich Wilde geldi, tam ekranı kaplayarak devasa kazançlar sağladı.</p>
<p>{$brand} platformunda Book of Dead, Mısır teması ve klasik mekaniğiyle her zaman en çok oynanan oyunlardan biri. Tek spin ile hayatınızı değiştirebilir!</p>",
        ],
    ];
}

// Get max sort_order per site
$maxSortOrders = [];
$rows = $pdo->query("SELECT site_id, MAX(sort_order) as max_so FROM site_earnings GROUP BY site_id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    $maxSortOrders[$r['site_id']] = (int)$r['max_so'];
}

// Insert earnings for all sites
$total = 0;
$stmt = $pdo->prepare("INSERT INTO site_earnings (site_id, image, video_url, title, content, sort_order, is_active, created_at, updated_at) VALUES (?, ?, NULL, ?, ?, ?, 1, ?, ?)");

foreach ($sites as $site) {
    $siteId = $site['id'];
    $brand = getBrandName($site['domain']);
    $earnings = getEarnings($brand);
    $startSort = ($maxSortOrders[$siteId] ?? 0) + 1;

    echo "=== {$site['domain']} ({$brand}) ===\n";

    foreach ($earnings as $i => $e) {
        $sortOrder = $startSort + $i;
        $stmt->execute([
            $siteId,
            $e['image'],
            $e['title'],
            $e['content'],
            $sortOrder,
            $now,
            $now,
        ]);
        echo "  [OK] {$e['title']}\n";
        $total++;
    }
    echo "\n";
}

echo "========================================\n";
echo "Toplam {$total} kazanç verisi eklendi.\n";
echo "Bitti!\n";
