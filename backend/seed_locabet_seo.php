<?php

/**
 * Locabet SEO Seed Script
 *
 * Sets up content_identity, categories, footer links, social links,
 * and content schedules for the 4 locabet sites.
 *
 * Usage:
 *   php artisan tinker --execute="$(tail -n +3 seed_locabet_seo.php)"
 */

use App\Models\Site;
use App\Models\Category;
use App\Models\FooterLink;
use App\Models\Post;
use App\Models\ContentSchedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// ─── SITE DEFINITIONS ───

$locabetSites = [
    'locabeet.com' => [
        'identity' => [
            'tone' => 'resmi ve kurumsal',
            'audience' => 'deneyimli online oyun kullanıcıları',
            'slogan' => 'Güvenilir oyun platformunun adresi',
            'keywords' => ['profesyonel', 'güvenilir', 'analiz', 'sektör lideri', 'kapsamlı rehber'],
        ],
        'prompt' => 'Sen Locabet sektör analistisin. Resmi, kurumsal ve güvenilir bir dil kullan. Hedef kitlen deneyimli online oyun kullanıcıları. Detaylı analizler, istatistiksel veriler ve karşılaştırmalı incelemeler sun. "Locabet uzman ekibinin" veya "Locabet güvencesiyle" gibi marka referansları kullan. Domain: {{domain}}',
        'meta_title' => 'Locabet Giriş 2026 – Güncel Adres ve Kapsamlı Rehber',
        'meta_description' => 'Locabet güncel giriş adresi, bonus kampanyaları ve detaylı platform rehberi. Güvenilir erişim için doğru adres.',
    ],
    'locabetbonus.me' => [
        'identity' => [
            'tone' => 'samimi ve arkadaşça',
            'audience' => 'bonus ve kampanya arayanlar',
            'slogan' => 'En avantajlı bonusların adresi',
            'keywords' => ['bonus', 'kampanya', 'fırsat', 'avantaj', 'promosyon'],
        ],
        'prompt' => 'Sen Locabet Bonus uzmanısın. Samimi, arkadaşça ve heyecan verici bir dil kullan. Hedef kitlen bonus ve kampanya arayan kullanıcılar. Güncel bonus fırsatları, çevrim şartları ve kampanya detaylarını sıcak bir üslupla anlat. "Locabet Bonus fırsatlarıyla" veya "Locabet Bonus ekibi olarak" gibi marka referansları kullan. Domain: {{domain}}',
        'meta_title' => 'Locabet Bonus 2026 – Güncel Kampanyalar ve Fırsatlar',
        'meta_description' => 'Locabet güncel bonus kampanyaları, hoş geldin bonusu ve promosyon fırsatları. En avantajlı bonuslar için rehberiniz.',
    ],
    'locabetcasino.com' => [
        'identity' => [
            'tone' => 'uzman ve teknik',
            'audience' => 'casino ve slot meraklıları',
            'slogan' => 'Casino dünyasının uzman rehberi',
            'keywords' => ['casino', 'slot', 'canlı casino', 'oyun rehberi', 'RTP analizi'],
        ],
        'prompt' => 'Sen Locabet Casino editörüsün. Uzman, teknik ve detaylı bir dil kullan. Hedef kitlen casino ve slot meraklıları. Oyun stratejileri, RTP analizleri, slot incelemeleri ve canlı casino deneyimlerini profesyonelce anlat. "Locabet Casino uzmanlarının" veya "Locabet Casino rehberinde" gibi marka referansları kullan. Domain: {{domain}}',
        'meta_title' => 'Locabet Casino 2026 – Oyun Rehberi ve Slot İncelemeleri',
        'meta_description' => 'Locabet casino oyunları, slot incelemeleri ve canlı casino rehberi. Uzman analizleri ve strateji ipuçlarıyla kazancınızı artırın.',
    ],
    'locabetgiris.site' => [
        'identity' => [
            'tone' => 'rehber ve öğretici',
            'audience' => 'giriş ve erişim sorunları yaşayan kullanıcılar',
            'slogan' => 'Kolay erişim, hızlı çözüm',
            'keywords' => ['giriş', 'erişim', 'güncel adres', 'çözüm rehberi', 'adım adım'],
        ],
        'prompt' => 'Sen Locabet Giriş teknik destek uzmanısın. Rehber niteliğinde, öğretici ve sabırlı bir dil kullan. Hedef kitlen giriş ve erişim sorunları yaşayan kullanıcılar. Adım adım çözüm rehberleri, DNS ayarları, VPN kullanımı ve alternatif erişim yöntemlerini detaylıca anlat. "Locabet Giriş rehberinde" veya "Locabet Giriş destek ekibi olarak" gibi marka referansları kullan. Domain: {{domain}}',
        'meta_title' => 'Locabet Giriş 2026 – Güncel Adres ve Erişim Rehberi',
        'meta_description' => 'Locabet güncel giriş adresi ve erişim rehberi. Adım adım giriş yapma, DNS ayarları ve sorun çözüm kılavuzu.',
    ],
];

// ─── CATEGORY DEFINITIONS ───

$categories = [
    'erisim' => [
        'name' => 'Erişim ve Giriş',
        'description' => 'Platform erişimi, giriş yöntemleri ve güncel adres bilgileri',
        'meta_title' => 'Erişim ve Giriş Rehberleri – Güncel Adres Bilgileri',
        'meta_description' => 'Güncel giriş adresleri, erişim sorunları çözümleri, DNS ayarları ve VPN rehberleri. Platforma kesintisiz erişim için kapsamlı bilgiler.',
        'content' => '<h1>Erişim ve Giriş Rehberleri</h1><p>Online platformlara erişim, günümüzde çeşitli teknik ve yasal faktörlerden etkilenebilmektedir. Bu kategori altında, platforma güvenli ve hızlı erişim sağlamak için ihtiyaç duyacağınız tüm bilgileri bulabilirsiniz.</p><h2>Güncel Adres Bilgileri</h2><p>Platformların alan adı değişiklikleri, erişim engellemeleri veya teknik güncellemeler nedeniyle giriş adresleri zaman zaman değişebilir. Güncel adresleri takip etmek, kesintisiz bir deneyim için büyük önem taşır. Telegram kanalları, resmi sosyal medya hesapları ve güvenilir bilgi kaynakları üzerinden güncel adres bilgilerine ulaşabilirsiniz.</p><h2>DNS Ayarları ve VPN Kullanımı</h2><p>Erişim sorunlarının çoğu DNS ayarlarının değiştirilmesiyle çözülebilir. Google DNS (8.8.8.8) veya Cloudflare DNS (1.1.1.1) gibi alternatif DNS sunucuları kullanarak platforma erişim sağlayabilirsiniz. Bunun yanı sıra, VPN uygulamaları da güvenli ve anonim erişim için etkili bir çözüm sunar.</p><h2>Tarayıcı ve Cihaz Uyumluluğu</h2><p>Farklı tarayıcılar ve cihazlar üzerinden platforma erişim deneyimi değişiklik gösterebilir. Chrome, Firefox, Safari ve Edge gibi modern tarayıcıların güncel sürümlerini kullanmak, hem güvenlik hem de performans açısından önerilir. Mobil cihazlarda ise özel uygulamalar veya mobil tarayıcı üzerinden erişim mümkündür.</p><h2>Sık Karşılaşılan Erişim Sorunları</h2><p>Bağlantı zaman aşımı, sayfa yüklenemedi hatası, DNS çözümleme sorunları ve yavaş bağlantı gibi yaygın problemlerin her biri farklı çözüm yöntemleri gerektirir. Sorun bazlı rehberlerimiz ile bu tür problemleri hızlıca çözebilirsiniz.</p><h2>Güvenli Erişim İpuçları</h2><p>Platforma erişirken güvenliğinizi sağlamak için dikkat etmeniz gereken unsurlar bulunmaktadır. SSL sertifika kontrolü, URL doğrulama ve şüpheli bağlantılardan kaçınma gibi temel güvenlik önlemlerini alarak, bilgilerinizi koruma altına alabilirsiniz. Her zaman resmi kaynakları tercih etmeniz önerilir.</p>',
        'sort_order' => 1,
    ],
    'bonus' => [
        'name' => 'Bonus ve Kampanyalar',
        'description' => 'Güncel bonus fırsatları, çevrim şartları ve kampanya detayları',
        'meta_title' => 'Bonus ve Kampanya Rehberi – Güncel Fırsatlar',
        'meta_description' => 'Hoş geldin bonusu, kayıp bonusu, free spin ve özel kampanya detayları. Çevrim şartları ve bonus kullanım rehberi.',
        'content' => '<h1>Bonus ve Kampanya Rehberleri</h1><p>Online platformlarda sunulan bonus ve kampanyalar, kullanıcı deneyimini zenginleştiren önemli avantajlardır. Hoş geldin bonuslarından sadakat programlarına kadar geniş bir yelpazede değerlendirilebilecek fırsatları bu kategori altında detaylıca inceliyoruz.</p><h2>Hoş Geldin Bonusları</h2><p>Yeni üyelere özel sunulan hoş geldin bonusları, platforma ilk adımı atanlar için cazip fırsatlar sunar. Bu bonusların oranları, üst limitleri ve çevrim şartları birbirinden farklılık gösterebilir. Karar vermeden önce tüm koşulları detaylıca incelemek önemlidir.</p><h2>Çevrim Şartları ve Koşullar</h2><p>Her bonusun bir çevrim şartı bulunur. Bu şart, bonus miktarının kaç katının oynanması gerektiğini belirler. Düşük çevrim şartlı bonuslar genellikle daha avantajlıdır. Hangi oyunların çevrime ne oranda katkı sağladığını bilmek, strateji geliştirmek için kritik öneme sahiptir.</p><h2>Yatırım Bonusları</h2><p>Belirli ödeme yöntemleriyle yapılan yatırımlara özel bonus kampanyaları düzenlenebilir. Kripto para, Papara veya banka havalesi gibi farklı yöntemlerle yapılan yatırımlara ek bonus avantajları sağlanabilir. Bu kampanyalar dönemsel olarak değişiklik gösterebilir.</p><h2>Kayıp Bonusu ve Cashback</h2><p>Belirli dönemlerdeki kayıplarınızın bir kısmının geri ödenmesini sağlayan kayıp bonusları, risk yönetimi açısından değerli bir fırsattır. Geri ödeme oranları, minimum kayıp tutarları ve başvuru süreçleri hakkında detaylı bilgileri rehberlerimizde bulabilirsiniz.</p><h2>Özel Turnuva ve Etkinlikler</h2><p>Platformlarda düzenlenen özel turnuvalar ve sezonluk etkinlikler, ek kazanç fırsatları sunar. Slot turnuvaları, canlı casino yarışmaları ve spor bahis ligleri gibi etkinliklerde ödül havuzlarından pay almak mümkündür.</p><h2>Bonus Seçim Stratejileri</h2><p>En uygun bonusu seçmek için oyun tercihlerinizi, bütçenizi ve çevrim şartlarını bir arada değerlendirmeniz gerekir. Her bonus türünün avantaj ve dezavantajlarını karşılaştırarak bilinçli tercihler yapabilirsiniz.</p>',
        'sort_order' => 2,
    ],
    'mobil' => [
        'name' => 'Mobil Erişim',
        'description' => 'Mobil giriş, uygulama indirme ve mobil optimizasyon rehberleri',
        'meta_title' => 'Mobil Erişim Rehberi – Uygulama ve Mobil Giriş',
        'meta_description' => 'Mobil giriş rehberi, Android ve iOS uygulama kurulumu, mobil optimizasyon ipuçları ve mobil site kullanım kılavuzu.',
        'content' => '<h1>Mobil Erişim Rehberleri</h1><p>Günümüzde internet trafiğinin büyük çoğunluğu mobil cihazlardan gerçekleşmektedir. Bu nedenle, platformlara mobil erişim konusu büyük önem taşımaktadır. Mobil tarayıcılar, özel uygulamalar ve mobil optimize edilmiş siteler aracılığıyla her yerden erişim mümkündür.</p><h2>Mobil Tarayıcı Üzerinden Giriş</h2><p>Herhangi bir uygulama indirmeden, mobil tarayıcınız üzerinden platforma erişebilirsiniz. Chrome, Safari veya Firefox gibi modern mobil tarayıcılar, masaüstü deneyimine yakın bir kullanım sunar. Tarayıcı ayarlarında pop-up engelleyiciyi kapatmak ve çerezlere izin vermek sorunsuz erişim için önemlidir.</p><h2>Android Uygulama Kurulumu</h2><p>Android cihazlar için sunulan APK dosyalarını indirip kurarak daha optimize bir deneyim elde edebilirsiniz. Bilinmeyen kaynaklara izin verme ayarını aktif hale getirmeniz gerekebilir. Uygulamayı her zaman resmi ve güvenilir kaynaklardan indirmeniz güvenliğiniz açısından kritik öneme sahiptir.</p><h2>iOS Erişim Yöntemleri</h2><p>iPhone ve iPad kullanıcıları, Safari tarayıcısı üzerinden platforma erişebilir. Ana ekrana kısayol ekleme özelliğini kullanarak uygulama benzeri bir deneyim elde edebilirsiniz. iOS\'un güvenlik kısıtlamaları nedeniyle bazı ek ayarlamalar gerekebilir.</p><h2>Mobil Performans İpuçları</h2><p>Mobil cihazlarda en iyi performansı elde etmek için düzenli olarak önbellek temizliği yapmak, uygulamayı güncel tutmak ve stabil bir internet bağlantısı kullanmak önerilir. Wi-Fi bağlantısı genellikle mobil veri bağlantısına göre daha kararlı bir deneyim sunar.</p><h2>Mobil Güvenlik Önlemleri</h2><p>Mobil cihazlarda güvenliğinizi sağlamak için ekran kilidi kullanmak, otomatik giriş bilgilerini dikkatli yönetmek ve güvenilir ağlara bağlanmak gibi önlemleri almak önemlidir. Halka açık Wi-Fi ağlarında VPN kullanımı ek güvenlik sağlar.</p>',
        'sort_order' => 3,
    ],
    'odeme' => [
        'name' => 'Ödeme Yöntemleri',
        'description' => 'Para yatırma, çekme yöntemleri ve ödeme rehberleri',
        'meta_title' => 'Ödeme Yöntemleri Rehberi – Para Yatırma ve Çekme',
        'meta_description' => 'Papara, kripto para, banka havalesi ile para yatırma ve çekme rehberi. Minimum limitler, çekim süreleri ve komisyon bilgileri.',
        'content' => '<h1>Ödeme Yöntemleri Rehberleri</h1><p>Online platformlarda para yatırma ve çekme işlemleri, kullanıcıların en çok merak ettiği konuların başında gelmektedir. Güvenli ödeme yöntemleri, hızlı işlem süreleri ve uygun limitler, platform seçiminde belirleyici faktörler arasındadır.</p><h2>Papara ile Yatırım ve Çekim</h2><p>Papara, Türkiye\'de en yaygın kullanılan dijital ödeme yöntemlerinden biridir. Anlık yatırım ve hızlı çekim imkanı sunar. Hesap eşleme işlemi tamamlandıktan sonra saniyeler içinde yatırım yapılabilir. Çekim talepleri genellikle birkaç saat içinde işleme alınır.</p><h2>Kripto Para ile İşlemler</h2><p>Bitcoin, USDT (Tether) ve Ethereum gibi kripto paralarla yatırım yapmak, gizlilik ve hız avantajı sağlar. Kripto para transferleri genellikle 10-30 dakika içinde onaylanır. Ağ yoğunluğuna bağlı olarak süre değişiklik gösterebilir. İşlem ücretleri kripto para türüne göre farklılık gösterir.</p><h2>Banka Havalesi ve EFT</h2><p>Geleneksel banka transferi yöntemleri, yüksek tutarlı işlemler için tercih edilir. EFT işlemleri mesai saatleri içinde gerçekleştirilirken, havale işlemleri aynı banka müşterileri arasında anlık olarak tamamlanır. İşlem limitleri ve süreleri bankaya göre değişiklik gösterir.</p><h2>Minimum ve Maksimum Limitler</h2><p>Her ödeme yönteminin kendine özgü minimum yatırım ve çekim limitleri bulunmaktadır. Bu limitler platform politikaları ve ödeme sağlayıcı kurallarına göre belirlenir. Yatırım yapmadan önce güncel limit bilgilerini kontrol etmek önemlidir.</p><h2>Çekim Süreleri ve Prosedürler</h2><p>Çekim talepleri genellikle hesap doğrulama durumuna bağlı olarak 1-24 saat içinde işleme alınır. İlk çekim talebinde kimlik doğrulama belgesi istenebilir. Düzenli kullanıcılar için çekim süreleri önemli ölçüde kısalır.</p><h2>Güvenli Ödeme İpuçları</h2><p>Ödeme işlemlerinde güvenliğinizi sağlamak için her zaman resmi ve doğrulanmış hesaplara transfer yapın. Şüpheli ödeme talepleri konusunda dikkatli olun ve işlem onaylarını dikkatlice kontrol edin. Büyük tutarlı işlemlerde ek doğrulama adımları normaldir.</p>',
        'sort_order' => 4,
    ],
    'oyun' => [
        'name' => 'Oyun Rehberleri',
        'description' => 'Casino, slot, canlı bahis ve oyun stratejileri',
        'meta_title' => 'Oyun Rehberleri – Casino, Slot ve Bahis Stratejileri',
        'meta_description' => 'Casino oyunları, slot incelemeleri, canlı bahis stratejileri ve oyun ipuçları. Kapsamlı rehberlerle kazanma şansınızı artırın.',
        'content' => '<h1>Oyun Rehberleri</h1><p>Online platformlarda sunulan oyun çeşitliliği, kullanıcılara geniş bir eğlence yelpazesi sunmaktadır. Casino klasiklerinden modern slot oyunlarına, canlı masa deneyimlerinden sanal bahislere kadar her zevke uygun seçenekler mevcuttur.</p><h2>Slot Oyunları ve RTP Analizi</h2><p>Slot oyunları, en popüler online casino kategorilerinden biridir. Her slot oyununun kendine özgü bir RTP (Return to Player) oranı bulunur. Yüksek RTP\'li oyunlar, uzun vadede daha fazla geri ödeme potansiyeli sunar. Pragmatic Play, NetEnt ve Microgaming gibi önde gelen sağlayıcıların oyunları güvenilir ve kaliteli deneyimler sunar.</p><h2>Canlı Casino Deneyimi</h2><p>Gerçek krupiyeler eşliğinde oynanan canlı casino oyunları, fiziksel casino atmosferini evinize taşır. Canlı rulet, blackjack, poker ve baccarat masaları, HD kalitesinde yayınlarla sunulur. Crazy Time ve Monopoly Live gibi game show formatındaki oyunlar da büyük popülerlik kazanmıştır.</p><h2>Masa Oyunları Stratejileri</h2><p>Blackjack, poker ve baccarat gibi masa oyunlarında strateji kullanmak, kazanma olasılığınızı artırabilir. Temel strateji tabloları, kart sayma teknikleri ve bankroll yönetimi gibi konularda detaylı rehberlerimizi inceleyerek oyun becerilerinizi geliştirebilirsiniz.</p><h2>Spor Bahisleri ve Canlı Bahis</h2><p>Futbol, basketbol, tenis ve daha birçok spor dalında bahis yapabilirsiniz. Canlı bahis seçenekleri, maç sürerken anlık oranlarla bahis yapma imkanı sunar. Maç öncesi analiz, istatistik takibi ve oran karşılaştırması başarılı bahis stratejisinin temel unsurlarıdır.</p><h2>Sorumlu Oyun İlkeleri</h2><p>Oyun deneyimini keyifli tutmak için bütçe belirleme, zaman sınırı koyma ve duygusal kararlardan kaçınma gibi sorumlu oyun ilkelerine uymanız önemlidir. Kayıp limitlerini önceden belirlemek ve bu limitlere sadık kalmak, sağlıklı bir oyun deneyimi için temel kurallardan biridir.</p>',
        'sort_order' => 5,
    ],
    'guvenlik' => [
        'name' => 'Güvenlik',
        'description' => 'Hesap güvenliği, lisans bilgileri ve güvenilirlik analizleri',
        'meta_title' => 'Güvenlik Rehberi – Hesap Koruma ve Güvenilirlik',
        'meta_description' => 'Hesap güvenliği ipuçları, 2FA kurulumu, lisans bilgileri ve platform güvenilirlik analizleri. Güvenli oyun deneyimi için rehberiniz.',
        'content' => '<h1>Güvenlik Rehberleri</h1><p>Online platformlarda güvenlik, kullanıcıların en öncelikli konularından biridir. Hesap güvenliğinden ödeme güvenliğine, lisans bilgilerinden kişisel veri korumasına kadar kapsamlı güvenlik rehberlerimiz ile bilinçli ve güvenli bir deneyim yaşayabilirsiniz.</p><h2>Hesap Güvenliği Temelleri</h2><p>Güçlü ve benzersiz şifre kullanmak, hesap güvenliğinin ilk adımıdır. Şifrenizde büyük-küçük harf, rakam ve özel karakter kombinasyonu kullanmanız önerilir. Aynı şifreyi birden fazla platformda kullanmaktan kaçının ve şifrenizi düzenli aralıklarla değiştirin.</p><h2>İki Faktörlü Doğrulama (2FA)</h2><p>İki faktörlü doğrulama, hesabınıza ek bir güvenlik katmanı ekler. Google Authenticator veya SMS doğrulama gibi yöntemlerle, şifreniz ele geçirilse bile hesabınıza yetkisiz erişim engellenmiş olur. 2FA kurulumunu mutlaka aktif hale getirmeniz tavsiye edilir.</p><h2>Lisans ve Düzenleme Bilgileri</h2><p>Güvenilir bir platform, uluslararası oyun otoriteleri tarafından verilen geçerli lisanslara sahip olmalıdır. Curaçao eGaming, Malta Gaming Authority ve UK Gambling Commission gibi kurumların lisansları, platformun belirli standartlara uyduğunun göstergesidir.</p><h2>Kişisel Veri Koruma</h2><p>Platformlar, kullanıcı verilerini SSL şifreleme teknolojisi ile korur. KVKK ve GDPR gibi düzenlemelere uyumluluk, kişisel bilgilerinizin güvenli şekilde işlenmesini garanti eder. Gizlilik politikalarını okumak ve veri paylaşım tercihlerinizi yönetmek önemlidir.</p><h2>Dolandırıcılıktan Korunma</h2><p>Sahte siteler, phishing saldırıları ve sosyal mühendislik yöntemlerine karşı dikkatli olunmalıdır. Her zaman resmi alan adını kontrol edin, şüpheli bağlantılara tıklamayın ve kişisel bilgilerinizi güvenli olmayan kanallardan paylaşmayın. Şüpheli durumlarda derhal müşteri hizmetleriyle iletişime geçin.</p>',
        'sort_order' => 6,
    ],
    'genel' => [
        'name' => 'Genel Bilgiler',
        'description' => 'Platform incelemeleri, kullanıcı rehberleri ve genel bilgiler',
        'meta_title' => 'Genel Bilgiler – Platform Rehberi ve İncelemeler',
        'meta_description' => 'Platform incelemeleri, kullanıcı rehberleri, avantaj-dezavantaj analizleri ve güncel gelişmeler. Kapsamlı bilgi kaynağınız.',
        'content' => '<h1>Genel Bilgiler ve Platform Rehberi</h1><p>Bu kategori altında, platform hakkında genel bilgiler, kullanıcı rehberleri, karşılaştırmalı analizler ve güncel gelişmeleri bulabilirsiniz. Yeni başlayanlar için temel bilgilerden deneyimli kullanıcılar için ileri düzey ipuçlarına kadar kapsamlı içerikler sunuyoruz.</p><h2>Platform Tanıtımı ve Genel Bakış</h2><p>Platformun sunduğu hizmetler, oyun çeşitliliği, ödeme yöntemleri ve müşteri hizmetleri gibi temel özellikler hakkında genel bir değerlendirme yapabilirsiniz. Kullanıcı arayüzü, tasarım kalitesi ve genel kullanım deneyimi gibi konularda detaylı incelemelerimizi okuyabilirsiniz.</p><h2>Kayıt ve Üyelik Süreci</h2><p>Platforma üye olmak için gerekli adımlar, form doldurma, hesap doğrulama ve ilk giriş süreçleri hakkında detaylı bilgi edinebilirsiniz. Kayıt sırasında dikkat edilmesi gereken noktalar ve hızlı üyelik ipuçları rehberlerimizde yer almaktadır.</p><h2>Müşteri Hizmetleri</h2><p>Canlı destek, e-posta, Telegram ve diğer iletişim kanalları üzerinden müşteri hizmetlerine ulaşabilirsiniz. Sorunlarınızın hızlı çözümü için doğru iletişim kanalını seçmek ve etkili bir şekilde sorunuzu aktarmak önemlidir.</p><h2>Kullanıcı Deneyimleri ve Yorumlar</h2><p>Gerçek kullanıcı deneyimleri ve yorumları, platform hakkında objektif bir değerlendirme yapmanızı sağlar. Olumlu ve olumsuz geri bildirimleri bir arada değerlendirerek bilinçli bir tercih yapabilirsiniz.</p><h2>Avantaj ve Dezavantajlar</h2><p>Her platformun güçlü ve zayıf yönleri bulunmaktadır. Kapsamlı artı-eksi analizlerimiz ile platformun size uygun olup olmadığını değerlendirebilirsiniz. Rakip platformlarla karşılaştırmalı incelemelerimiz de karar sürecinize yardımcı olacaktır.</p>',
        'sort_order' => 7,
    ],
];

// ─── FOOTER LINK DEFINITIONS ───

$footerLinks = [
    ['link_text' => 'Locabet Giriş', 'link_url' => 'https://locabeet.com', 'sort_order' => 1],
    ['link_text' => 'Locabet Bonus', 'link_url' => 'https://locabetbonus.me', 'sort_order' => 2],
    ['link_text' => 'Locabet Casino', 'link_url' => 'https://locabetcasino.com', 'sort_order' => 3],
    ['link_text' => 'Locabet Mobil', 'link_url' => '/locabet-mobil-giris', 'sort_order' => 4],
    ['link_text' => 'Locabet Kayıt', 'link_url' => '/locabet-kayit', 'sort_order' => 5],
    ['link_text' => 'Blog', 'link_url' => '/blog', 'sort_order' => 6],
];

// ─── SOCIAL LINKS ───

$socialLinks = [
    'telegram' => 'https://t.me/locabet',
    'instagram' => 'https://instagram.com/locabet',
    'x' => 'https://x.com/locabet',
];

// ─── CATEGORY → POST SLUG MAPPING ───

$categorySlugPatterns = [
    'erisim' => ['giris', 'adres', 'vpn', 'dns', 'erisim', 'ayna', 'mirror'],
    'bonus' => ['bonus', 'kampanya', 'promosyon', 'free-spin', 'kayip', 'cashback', 'davet', 'yatirim-bonusu'],
    'mobil' => ['mobil', 'android', 'ios', 'apk', 'uygulama'],
    'odeme' => ['papara', 'kripto', 'banka', 'havale', 'yatirim', 'cekim', 'odeme', 'para'],
    'oyun' => ['casino', 'slot', 'bahis', 'canli', 'poker', 'rulet', 'blackjack', 'sanal'],
    'guvenlik' => ['guvenlik', 'guvenilir', 'lisans', '2fa', 'dogrulama', 'sifre'],
    'genel' => ['uyelik', 'kayit', 'musteri', 'yorum', 'avantaj', 'dezavantaj', 'inceleme'],
];

// ══════════════════════════════════════════════
// EXECUTION
// ══════════════════════════════════════════════

echo "=== Locabet SEO Seed Script ===\n\n";

foreach ($locabetSites as $domain => $config) {
    $site = Site::where('domain', $domain)->first();

    if (!$site) {
        echo "SKIP: Site not found: {$domain}\n";
        continue;
    }

    echo "--- Processing: {$domain} ---\n";

    // ─── 1a. Update Site Settings (cms_main.sites) ───

    $site->update([
        'content_identity' => $config['identity'],
        'content_prompt_template' => $config['prompt'],
        'meta_title' => $config['meta_title'],
        'meta_description' => $config['meta_description'],
        'social_links' => $socialLinks,
    ]);
    echo "  ✓ Updated content_identity, meta_title, meta_description, social_links\n";

    // ─── Switch to tenant DB ───

    $tenantDb = $site->db_name;
    Config::set('database.connections.tenant.database', $tenantDb);
    DB::connection('tenant')->reconnect();

    // ─── 1b. Create Categories ───

    $categoryMap = []; // slug => id
    foreach ($categories as $slug => $catData) {
        $existing = Category::on('tenant')->where('slug', $slug)->first();
        if ($existing) {
            $existing->update([
                'name' => $catData['name'],
                'description' => $catData['description'],
                'content' => $catData['content'],
                'meta_title' => $catData['meta_title'],
                'meta_description' => $catData['meta_description'],
                'sort_order' => $catData['sort_order'],
                'is_active' => true,
            ]);
            $categoryMap[$slug] = $existing->id;
        } else {
            $cat = Category::on('tenant')->create([
                'slug' => $slug,
                'name' => $catData['name'],
                'description' => $catData['description'],
                'content' => $catData['content'],
                'meta_title' => $catData['meta_title'],
                'meta_description' => $catData['meta_description'],
                'sort_order' => $catData['sort_order'],
                'is_active' => true,
            ]);
            $categoryMap[$slug] = $cat->id;
        }
    }
    echo "  ✓ Created/updated " . count($categories) . " categories\n";

    // ─── Assign category_id to existing posts ───

    $posts = Post::on('tenant')->whereNull('category_id')->get();
    $assigned = 0;
    foreach ($posts as $post) {
        $postSlug = $post->slug;
        $matchedCategory = null;

        foreach ($categorySlugPatterns as $catSlug => $patterns) {
            foreach ($patterns as $pattern) {
                if (Str::contains($postSlug, $pattern)) {
                    $matchedCategory = $catSlug;
                    break 2;
                }
            }
        }

        if ($matchedCategory && isset($categoryMap[$matchedCategory])) {
            $post->update(['category_id' => $categoryMap[$matchedCategory]]);
            $assigned++;
        } else {
            // Default to 'genel'
            if (isset($categoryMap['genel'])) {
                $post->update(['category_id' => $categoryMap['genel']]);
                $assigned++;
            }
        }
    }
    echo "  ✓ Assigned categories to {$assigned} posts\n";

    // ─── 1c. Create Footer Links ───

    $existingFooterCount = FooterLink::on('tenant')->count();
    if ($existingFooterCount === 0) {
        foreach ($footerLinks as $link) {
            FooterLink::on('tenant')->create([
                'link_text' => $link['link_text'],
                'link_url' => $link['link_url'],
                'sort_order' => $link['sort_order'],
                'is_active' => true,
            ]);
        }
        echo "  ✓ Created " . count($footerLinks) . " footer links\n";
    } else {
        echo "  ⊘ Footer links already exist ({$existingFooterCount}), skipping\n";
    }

    echo "\n";
}

// ─── Faz 5: Content Schedules ───

echo "--- Setting up Content Schedules ---\n";

foreach ($locabetSites as $domain => $config) {
    $site = Site::where('domain', $domain)->first();
    if (!$site) continue;

    $existing = ContentSchedule::where('site_id', $site->id)->first();
    if ($existing) {
        $existing->update([
            'content_type' => 'daily_post',
            'frequency' => 'custom',
            'interval_hours' => 72, // 3 days
            'is_active' => true,
        ]);
        echo "  ✓ Updated content schedule for {$domain}\n";
    } else {
        ContentSchedule::create([
            'site_id' => $site->id,
            'content_type' => 'daily_post',
            'frequency' => 'custom',
            'run_at' => '08:00',
            'interval_hours' => 72,
            'is_active' => true,
            'next_run_at' => now()->addHours(72),
        ]);
        echo "  ✓ Created content schedule for {$domain} (every 72h)\n";
    }
}

echo "\n=== Seed Complete ===\n";
