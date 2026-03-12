<?php
/**
 * Seed earnings for 10 sites that don't have any yet.
 * Uses only images that physically exist on the server.
 *
 * Run: php artisan tinker --execute="require 'seed_earnings_missing_sites.php';"
 */

use App\Models\Site;
use App\Models\SiteEarning;

$brands = [
    'locabetbonus.me'   => 'Locabet',
    'locabetcasino.com' => 'Locabet',
    'locabetgiris.site' => 'Locabet',
    'locabeet.com'      => 'Locabet',
    'kayacasino.top'    => 'KayaCasino',
    'girislizabet.site' => 'Lizabet',
    'lizabahis.site'    => 'Lizabet',
    'lizabetcasino.com' => 'Lizabet',
    'lizabetgiris.site' => 'Lizabet',
    'prenssbet.org'     => 'Prensbet',
];

function getEarnings($brand) {
    return [
        [
            'title' => "Sweet Bonanza'da 125.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/sweet-bonanza.png',
            'content' => "<p>Sweet Bonanza slotunda şeker yağmuru turunda büyük çarpanlar art arda geldi. <strong>50 TL</strong> bahisle başlayan oyunda tumble özelliği sayesinde çarpanlar x32'ye kadar yükseldi ve toplam kazanç <strong>125.000 TL</strong> seviyesine ulaştı.</p>
<p>Pragmatic Play'in en popüler slotlarından Sweet Bonanza'da scatter sembolleri free spin turunu tetikledi. Ardından gelen şeker bombası çarpanları ile her düşüşte kazanç katlandı. Özellikle 3. ve 7. free spinde gelen x64 ve x128 çarpanlar toplam kazancı zirveye taşıdı.</p>
<p>{$brand} platformunda bu oyunda düşük bahisle ciddi kazanç elde edilebileceğini bir kez daha gördük.</p>",
        ],
        [
            'title' => "Gates of Olympus ile 89.500 TL Kazanç!",
            'image' => '/storage/uploads/earnings/gates-of-olympus.png',
            'content' => "<p>Gates of Olympus slotunda Zeus'un şimşekleri tam isabet etti! <strong>25 TL</strong> bahisle oynanan free spin turunda çarpan değerleri x500'e kadar çıktı ve tek seferde <strong>89.500 TL</strong> kazanç elde edildi.</p>
<p>Tumble mekaniği ve rastgele çarpanlar birleşince inanılmaz sonuçlar ortaya çıkıyor. Son 3 free spinde üst üste gelen yüksek çarpanlar toplam kazancı uçurdu.</p>
<p>{$brand} üzerinden küçük bahisle başlayıp Olympus'un kapılarını aralayan bu el, stratejik oyunun önemini gösteriyor.</p>",
        ],
        [
            'title' => "Dog House Megaways'de 54.200 TL Kazanç!",
            'image' => '/storage/uploads/earnings/dog-house.png',
            'content' => "<p>Dog House Megaways slotunda yapışkan wild özelliği devreye girdi ve <strong>30 TL</strong> bahisle başlayan oyun <strong>54.200 TL</strong> kazançla sonuçlandı.</p>
<p>Pragmatic Play'in Megaways versiyonunda 117.649'a kadar kazanç yolu bulunuyor. Free spin turunda köpek kulübeleri wild olarak sabitlendi ve her spinde çarpan değerleri artmaya devam etti.</p>
<p>{$brand} platformunda Dog House serisinin ne kadar kazançlı olabileceğini bu el kanıtlıyor.</p>",
        ],
        [
            'title' => "Starlight Princess ile 167.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/starlight-princess.png',
            'content' => "<p>Starlight Princess slotunda prenses temalı free spin turunda büyük sürpriz yaşandı. <strong>100 TL</strong> bahisle giren oyuncumuz, çarpan değerlerinin x256'ya kadar çıkmasıyla toplam <strong>167.000 TL</strong> kazanç elde etti.</p>
<p>Gates of Olympus'un kız kardeşi olan bu oyunda da tumble ve rastgele çarpan mekaniği bulunuyor. 12 free spinin 8'inde çarpan geldi ve toplamda x1.670 çarpana ulaşıldı.</p>
<p>{$brand} üzerinde Starlight Princess'te sabırlı ve disiplinli oyunun karşılığını almak mümkün.</p>",
        ],
        [
            'title' => "Bigger Bass Bonanza ile 70.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/bigger-bass.png',
            'content' => "<p>Bigger Bass Bonanza slotunda balıkçı free spin turunda dev bir balık yakalandı! <strong>40 TL</strong> bahisle başlayan oyunda balıkçı wild sembolü üst üste para ödüllü balıkları topladı ve kazanç <strong>70.000 TL</strong> seviyesine fırladı.</p>
<p>Pragmatic Play'in bu sevilen serisinde, free spin turunda balıkçı sembolünün para değerli balıkları toplaması mekaniğine dayanıyor. x10 ve x25 değerli balıkların art arda gelmesi toplam kazancı yükseltti.</p>
<p>{$brand} platformunda Bigger Bass serisi her zaman en çok oynanan oyunlar arasında yer alıyor.</p>",
        ],
        [
            'title' => "Sugar Rush ile 112.500 TL Kazanç!",
            'image' => '/storage/uploads/earnings/sugar-rush.png',
            'content' => "<p>Sugar Rush slotunda şeker patlaması yaşandı! <strong>60 TL</strong> bahisle giren oyuncumuz, aynı pozisyonda kazanç geldiğinde artan çarpan özelliği sayesinde toplam <strong>112.500 TL</strong> kazanç elde etti.</p>
<p>7x7 grid üzerinde oynanan Sugar Rush'ında cluster pays ve pozisyonel çarpan mekaniği bulunuyor. Her kazanç aynı pozisyondaki çarpanı x2, x4, x8... şeklinde artırıyor. Bu elde merkez bölgede x256 çarpana kadar çıkıldı.</p>
<p>{$brand} üzerinde Sugar Rush, tatlı teması ve devasa kazanç potansiyeliyle en popüler oyunlar arasında.</p>",
        ],
        [
            'title' => "Fruit Party'de 93.600 TL Kazanç!",
            'image' => '/storage/uploads/earnings/fruit-party.png',
            'content' => "<p>Fruit Party slotunda meyve festivali başladı! <strong>75 TL</strong> bahisle oynanan free spin turunda rastgele çarpanlar üst üste geldi ve toplam kazanç <strong>93.600 TL</strong> seviyesine ulaştı.</p>
<p>Cluster pays mekaniğini kullanan Fruit Party'de aynı türden 5 veya daha fazla meyve yan yana geldiğinde kazanç oluşuyor. Tumble özelliği ile zincirleme kazançlar mümkün.</p>
<p>{$brand} platformunda Fruit Party her zaman eğlenceli ve kazançlı bir seçenek olarak öne çıkıyor.</p>",
        ],
        [
            'title' => "Wild West Gold'da 58.900 TL Kazanç!",
            'image' => '/storage/uploads/earnings/wild-west-gold.png',
            'content' => "<p>Wild West Gold slotunda kovboy temalı free spin turunda yapışkan wild semboller art arda geldi. <strong>35 TL</strong> bahisle başlayan oyunda toplamda <strong>58.900 TL</strong> kazanç elde edildi.</p>
<p>Free spin turunda wild semboller sabitlenip çarpan kazanıyor (x2, x3, x5). 5 tane yapışkan wild aynı anda ekranda kaldı ve çarpanları birbirleriyle çarpılınca toplam x1.683 çarpana ulaşıldı.</p>
<p>{$brand} platformunda Wild West Gold, yüksek volatilitesi ve büyük kazanç potansiyeliyle favoriler arasında.</p>",
        ],
        [
            'title' => "Madame Destiny Megaways'de 76.400 TL!",
            'image' => '/storage/uploads/earnings/madame-destiny.png',
            'content' => "<p>Madame Destiny Megaways slotunda falcı kadının kristal küresi parladı! <strong>40 TL</strong> bahisle free spin turuna girildikten sonra çarpanlar art arda yükseldi ve toplam kazanç <strong>76.400 TL</strong> oldu.</p>
<p>200.704 kazanç yolu bulunan bu oyunda free spin turunda artan çarpan özelliği sayesinde her kazançlı spin bir sonrakini daha değerli kılıyor.</p>
<p>{$brand} platformunda Madame Destiny serisi mistik teması ve yüksek kazanç potansiyeliyle her zaman popüler.</p>",
        ],
        [
            'title' => "Buffalo King Megaways ile 85.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/buffalo-king.png',
            'content' => "<p>Buffalo King Megaways slotunda vahşi doğanın gücü hissedildi! <strong>50 TL</strong> bahisle başlayan oyunda Megaways mekaniği sayesinde kazanç yolları maksimuma çıktı ve toplam <strong>85.000 TL</strong> kazanç elde edildi.</p>
<p>Pragmatic Play'in bu güçlü yapımında free spin turunda çarpanlar katlanarak artıyor. Buffalo sembolleri tam ekranı kapladığında inanılmaz kazançlar ortaya çıkıyor.</p>
<p>{$brand} üzerinde Buffalo King, yüksek volatiliteli oyun arayanlar için ideal bir tercih.</p>",
        ],
        [
            'title' => "Power of Thor Megaways ile 98.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/power-of-thor.png',
            'content' => "<p>Power of Thor Megaways slotunda Thor'un çekici şimşekler saçtı! <strong>45 TL</strong> bahisle oynanan turda Megaways mekaniği ve cascading wins birleşti, toplam kazanç <strong>98.000 TL</strong> seviyesine ulaştı.</p>
<p>Pragmatic Play'in İskandinav mitolojisi temalı bu oyununda free spin turunda her kazançta çarpan bir artıyor. Thor'un çekici wild olarak geldiğinde ekstra çarpanlar devreye giriyor.</p>
<p>{$brand} platformunda Power of Thor, epik teması ve yüksek kazanç potansiyeliyle dikkat çekiyor.</p>",
        ],
        [
            'title' => "Gems Bonanza ile 47.100 TL Kazanç!",
            'image' => '/storage/uploads/earnings/gems-bonanza.png',
            'content' => "<p>Gems Bonanza slotunda değerli taşlar parladı! <strong>30 TL</strong> bahisle başlayan oyunda cluster mekaniği ve Gold Fever özelliği devreye girdi, toplam kazanç <strong>47.100 TL</strong> olarak gerçekleşti.</p>
<p>8x8 grid üzerinde oynanan bu Pragmatic Play yapımında aynı türden taşlar bir araya geldiğinde patlıyor ve yeni taşlar düşüyor. Gold Fever turunda tüm çarpanlar aktif kalıyor.</p>
<p>{$brand} üzerinde Gems Bonanza, renkli grafikleri ve cluster pays mekaniğiyle keyifli bir oyun deneyimi sunuyor.</p>",
        ],
        [
            'title' => "Hot Fiesta'da 33.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/hot-fiesta.png',
            'content' => "<p>Hot Fiesta slotunda Meksika festivali ateşli başladı! <strong>25 TL</strong> bahisle giren oyuncumuz, free spin turunda para dolu piñata'lar toplayarak <strong>33.000 TL</strong> kazanç elde etti.</p>
<p>Pragmatic Play'in eğlenceli bu oyununda free spin turunda Money Collect özelliği bulunuyor. Piñata sembolleri üzerindeki değerler toplanarak büyük kazançlar elde ediliyor.</p>
<p>{$brand} platformunda Hot Fiesta, renkli teması ve eğlenceli mekaniğiyle en sevilen oyunlardan biri.</p>",
        ],
        [
            'title' => "Great Rhino Megaways ile 65.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/great-rhino-megaways.png',
            'content' => "<p>Great Rhino Megaways slotunda Afrika savanasında büyük kazanç yakalandı! <strong>35 TL</strong> bahisle oynanan turda Megaways mekaniği ve süper free spinler birleşti, toplam <strong>65.000 TL</strong> kazanç sağlandı.</p>
<p>Bu Pragmatic Play oyununda 200.704'e kadar kazanç yolu var. Süper free spin turunda çarpanlar sınırsız olarak artıyor ve gerçekten büyük kazançlar mümkün oluyor.</p>
<p>{$brand} üzerinde Great Rhino Megaways, vahşi doğa teması ve devasa potansiyeliyle her zaman popüler.</p>",
        ],
        [
            'title' => "Hand of Midas ile 52.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/hand-of-midas.png',
            'content' => "<p>Hand of Midas slotunda Kral Midas'ın dokunuşu altına çevirdi! <strong>40 TL</strong> bahisle başlayan free spin turunda altın semboller ekranı kapladı ve toplam kazanç <strong>52.000 TL</strong> oldu.</p>
<p>Pragmatic Play'in bu mitolojik temalı slotunda free spin turunda rastgele semboller altın sembole dönüşüyor ve kazanç potansiyeli katlanıyor.</p>
<p>{$brand} platformunda Hand of Midas, altın teması ve yüksek çarpanlarıyla dikkat çeken oyunlar arasında.</p>",
        ],
        [
            'title' => "Floating Dragon ile 44.500 TL Kazanç!",
            'image' => '/storage/uploads/earnings/floating-dragon.png',
            'content' => "<p>Floating Dragon slotunda ejderha hazinelerini ortaya çıkardı! <strong>20 TL</strong> bahisle oynanan turda Hold & Spin özelliği tetiklendi ve toplam <strong>44.500 TL</strong> kazanç elde edildi.</p>
<p>Pragmatic Play'in Asya temalı bu oyununda Hold & Spin turunda ekrandaki para sembolleri sabitlenir ve yeni gelenlere yer açılır. Mega sembol geldiğinde kazanç patlaması yaşanıyor.</p>
<p>{$brand} üzerinde Floating Dragon, Hold & Spin mekaniğiyle heyecan verici bir oyun deneyimi sunuyor.</p>",
        ],
        [
            'title' => "Lucky Lightning ile 38.700 TL Kazanç!",
            'image' => '/storage/uploads/earnings/lucky-lightning.png',
            'content' => "<p>Lucky Lightning slotunda Zeus'un şansı yıldırım gibi çarptı! <strong>30 TL</strong> bahisle oynanan turda Lightning Respin özelliği devreye girdi ve <strong>38.700 TL</strong> kazanç elde edildi.</p>
<p>Bu Pragmatic Play yapımında Lightning Respin turunda ekrandaki değerler toplanıyor. Jackpot sembolleri Grand, Major, Minor ve Mini ödüller sunuyor.</p>
<p>{$brand} platformunda Lucky Lightning, jackpot fırsatlarıyla heyecan arayanlar için ideal bir tercih.</p>",
        ],
        [
            'title' => "Big Bass Splash ile 61.000 TL Kazanç!",
            'image' => '/storage/uploads/earnings/big-bass-splash.png',
            'content' => "<p>Big Bass Splash slotunda balıkçı macerası büyük kazançla sonuçlandı! <strong>35 TL</strong> bahisle başlayan oyunda free spin turunda balıkçı wild'ları para ödüllü balıkları topladı ve <strong>61.000 TL</strong> kazanç sağlandı.</p>
<p>Big Bass serisinin en yeni üyesi olan Splash versiyonunda ekstra free spin satın alma ve geliştirilmiş balıkçı mekaniği bulunuyor. x50 değerli altın balık geldiğinde kazançlar katlanıyor.</p>
<p>{$brand} üzerinde Big Bass Splash, serinin en kazançlı versiyonu olarak öne çıkıyor.</p>",
        ],
        [
            'title' => "Wild West Gold Megaways'de 72.500 TL!",
            'image' => '/storage/uploads/earnings/wild-west-gold-megaways.png',
            'content' => "<p>Wild West Gold Megaways slotunda kovboy dünyasının Megaways versiyonu büyük kazanç getirdi! <strong>45 TL</strong> bahisle oynanan turda yapışkan wildlar ve Megaways mekaniği birleşti, toplam <strong>72.500 TL</strong> kazanç elde edildi.</p>
<p>Orijinal Wild West Gold'un güçlendirilmiş versiyonunda 117.649 kazanç yolu ve artan çarpanlarla free spin turu çok daha kazançlı hale geliyor.</p>
<p>{$brand} platformunda Wild West Gold Megaways, klasik kovboy temasını modern Megaways mekaniğiyle birleştiriyor.</p>",
        ],
    ];
}

$targetDomains = array_keys($brands);
$total = 0;

foreach ($targetDomains as $domain) {
    $site = Site::where('domain', $domain)->first();
    if (!$site) {
        echo "[SKIP] {$domain} — site not found\n";
        continue;
    }

    // Check if already has earnings
    $existing = SiteEarning::where('site_id', $site->id)->count();
    if ($existing > 0) {
        echo "[SKIP] {$domain} — already has {$existing} earnings\n";
        continue;
    }

    $brand = $brands[$domain];
    $entries = getEarnings($brand);

    echo "[{$domain}] Adding " . count($entries) . " earnings (brand: {$brand})\n";

    foreach ($entries as $i => $e) {
        SiteEarning::create([
            'site_id'    => $site->id,
            'image'      => $e['image'],
            'video_url'  => null,
            'title'      => $e['title'],
            'content'    => $e['content'],
            'sort_order' => $i,
            'is_active'  => true,
        ]);
        $total++;
    }
}

echo "\n--- Total: {$total} earnings created ---\n";
