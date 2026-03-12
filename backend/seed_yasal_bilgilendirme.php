<?php
/**
 * Yasal Bilgilendirme sayfası — tüm siteler için
 * Her site benzersiz içerik alır (marka adı + varyasyon)
 *
 * Run: php artisan tinker --execute="require 'seed_yasal_bilgilendirme.php';"
 */

use App\Models\Site;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

// Brand name mapping (domain => display brand)
$brands = [
    'rise-bets.com'            => 'RiseBet',
    'casival.me'               => 'Casival',
    'ilkbahis.click'           => 'İlkBahis',
    'ilkbahis.link'            => 'İlkBahis',
    'ilkbahisgiri.net'         => 'İlkBahis',
    'ilkbahisonline.com'       => 'İlkBahis',
    'prensbet.me'              => 'Prensbet',
    'risebett.me'              => 'RiseBet',
    'rayzbet.net'              => 'RiseBet',
    'prensbetgiris.online'     => 'Prensbet',
    'prensbetgiris.site'       => 'Prensbet',
    'prensbetgirisonline.one'  => 'Prensbet',
    'prenssbet.com'            => 'Prensbet',
    'prenssbet.net'            => 'Prensbet',
    'prenssbet.org'            => 'Prensbet',
    'risebette.com'            => 'RiseBet',
    'risebets.click'           => 'RiseBet',
    'pragmaticlive.click'      => 'PragmaticPlay',
    'risespor.com'             => 'RiseBet',
    'locabetbonus.me'          => 'Locabet',
    'locabetcasino.com'        => 'Locabet',
    'locabetgiris.site'        => 'Locabet',
    'locabeet.com'             => 'Locabet',
    'kayacasino.top'           => 'KayaCasino',
    'girislizabet.site'        => 'Lizabet',
    'lizabahis.site'           => 'Lizabet',
    'lizabetcasino.com'        => 'Lizabet',
    'lizabetgiris.site'        => 'Lizabet',
];

// 6 content variations to avoid duplicate content
function getVariation($domain) {
    $hash = crc32($domain);
    return abs($hash) % 6;
}

function buildContent($brand, $variation) {
    $sections = [];

    // ── 1. Sorumlu Oyun ──
    $sorumlu = [
        [
            "<p>{$brand} olarak, sorumlu oyun oynamayı teşvik etmeyi en önemli önceliklerimiz arasında görüyoruz. Bahis ve casino oyunları eğlence amaçlı sunulmakta olup, bağımlılık yapıcı nitelik taşıyabilir. Kullanıcılarımızın oyun aktivitelerini kontrol altında tutmalarını ve bütçelerini aşmamalarını öneriyoruz.</p>",
            "<p>Oyun oynamanın günlük yaşamınızı, iş performansınızı veya kişisel ilişkilerinizi olumsuz etkilediğini fark ederseniz, derhal profesyonel destek almanızı tavsiye ederiz. {$brand} platformunda kendi kendinize limit belirleme ve hesap dondurma gibi araçlar mevcuttur.</p>",
            "<p>Sorumlu oyun ilkelerine bağlı kalmak, hem bireysel sağlığınız hem de çevrenizdeki insanlar için büyük önem taşır. Lütfen oyun oynamayı yalnızca bir eğlence aracı olarak değerlendirin.</p>",
        ],
        [
            "<p>{$brand}, kullanıcılarının bilinçli ve sorumlu bir şekilde oyun oynamasını desteklemektedir. Bahis ve casino aktiviteleri, doğası gereği risk içerir ve kontrol edilmediği takdirde bağımlılığa yol açabilir. Platform üzerinde harcama limitleri belirleyerek oyun deneyiminizi güvenli tutabilirsiniz.</p>",
            "<p>Oyun alışkanlıklarınızın kontrolünüzü aştığını hissediyorsanız, kendinize mola vermeniz ve gerekirse uzman desteği aramanız kritik bir adımdır. {$brand} olarak, kullanıcı refahını her zaman ön planda tutuyoruz.</p>",
            "<p>Bahis ve şans oyunlarını mali durumunuzu tehlikeye atmadan, eğlence amaçlı oynamanız gerektiğini hatırlatırız. Kaybetmeyi göze alabileceğiniz miktarları aşmamaya özen gösterin.</p>",
        ],
        [
            "<p>Sorumlu oyun, {$brand} platformunun temel değerlerinden biridir. Tüm kullanıcılarımıza, bahis ve casino oyunlarını yalnızca eğlence amacıyla oynamalarını ve kendi mali sınırlarını belirlemelerini tavsiye ediyoruz. Kontrolsüz oyun oynama alışkanlıkları ciddi sorunlara neden olabilir.</p>",
            "<p>{$brand} bünyesinde sunulan araçlarla hesabınıza yatırım limitleri koyabilir, belirli sürelerde oyun oynamayı durdurabilir veya hesabınızı geçici olarak askıya alabilirsiniz. Bu araçları aktif şekilde kullanmanızı öneriyoruz.</p>",
            "<p>Oyun bağımlılığı, bireyin günlük hayatını ve mali durumunu olumsuz etkileyen ciddi bir sorundur. Kendinizde ya da yakınlarınızda böyle bir durum tespit ederseniz, profesyonel yardım kuruluşlarına başvurmaktan çekinmeyin.</p>",
        ],
        [
            "<p>{$brand} platformu, sorumlu oyun politikalarına sıkı sıkıya bağlıdır. Bahis ve casino oyunları her ne kadar keyifli bir deneyim sunsa da kontrolsüz oynandığında bağımlılık riski taşımaktadır. Kullanıcılarımızın bilinçli tercihler yapmasını destekliyoruz.</p>",
            "<p>Harcamalarınızı takip etmek, oyun için ayırdığınız süreyi sınırlamak ve kaybetmeyi kaldırabileceğiniz miktarlarla oynamak sorumlu oyunun temel prensipleridir. {$brand}, bu konuda kullanıcılarına çeşitli kontrol mekanizmaları sunmaktadır.</p>",
            "<p>Eğer oyun oynamak sizin için artık bir eğlence olmaktan çıkıp stres kaynağına dönüşmüşse, lütfen vakit kaybetmeden profesyonel destek hizmetlerinden yararlanın.</p>",
        ],
        [
            "<p>{$brand} olarak, her kullanıcımızın sağlıklı oyun alışkanlıkları geliştirmesini amaçlıyoruz. Bahis ve casino oyunları eğlenceli bir aktivite olmakla birlikte, dikkatli yönetilmediğinde bağımlılık riski oluşturabilir.</p>",
            "<p>Platformumuzda mevcut olan öz-dışlama ve limit belirleme özellikleri, oyun alışkanlıklarınızı kontrol altında tutmanıza yardımcı olmak için tasarlanmıştır. {$brand}, sorumlu oyun konusundaki farkındalığı artırmayı görev edinmiştir.</p>",
            "<p>Oyun bağımlılığı belirtileri gösterdiğinizi düşünüyorsanız, profesyonel destek almak en doğru adımdır. Kendinize ve sevdiklerinize karşı sorumluluğunuzu unutmayın.</p>",
        ],
        [
            "<p>Sorumlu oyun anlayışı, {$brand} platformunun vazgeçilmez ilkesidir. Casino ve bahis oyunları eğlence amacıyla tasarlanmış olup, finansal bir kazanç yöntemi olarak değerlendirilmemelidir.</p>",
            "<p>{$brand}, kullanıcılarına oyun süreleri ve harcama limitleri konusunda tam kontrol imkânı sunmaktadır. Bu araçları düzenli olarak kullanarak oyun deneyiminizi güvenli ve keyifli tutabilirsiniz.</p>",
            "<p>Bahis alışkanlıklarınızın hayatınızı olumsuz etkilediğini hissediyorsanız, oyun oynamaya ara verin ve uzman bir kuruluştan destek talep edin. Sorumlu oyun, herkesin sorumluluğudur.</p>",
        ],
    ];

    // ── 2. Yaş Kısıtlaması ──
    $yas = [
        [
            "<p>{$brand} platformuna erişim ve platformdaki hizmetlerden yararlanma hakkı yalnızca 18 yaşını doldurmuş bireylere aittir. Yaş doğrulaması yapılmadan hesap oluşturulamaz ve bahis ya da casino oyunlarına katılım sağlanamaz.</p>",
            "<p>Reşit olmayan bireylerin bahis sitelerine erişimini engellemek hem yasal bir zorunluluk hem de etik bir sorumluluktur. {$brand}, kayıt sürecinde ve gerekli görülen durumlarda ek kimlik doğrulaması talep etme hakkını saklı tutar.</p>",
            "<p>Ebeveynler ve veliler, çocuklarının internet kullanımını denetlemeli ve bahis sitelerine erişimi engelleyen ebeveyn kontrol yazılımlarından faydalanmalıdır.</p>",
        ],
        [
            "<p>{$brand} hizmetlerini kullanabilmek için kullanıcıların en az 18 yaşında olması zorunludur. Bu kural, yasal düzenlemeler çerçevesinde uygulanmakta olup istisnası bulunmamaktadır.</p>",
            "<p>Kayıt işlemi sırasında beyan edilen yaş bilgisinin doğruluğu kullanıcının sorumluluğundadır. {$brand}, gerekli gördüğü durumlarda kimlik belgesi ile yaş doğrulaması yapma hakkına sahiptir.</p>",
            "<p>18 yaşından küçük bireylerin kumar ve bahis faaliyetlerine katılması kesinlikle yasaktır. Velilerin, çocuklarının bu tür platformlara erişimini engellemek adına gerekli tedbirleri alması önerilir.</p>",
        ],
        [
            "<p>Bahis ve casino oyunlarına katılım için asgari yaş sınırı 18'dir. {$brand}, bu kuralı titizlikle uygulamakta ve reşit olmayan kullanıcıların platforma erişimini önlemeye yönelik güvenlik önlemleri almaktadır.</p>",
            "<p>Hesap açma sürecinde kullanıcılar yaş bilgilerini doğru bir şekilde beyan etmekle yükümlüdür. Yanıltıcı bilgi veren kullanıcıların hesapları kalıcı olarak kapatılabilir.</p>",
            "<p>Reşit olmayan bireylerin korunması {$brand} için öncelikli bir konudur. Ailelerin, çocuklarının çevrimiçi aktivitelerini izlemelerini ve uygun filtreleme yazılımlarını kullanmalarını öneririz.</p>",
        ],
        [
            "<p>{$brand} platformunda işlem yapabilmek için 18 yaş ve üzeri olma şartı aranmaktadır. Bu kısıtlama, uluslararası düzenlemeler ve sorumlu oyun ilkeleri doğrultusunda uygulanmaktadır.</p>",
            "<p>Yaş doğrulaması, hesap güvenliğinin ve yasal uyumluluğun sağlanması açısından kritik bir adımdır. {$brand}, kimlik doğrulama süreçlerini en üst düzeyde yürütmektedir.</p>",
            "<p>Küçüklerin bahis ve şans oyunlarına erişimini engellemek toplumsal bir sorumluluktur. Ebeveynlerin bu konuda aktif rol üstlenmesi büyük önem taşımaktadır.</p>",
        ],
        [
            "<p>{$brand} bünyesinde sunulan tüm bahis ve casino hizmetlerinden faydalanabilmek için kullanıcının 18 yaşını tamamlamış olması gerekmektedir. Bu şart, tüm kullanıcılar için geçerlidir.</p>",
            "<p>Platform, kayıt aşamasında ve sonrasında yaş doğrulaması amacıyla resmi kimlik belgesi talep edebilir. Yanlış beyanda bulunan kullanıcıların hesapları askıya alınır. {$brand}, reşit olmayan bireylerin korunmasını en yüksek öncelik olarak kabul eder.</p>",
            "<p>Anne-babalar ve yasal veliler, çocuklarını çevrimiçi kumar sitelerinden korumak adına ebeveyn denetim araçlarını aktif olarak kullanmalıdır.</p>",
        ],
        [
            "<p>18 yaşın altındaki bireyler, {$brand} platformundaki hiçbir hizmetten yararlanamazlar. Bu kural hem yasal düzenlemelere hem de sorumlu oyun politikalarına uygunluk amacıyla uygulanmaktadır.</p>",
            "<p>Kullanıcılar, kayıt esnasında doğum tarihlerini doğru olarak girmekle yükümlüdür. {$brand}, şüpheli durumlarda ek doğrulama prosedürleri uygulayabilir ve gerektiğinde hesap erişimini kısıtlayabilir.</p>",
            "<p>Çocukların ve gençlerin çevrimiçi bahis platformlarından uzak tutulması konusunda ailelere önemli sorumluluklar düşmektedir. Bu amaçla içerik filtreleme araçlarının kullanılmasını tavsiye ediyoruz.</p>",
        ],
    ];

    // ── 3. Yasal Uygunluk ──
    $yasal = [
        [
            "<p>Bahis ve casino oyunlarının yasallığı, kullanıcının bulunduğu ülke ve yargı bölgesine göre farklılık göstermektedir. {$brand} platformunu kullanmadan önce, bulunduğunuz bölgedeki yasal düzenlemeleri incelemeniz ve çevrimiçi bahis faaliyetlerinin yasal olup olmadığını doğrulamanız gerekmektedir.</p>",
            "<p>{$brand}, herhangi bir ülke veya bölgede yasa dışı faaliyetleri teşvik etmemekte ve kullanıcılarını yerel yasalara uymaya davet etmektedir. Platformun sunduğu hizmetlerin yasal statüsü bölgeden bölgeye değişebilir.</p>",
            "<p>Kullanıcılar, kendi yargı bölgelerindeki mevzuata uygunluğu sağlamakla yükümlüdür. Yasal sorumluluğun tamamen kullanıcıya ait olduğunu hatırlatırız.</p>",
        ],
        [
            "<p>Çevrimiçi bahis ve şans oyunlarına ilişkin yasal düzenlemeler, ülkeden ülkeye önemli farklılıklar göstermektedir. {$brand}, kullanıcılarından platformu kullanmadan önce kendi ülkelerindeki ilgili mevzuatı kontrol etmelerini talep etmektedir.</p>",
            "<p>Bu platformda sunulan hizmetler, yalnızca çevrimiçi bahis ve casino oyunlarının yasal olduğu bölgelerdeki kullanıcılara yöneliktir. {$brand}, yasadışı kullanımdan doğabilecek sonuçlardan sorumlu tutulamaz.</p>",
            "<p>Her kullanıcı, kendi ülkesinin ya da ikamet ettiği bölgenin kanunlarına uygun hareket etmekle yükümlüdür. Hukuki belirsizlik durumunda bir avukattan danışmanlık almanızı öneririz.</p>",
        ],
        [
            "<p>Kumar ve bahis faaliyetlerinin hukuki durumu her ülkede farklıdır. {$brand} platformuna kayıt olmadan önce, çevrimiçi bahis ve casino oyunlarının bulunduğunuz ülkede yasal olup olmadığını araştırmanız önemle tavsiye edilir.</p>",
            "<p>{$brand}, hiçbir koşulda kullanıcılarını yasa dışı faaliyetlere yönlendirmemekte ve teşvik etmemektedir. Platformumuz, yasal çerçevede hizmet sunmayı amaçlamaktadır.</p>",
            "<p>Yasal uygunluk konusundaki sorumluluk kullanıcıya aittir. İkamet ettiğiniz ülkenin veya bölgenin mevzuatına aykırı davranışlardan doğacak tüm hukuki sonuçlar kullanıcının sorumluluğundadır.</p>",
        ],
        [
            "<p>{$brand} platformunu kullanarak bahis veya casino oyunlarına katılmadan önce, bu faaliyetlerin ikamet ettiğiniz yargı bölgesinde yasal olup olmadığını doğrulamanız gerekmektedir. Çevrimiçi kumar düzenlemeleri uluslararası alanda büyük farklılıklar göstermektedir.</p>",
            "<p>Platform, yalnızca bilgilendirme ve eğlence amaçlı hizmet vermektedir. {$brand}, kullanıcıların yasal düzenlemelere uygun hareket etmesini bekler ve yasadışı kullanımı kesinlikle onaylamaz.</p>",
            "<p>Hukuki konularda tereddüt yaşamanız halinde, yerel makamlardan veya hukuk danışmanlarından bilgi almanızı tavsiye ederiz.</p>",
        ],
        [
            "<p>Bahis ve şans oyunlarının hukuki çerçevesi, her ülkenin kendi mevzuatı tarafından belirlenmektedir. {$brand}, kullanıcılarından bu platforma erişmeden önce yerel yasaları kontrol etmelerini rica etmektedir.</p>",
            "<p>{$brand} hizmetleri, çevrimiçi bahis ve casino faaliyetlerinin serbest olduğu bölgelere yöneliktir. Yasadışı kullanımdan kaynaklanan durumlardan platform sorumlu tutulamaz.</p>",
            "<p>Kullanıcılar, platforma kayıt olarak yerel yasal düzenlemelere uyacaklarını taahhüt etmiş sayılır. Herhangi bir hukuki sorumluluk kullanıcının kendisine aittir.</p>",
        ],
        [
            "<p>Çevrimiçi kumar ve bahis hizmetlerinin yasallığı, kullanıcının bulunduğu coğrafi konuma bağlı olarak değişiklik göstermektedir. {$brand} platformunu kullanmadan önce, kendi bölgenizdeki ilgili yasal düzenlemeleri araştırmanız zorunludur.</p>",
            "<p>Bu platform, herhangi bir yargı bölgesinde yasa dışı faaliyetleri desteklemek veya teşvik etmek amacı taşımamaktadır. {$brand}, yasal çerçeveye uygunluğu esas alarak hizmet vermektedir.</p>",
            "<p>Kullanıcıların, hizmetlerimizi kullanırken kendi ülke ve bölgelerinin yasal gerekliliklerini karşıladığından emin olmaları beklenmektedir.</p>",
        ],
    ];

    // ── 4. Risk Uyarısı ──
    $risk = [
        [
            "<p>Bahis ve casino oyunları doğası gereği finansal risk içermektedir. {$brand} platformunda gerçekleştirilen tüm bahis ve oyun işlemlerinde kazanç garantisi bulunmamaktadır. Kullanıcılar, kaybetmeyi göze alabilecekleri tutarlarla oynamalıdır.</p>",
            "<p>Geçmiş performans veya istatistikler, gelecekteki sonuçlar hakkında garanti oluşturmaz. Her bahis ve oyun bağımsız bir olaydır ve sonuçları önceden tahmin edilemez.</p>",
            "<p>{$brand}, kullanıcılarını bilinçli kararlar almaya teşvik eder. Oyun bütçenizi önceden belirleyin ve bu bütçeyi aşmamaya özen gösterin.</p>",
        ],
        [
            "<p>{$brand} platformunda sunulan bahis ve casino hizmetleri finansal risk taşımaktadır. Hiçbir strateji veya yöntem, kazanmayı garantileyemez. Kullanıcıların bu gerçeği kabul ederek hareket etmeleri büyük önem taşır.</p>",
            "<p>Kaybetme olasılığı her zaman mevcuttur ve oyuncuların sadece kaybetmeyi karşılayabilecekleri miktarlarla işlem yapmaları gerekmektedir. Borç alarak veya temel ihtiyaçlarınıza ayrılan parayı kullanarak bahis oynamak kesinlikle önerilmez.</p>",
            "<p>Finansal durumunuzu riske atmadan oyun oynamak için bütçe planlaması yapmanızı ve limitlerinizie sadık kalmanızı {$brand} olarak öneriyoruz.</p>",
        ],
        [
            "<p>Casino ve bahis oyunlarında her zaman maddi kayıp riski bulunmaktadır. {$brand}, kullanıcılarını bu konuda bilgilendirmeyi ve bilinçli oyun oynamayı teşvik etmeyi görev edinmiştir.</p>",
            "<p>Oyun sonuçları şansa dayalıdır ve herhangi bir kazanç garantisi söz konusu değildir. Geçmişteki kazançlar, gelecekteki sonuçların göstergesi olarak değerlendirilemez.</p>",
            "<p>Mali durumunuzu tehlikeye atacak miktarlarla işlem yapmaktan kaçının. {$brand}, yalnızca bütçeniz dahilinde ve eğlence amaçlı oyun oynamanızı tavsiye etmektedir.</p>",
        ],
        [
            "<p>{$brand} üzerinde gerçekleştirilen bahis ve casino işlemleri finansal kayıplara yol açabilir. Tüm kullanıcılar, oyun oynamanın maddi risk içerdiğini kabul etmiş sayılır.</p>",
            "<p>Bahis ve casino oyunlarında kesin kazanç sağlayan bir yöntem bulunmamaktadır. Sonuçlar rastgele belirlendiğinden, her işlemde kayıp yaşama olasılığı vardır.</p>",
            "<p>Finansal güvenliğinizi korumak adına, oyun için ayırdığınız bütçeyi önceden belirleyin ve asla bu sınırı aşmayın. {$brand}, sorumlu oyun anlayışıyla kullanıcılarını desteklemektedir.</p>",
        ],
        [
            "<p>Bahis ve şans oyunları, kazanç fırsatı sunmakla birlikte önemli finansal riskler de barındırmaktadır. {$brand} kullanıcıları, bu risklerin farkında olmalı ve buna göre hareket etmelidir.</p>",
            "<p>Hiçbir bahis stratejisi ya da sistem %100 kazanç garantisi veremez. Her oyun ve bahis sonucu bağımsızdır ve önceki sonuçlardan etkilenmez.</p>",
            "<p>{$brand} olarak, kullanıcılarımızın yalnızca harcanabilir gelirlerinden ayırdıkları bütçeyle oyun oynamalarını ve mali yükümlülüklerini göz ardı etmemelerini öneriyoruz.</p>",
        ],
        [
            "<p>{$brand} platformundaki tüm bahis ve casino aktiviteleri finansal risk içerir. Kullanıcılar, olası kayıpları karşılayabilecek mali güce sahip olduklarından emin olmalıdır.</p>",
            "<p>Şans oyunlarında sonuçlar tamamen rastlantısaldır. Kazanma veya kaybetme olasılıkları matematiksel olarak belirlenir ve hiçbir dış faktör bu olasılıkları garanti altına alamaz.</p>",
            "<p>Sorumlu bir oyuncu olarak bütçenizi kontrol altında tutun ve oyun oynamayı bir gelir kaynağı olarak görmeyin. {$brand}, her zaman bilinçli oyun oynamayı destekler.</p>",
        ],
    ];

    // ── 5. Bağımlılık Desteği ──
    $bagimlilik = [
        [
            "<p>Kumar bağımlılığı, bireylerin yaşam kalitesini ciddi şekilde etkileyen bir durumdur. {$brand}, kullanıcılarını bu konuda bilinçlendirmeyi ve ihtiyaç duyanları destek kaynaklarına yönlendirmeyi amaçlamaktadır.</p>",
            "<p>Eğer bahis veya casino oyunları üzerinde kontrolünüzü kaybettiğinizi düşünüyorsanız, uluslararası destek kuruluşlarına başvurabilirsiniz. Gamblers Anonymous ve benzeri organizasyonlar, bağımlılıkla mücadele eden bireylere yardım sunmaktadır.</p>",
            "<p>{$brand}, kullanıcılarına hesap dondurma ve öz-dışlama seçenekleri sunarak bağımlılık riskini azaltmayı hedeflemektedir. Bu araçları kullanmaktan çekinmeyin.</p>",
        ],
        [
            "<p>Kumar bağımlılığı, tedavi edilmediği takdirde mali, sosyal ve psikolojik sorunlara yol açabilen ciddi bir sağlık problemidir. {$brand}, kullanıcılarının bu riskten korunması için gerekli tedbirleri almaktadır.</p>",
            "<p>Oyun alışkanlıklarınızın kontrolden çıktığını hissediyorsanız, profesyonel yardım almak en doğru adımdır. Yerel sağlık kuruluşları ve bağımlılık danışma hatları bu konuda size rehberlik edebilir.</p>",
            "<p>{$brand} platformu, sorumlu oyun politikaları kapsamında kullanıcılarına limit belirleme, mola verme ve hesap kapatma gibi araçlar sunmaktadır.</p>",
        ],
        [
            "<p>Bahis bağımlılığı, bireyin hayatının birçok alanını olumsuz etkileyebilecek bir durumdur. {$brand} olarak, kullanıcılarımızın sağlıklı oyun alışkanlıkları geliştirmelerine katkıda bulunmayı hedefliyoruz.</p>",
            "<p>Kendinizde veya çevrenizde kumar bağımlılığı belirtileri fark ettiğinizde, vakit kaybetmeden profesyonel destek kaynaklarına ulaşmanız önem taşır. Uluslararası platformlarda GamCare ve Gamblers Anonymous gibi kuruluşlar destek vermektedir.</p>",
            "<p>{$brand}, kullanıcılarının ihtiyaç duyması halinde hesaplarını geçici veya kalıcı olarak kapatma imkânı sunmaktadır. Bağımlılık riski hissettiğinizde bu seçenekleri değerlendirmenizi öneririz.</p>",
        ],
        [
            "<p>Kumar bağımlılığı, kişinin finansal durumunu, ilişkilerini ve ruh sağlığını derinden etkileyebilir. {$brand}, bu konudaki farkındalığı artırmayı önemli bir sorumluluk olarak görmektedir.</p>",
            "<p>Bağımlılık belirtileri arasında kontrolsüz harcama, oyun oynama dürtüsünü bastıramama ve kayıpları telafi etmek için daha fazla bahis yapma gibi davranışlar yer alır. Bu belirtileri fark ettiğinizde profesyonel yardım almanızı öneriyoruz.</p>",
            "<p>{$brand} platformunda sunulan öz-değerlendirme araçları ve destek kaynakları, bağımlılık riskini erken aşamada tespit etmenize yardımcı olabilir.</p>",
        ],
        [
            "<p>Oyun bağımlılığı ile mücadele, {$brand} için önemli bir konudur. Kullanıcılarımızın sağlıklı bir oyun deneyimi yaşamasını sağlamak amacıyla çeşitli önleyici tedbirler uygulamaktayız.</p>",
            "<p>Kumar bağımlılığı belirtileri gösteriyorsanız, profesyonel destek hizmetlerinden yararlanmanızı şiddetle tavsiye ederiz. Birçok ülkede ücretsiz danışma hatları ve destek grupları mevcuttur.</p>",
            "<p>{$brand} olarak, kullanıcılarımızın ihtiyaç duyması halinde hesap kısıtlama ve oyundan uzaklaşma araçlarını kolayca kullanabilmelerini sağlıyoruz.</p>",
        ],
        [
            "<p>{$brand}, kumar bağımlılığının ciddi bir sorun olduğunun bilinciyle hareket etmektedir. Platformumuz, kullanıcıların kontrollü ve sağlıklı bir oyun deneyimi yaşaması için tasarlanmıştır.</p>",
            "<p>Oyun oynama alışkanlıklarınız günlük yaşamınızı, mali durumunuzu veya ilişkilerinizi olumsuz etkiliyorsa, derhal profesyonel destek almanız gerekmektedir. GamCare, Gamblers Anonymous ve yerel sağlık birimleri bu konuda yardımcı olabilir.</p>",
            "<p>Hesabınızda kendi kendinize sınırlamalar koyabilir, belirli süreler için oyun erişiminizi durdurabilirsiniz. {$brand}, her kullanıcının refahını öncelikli olarak değerlendirmektedir.</p>",
        ],
    ];

    // ── 6. Gizlilik ve Veri Koruması ──
    $gizlilik = [
        [
            "<p>{$brand}, kullanıcılarının kişisel verilerinin korunmasını en yüksek öncelik olarak kabul etmektedir. Toplanan tüm veriler, güncel güvenlik standartları ve şifreleme teknolojileri ile korunmaktadır.</p>",
            "<p>Kişisel bilgileriniz, yalnızca hizmet sunumu ve yasal yükümlülüklerin yerine getirilmesi amacıyla işlenmektedir. {$brand}, kullanıcı verilerini üçüncü taraflarla izinsiz paylaşmamaktadır.</p>",
            "<p>Veri gizliliği politikamız hakkında detaylı bilgi almak için gizlilik politikası sayfamızı incelemenizi tavsiye ederiz.</p>",
        ],
        [
            "<p>Kullanıcı verilerinin güvenliği, {$brand} için vazgeçilmez bir ilkedir. Platform üzerinde paylaştığınız tüm bilgiler, endüstri standardı güvenlik protokolleri ile koruma altına alınmaktadır.</p>",
            "<p>{$brand}, kişisel verilerinizi yalnızca belirtilen amaçlar doğrultusunda kullanmakta ve bu verileri yetkisiz erişimlere karşı korumaktadır. Veri işleme süreçlerimiz şeffaflık ilkesiyle yürütülmektedir.</p>",
            "<p>Gizlilik haklarınız ve verilerinizin nasıl korunduğu hakkında detaylı bilgi için ilgili gizlilik politikamıza göz atabilirsiniz.</p>",
        ],
        [
            "<p>{$brand} platformu, kullanıcı gizliliğine büyük önem vermektedir. Kişisel veriler, SSL şifreleme ve güvenlik duvarları gibi ileri teknolojilerle korunmaktadır.</p>",
            "<p>Topladığımız veriler, hizmet kalitesinin iyileştirilmesi ve yasal gerekliliklerin karşılanması amacıyla sınırlı olarak işlenmektedir. {$brand}, veri minimizasyonu ilkesine bağlı kalarak yalnızca gerekli bilgileri talep etmektedir.</p>",
            "<p>Kullanıcılar, kişisel verilerine erişim, düzeltme ve silme haklarına sahiptir. Bu haklarınızı kullanmak için müşteri destek ekibimizle iletişime geçebilirsiniz.</p>",
        ],
        [
            "<p>Kişisel verilerin korunması, {$brand} için temel bir sorumluluktur. Platformumuz, kullanıcı bilgilerini en yüksek güvenlik standartlarında saklamaktadır.</p>",
            "<p>{$brand}, kullanıcı verilerini açık rıza olmaksızın üçüncü şahıslarla paylaşmaz. Veri işleme faaliyetleri, yürürlükteki veri koruma mevzuatına uygun olarak gerçekleştirilmektedir.</p>",
            "<p>Gizlilik politikamız kapsamında haklarınız ve verilerinizin kullanım amacı hakkında tam bilgi sahibi olabilirsiniz.</p>",
        ],
        [
            "<p>{$brand}, modern güvenlik altyapısıyla kullanıcı verilerini en üst düzeyde korumaktadır. Tüm veri transferleri şifreli protokoller üzerinden gerçekleştirilmektedir.</p>",
            "<p>Kişisel bilgileriniz, yalnızca hizmet sunumu için zorunlu olan durumlarda ve yasal çerçevede işlenmektedir. {$brand}, veri güvenliği ihlallerine karşı proaktif önlemler almaktadır.</p>",
            "<p>Verilerinizin nasıl toplandığı, işlendiği ve saklandığı hakkında detaylı bilgi edinmek için gizlilik politikamızı incelemenizi öneririz.</p>",
        ],
        [
            "<p>Kullanıcı gizliliği ve veri güvenliği, {$brand} platformunun temel taşlarındandır. Kişisel bilgileriniz, gelişmiş şifreleme yöntemleri ve güvenlik önlemleriyle korunmaktadır.</p>",
            "<p>{$brand}, veri toplama ve işleme süreçlerinde şeffaflığı esas almaktadır. Kullanıcı verileri, izin verilen amaçlar dışında kesinlikle kullanılmaz ve yetkisiz taraflarla paylaşılmaz.</p>",
            "<p>Veri koruma haklarınız konusunda sorularınız varsa, destek ekibimiz aracılığıyla bilgi talep edebilirsiniz.</p>",
        ],
    ];

    // ── 7. Sorumluluk Reddi ──
    $sorumluluk = [
        [
            "<p>{$brand} platformunda yer alan tüm içerikler bilgilendirme amaçlıdır. Sunulan bilgiler, herhangi bir kazanç veya sonuç garantisi oluşturmamaktadır.</p>",
            "<p>Bahis ve casino oyunlarına ilişkin paylaşılan analizler, istatistikler ve öneriler yalnızca bilgi amacı taşımakta olup yatırım tavsiyesi niteliği taşımamaktadır. {$brand}, kullanıcıların bu bilgilere dayanarak aldıkları kararlardan sorumlu tutulamaz.</p>",
            "<p>Platform üzerindeki üçüncü taraf bağlantıları ve içerikleri hakkında {$brand} sorumluluk kabul etmemektedir. Kullanıcılar, dış kaynaklara erişirken kendi takdirlerini kullanmalıdır.</p>",
        ],
        [
            "<p>{$brand} web sitesinde yayınlanan bilgiler genel bilgilendirme amacıyla hazırlanmıştır. Bu içerikler profesyonel danışmanlık yerine geçmez ve herhangi bir garanti içermez.</p>",
            "<p>Kullanıcılar, platformda yer alan bilgilere dayanarak verdikleri kararların sorumluluğunu kendileri üstlenmektedir. {$brand}, doğrudan veya dolaylı kayıplardan sorumlu tutulamaz.</p>",
            "<p>Dış web sitelerine yönlendiren bağlantılar bilgi amaçlı sağlanmıştır. {$brand}, bu sitelerin içeriklerini kontrol etmemekte ve onaylamadığını beyan etmektedir.</p>",
        ],
        [
            "<p>{$brand} platformundaki tüm içerikler, kullanıcıları bilgilendirme amacıyla sunulmaktadır. Hiçbir içerik, belirli bir sonucu garanti eden bir taahhüt olarak yorumlanmamalıdır.</p>",
            "<p>Bahis kararları tamamen kullanıcının kendi sorumluluğundadır. {$brand}, kullanıcıların platformdaki bilgilere dayanarak yaşayabilecekleri olası kayıplardan dolayı herhangi bir yükümlülük kabul etmemektedir.</p>",
            "<p>Platformda yer alan üçüncü taraf içerikleri ve bağlantıları, {$brand} tarafından onaylanmış olarak değerlendirilmemelidir.</p>",
        ],
        [
            "<p>Bu web sitesinde yer alan içerikler bilgilendirme ve eğlence amaçlıdır. {$brand}, sunulan bilgilerin doğruluğu veya tamlığı konusunda herhangi bir garanti vermemektedir.</p>",
            "<p>Kullanıcılar, bahis ve casino oyunlarına katılırken kendi araştırmalarını yapmalı ve bilinçli kararlar vermelidir. {$brand}, kullanıcıların finansal kayıplarından sorumlu değildir.</p>",
            "<p>Platform üzerinde referans verilen üçüncü taraf hizmetleri ve kaynakları, {$brand} ile bağlantılı olmayabilir. Bu kaynaklara erişim kullanıcının kendi sorumluluğundadır.</p>",
        ],
        [
            "<p>{$brand} sitesinde yayınlanan tüm materyaller bilgilendirme amaçlı olup herhangi bir yatırım veya bahis tavsiyesi niteliği taşımamaktadır.</p>",
            "<p>Platformda paylaşılan içeriklerin kullanımından doğabilecek sonuçlardan {$brand} sorumlu tutulamaz. Kullanıcılar, kendi kararlarının sorumluluğunu taşımaktadır.</p>",
            "<p>Harici web sitelerine yapılan yönlendirmeler kolaylık sağlamak amacıyla sunulmakta olup {$brand}, bu sitelerin gizlilik politikaları ve içerikleri hakkında sorumluluk kabul etmemektedir.</p>",
        ],
        [
            "<p>Bu platformda sunulan bilgiler yalnızca genel bilgilendirme niteliğindedir. {$brand}, içeriklerin eksiksiz, doğru veya güncel olduğuna dair hiçbir garanti vermemektedir.</p>",
            "<p>Bahis ve casino oyunlarıyla ilgili herhangi bir karar almadan önce kendi araştırmanızı yapmanız önerilir. {$brand}, bu kararların sonuçlarından dolayı sorumluluk kabul etmez.</p>",
            "<p>Platformdan erişilen üçüncü taraf web sitelerinin içerik ve hizmetleri {$brand} kontrolü dışındadır. Bu sitelerin kullanımı tamamen kullanıcının sorumluluğundadır.</p>",
        ],
    ];

    // ── 8. Telif Hakkı ──
    $telif = [
        [
            "<p>{$brand} web sitesinde yer alan tüm içerikler, görseller, logolar, tasarımlar ve yazılımlar telif hakkı yasaları kapsamında korunmaktadır. Bu materyallerin izinsiz kopyalanması, dağıtılması veya çoğaltılması yasaktır.</p>",
            "<p>Platform üzerindeki içeriklerin ticari amaçlarla kullanılması, {$brand} yönetiminin yazılı izni olmaksızın mümkün değildir. İhlal durumunda yasal işlem başlatılabilir.</p>",
            "<p>Kullanıcılar, {$brand} platformundaki içerikleri yalnızca kişisel kullanım amacıyla görüntüleyebilir. Her türlü telif hakkı {$brand} yönetimine aittir.</p>",
        ],
        [
            "<p>{$brand} platformunda yayınlanan tüm içerik ve materyaller fikri mülkiyet hakları ile korunmaktadır. Yazılar, görseller, logolar ve diğer tüm yaratıcı çalışmalar {$brand} mülkiyetindedir.</p>",
            "<p>Bu içeriklerin önceden yazılı izin alınmadan herhangi bir şekilde çoğaltılması, dağıtılması veya ticari amaçla kullanılması kesinlikle yasaktır.</p>",
            "<p>Telif hakkı ihlali tespit edilmesi durumunda {$brand}, yasal haklarını kullanma hakkını saklı tutmaktadır.</p>",
        ],
        [
            "<p>{$brand} web sitesindeki tüm metin, grafik, logo ve multimedya içerikleri telif hakkı koruması altındadır. Bu materyallerin tamamı veya bir kısmı izinsiz olarak kullanılamaz.</p>",
            "<p>İçeriklerin kişisel kullanım dışında herhangi bir amaçla kopyalanması veya yeniden yayınlanması için {$brand} yönetiminden yazılı onay alınması gerekmektedir.</p>",
            "<p>Fikri mülkiyet haklarının ihlali halinde yasal yaptırımlar uygulanabilir. {$brand}, bu konudaki tüm haklarını saklı tutar.</p>",
        ],
        [
            "<p>Bu web sitesinde yer alan her türlü içerik, tasarım unsuru ve yazılım, {$brand} yönetiminin fikri mülkiyetinde olup telif hakları yasaları ile korunmaktadır.</p>",
            "<p>{$brand} platformundaki materyallerin izinsiz kopyalanması, değiştirilmesi veya dağıtılması yasal yaptırımlarla sonuçlanabilir.</p>",
            "<p>Kullanıcılar, site içeriklerini yalnızca kişisel ve ticari olmayan amaçlarla kullanabilir. Ticari kullanım için önceden izin alınması zorunludur.</p>",
        ],
        [
            "<p>{$brand} bünyesindeki tüm özgün içerikler, görsel materyaller ve yazılımlar uluslararası telif hakkı yasaları kapsamında koruma altındadır.</p>",
            "<p>Bu içeriklerin herhangi bir ortamda izinsiz çoğaltılması, yayınlanması veya dağıtılması yasaktır. {$brand}, telif hakkı ihlallerine karşı yasal süreç başlatma hakkına sahiptir.</p>",
            "<p>İçerik kullanımına ilişkin talepler için {$brand} iletişim kanalları üzerinden başvuruda bulunabilirsiniz.</p>",
        ],
        [
            "<p>{$brand} platformundaki tüm içerik ve materyaller, telif hakkı mevzuatı çerçevesinde korunmaktadır. Logo, grafik, metin ve diğer yaratıcı çalışmaların tüm hakları saklıdır.</p>",
            "<p>İzinsiz kopyalama, dağıtım veya değişiklik yapma girişimleri yasal kovuşturmaya konu olabilir. {$brand}, fikri mülkiyet haklarının korunması konusunda kararlıdır.</p>",
            "<p>Platformdaki içerikler hakkında lisans veya kullanım izni talep etmek için {$brand} yönetimi ile iletişime geçmeniz gerekmektedir.</p>",
        ],
    ];

    $v = $variation;
    $content = '';
    $content .= "<h2>Sorumlu Oyun</h2>\n" . implode("\n", $sorumlu[$v]);
    $content .= "\n\n<h2>Yaş Kısıtlaması</h2>\n" . implode("\n", $yas[$v]);
    $content .= "\n\n<h2>Yasal Uygunluk</h2>\n" . implode("\n", $yasal[$v]);
    $content .= "\n\n<h2>Risk Uyarısı</h2>\n" . implode("\n", $risk[$v]);
    $content .= "\n\n<h2>Bağımlılık Desteği</h2>\n" . implode("\n", $bagimlilik[$v]);
    $content .= "\n\n<h2>Gizlilik ve Veri Koruması</h2>\n" . implode("\n", $gizlilik[$v]);
    $content .= "\n\n<h2>Sorumluluk Reddi</h2>\n" . implode("\n", $sorumluluk[$v]);
    $content .= "\n\n<h2>Telif Hakkı</h2>\n" . implode("\n", $telif[$v]);

    return $content;
}

function buildMeta($brand, $variation) {
    $titles = [
        "Yasal Bilgilendirme | {$brand} Sorumluluk ve Kullanım Koşulları",
        "{$brand} Yasal Bilgilendirme - Sorumlu Oyun ve Kullanım Şartları",
        "Yasal Uyarı ve Bilgilendirme | {$brand} Platformu",
        "{$brand} - Yasal Bilgilendirme, Gizlilik ve Sorumluluk Reddi",
        "Yasal Bilgilendirme ve Sorumlu Oyun | {$brand}",
        "{$brand} Yasal Bilgilendirme - Risk Uyarısı ve Kullanım Koşulları",
    ];

    $descriptions = [
        "{$brand} yasal bilgilendirme sayfası. Sorumlu oyun, yaş kısıtlaması, risk uyarısı, gizlilik politikası ve telif hakkı bilgileri hakkında detaylı açıklamalar.",
        "{$brand} platformunun yasal bilgilendirme ve sorumluluk reddi sayfası. Kullanım koşulları, veri koruması ve sorumlu bahis oynama kuralları hakkında bilgi edinin.",
        "Sorumlu oyun ilkeleri, yasal uygunluk, risk uyarısı ve gizlilik politikası hakkında {$brand} yasal bilgilendirme sayfası. 18 yaş ve üzeri kullanıcılar içindir.",
        "{$brand} yasal uyarı ve bilgilendirme sayfası. Bahis ve casino oyunlarına ilişkin yasal sorumluluklar, veri koruma ve telif hakkı bilgileri.",
        "{$brand} platformu yasal bilgilendirme: Sorumlu oyun politikası, yaş sınırlaması, finansal risk uyarısı ve kullanıcı verilerinin korunması hakkında bilgiler.",
        "Yasal bilgilendirme ve sorumluluk reddi. {$brand} platformundaki bahis ve casino hizmetlerine ilişkin kullanım koşulları ve yasal uyarılar.",
    ];

    return [
        'meta_title' => $titles[$variation],
        'meta_description' => $descriptions[$variation],
    ];
}

$sites = Site::orderBy('id')->get();
$total = 0;

foreach ($sites as $site) {
    $domain = $site->domain;
    $brand = $brands[$domain] ?? $site->name;
    $variation = getVariation($domain);

    Config::set('database.connections.tenant.database', $site->db_name);
    DB::purge('tenant');
    DB::reconnect('tenant');

    // Skip if already exists
    $exists = DB::connection('tenant')->table('pages')->where('slug', 'yasal-bilgilendirme')->exists();
    if ($exists) {
        echo "[{$domain}] SKIP — already exists\n";
        continue;
    }

    $content = buildContent($brand, $variation);
    $meta = buildMeta($brand, $variation);

    DB::connection('tenant')->table('pages')->insert([
        'slug'             => 'yasal-bilgilendirme',
        'title'            => 'Yasal Bilgilendirme',
        'content'          => $content,
        'meta_title'       => $meta['meta_title'],
        'meta_description' => $meta['meta_description'],
        'meta_keywords'    => "yasal bilgilendirme, sorumlu oyun, {$brand}, bahis kuralları, gizlilik politikası, yaş kısıtlaması, risk uyarısı",
        'is_published'     => true,
        'sort_order'       => 99,
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    echo "[{$domain}] OK — brand: {$brand}, variation: {$variation}\n";
    $total++;
}

echo "\n--- Total created: {$total} pages ---\n";
