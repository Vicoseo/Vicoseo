<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AIContentService
{
    /**
     * 30+ topic pool for daily content rotation.
     */
    private const TOPIC_POOL = [
        // Giriş/Erişim
        ['slug_suffix' => 'giris-yapamiyor-cozum', 'topic' => '{brand} Giriş Yapamıyorum Çözüm', 'category' => 'erisim', 'instructions' => '{brand} giriş yapamama sorunlarının çözüm rehberi. DNS sorunları, tarayıcı cache temizleme, VPN kullanımı, alternatif adresler. Kullanıcıların yaşadığı gerçek sorunlara pratik çözümler sun.'],
        ['slug_suffix' => 'yeni-adres-nereden-bulunur', 'topic' => '{brand} Yeni Adres Nereden Bulunur?', 'category' => 'erisim', 'instructions' => '{brand} güncel giriş adresini güvenli şekilde bulma yöntemleri. Resmi kanallar, Telegram grupları, sosyal medya takibi. Sahte sitelerden korunma uyarıları.'],
        ['slug_suffix' => 'vpn-ile-giris', 'topic' => '{brand} VPN ile Giriş Rehberi', 'category' => 'erisim', 'instructions' => '{brand} platformuna VPN kullanarak güvenli giriş yapma rehberi. En iyi VPN servisleri, bağlantı ayarları, hız optimizasyonu.'],
        ['slug_suffix' => 'ayna-site-nedir', 'topic' => '{brand} Ayna Site (Mirror) Nedir?', 'category' => 'erisim', 'instructions' => 'Ayna site kavramı, {brand} ayna siteleri nasıl çalışır, orijinal site ile farkları, güvenli ayna site doğrulama yöntemleri.'],
        ['slug_suffix' => 'dns-ayari-ile-giris', 'topic' => '{brand} DNS Ayarı ile Giriş', 'category' => 'erisim', 'instructions' => 'DNS ayarlarını değiştirerek {brand} sitesine erişim rehberi. Google DNS, Cloudflare DNS kurulumu, adım adım anlatım.'],

        // Bonus
        ['slug_suffix' => 'hos-geldin-bonusu', 'topic' => '{brand} Hoş Geldin Bonusu {year}', 'category' => 'bonus', 'instructions' => '{brand} hoş geldin bonusu detayları, çevrim şartları, minimum yatırım miktarı, bonus kullanım koşulları ve ipuçları.'],
        ['slug_suffix' => 'cevrim-sartlari-rehberi', 'topic' => '{brand} Çevrim Şartları Rehberi', 'category' => 'bonus', 'instructions' => '{brand} bonus çevrim şartları nasıl çalışır, çevrim hesaplama, hangi oyunlar çevrime katkı sağlar, dikkat edilmesi gerekenler.'],
        ['slug_suffix' => 'free-spin-bonuslari', 'topic' => '{brand} Free Spin Bonusları', 'category' => 'bonus', 'instructions' => '{brand} free spin kampanyaları, hangi slot oyunlarında geçerli, kazanç çekim şartları, güncel free spin promosyonları.'],
        ['slug_suffix' => 'kayip-bonusu', 'topic' => '{brand} Kayıp Bonusu Nasıl Alınır?', 'category' => 'bonus', 'instructions' => '{brand} kayıp bonusu (cashback) detayları, geri ödeme oranları, başvuru yöntemi, hangi oyunlar dahil.'],
        ['slug_suffix' => 'arkadas-davet-bonusu', 'topic' => '{brand} Arkadaş Davet Bonusu', 'category' => 'bonus', 'instructions' => '{brand} referans sistemi, arkadaş davet bonusu nasıl çalışır, davet linki oluşturma, kazanç detayları.'],
        ['slug_suffix' => 'ilk-yatirim-bonusu', 'topic' => '{brand} İlk Yatırım Bonusu {year}', 'category' => 'bonus', 'instructions' => '{brand} ilk yatırım bonusu kampanyası, bonus oranları, minimum yatırım, çevrim şartları ve detaylı kullanım rehberi.'],

        // Mobil
        ['slug_suffix' => 'mobil-uygulama-indirme', 'topic' => '{brand} Mobil Uygulama İndirme', 'category' => 'mobil', 'instructions' => '{brand} mobil uygulamasını indirme ve kurma rehberi. Android ve iOS için ayrı ayrı adımlar, sistem gereksinimleri.'],
        ['slug_suffix' => 'android-apk-kurulum', 'topic' => '{brand} Android APK Kurulum Rehberi', 'category' => 'mobil', 'instructions' => '{brand} Android APK dosyasını indirme, bilinmeyen kaynaklara izin verme, kurulum adımları, güncelleme yöntemi.'],
        ['slug_suffix' => 'ios-giris-rehberi', 'topic' => '{brand} iOS Giriş Rehberi', 'category' => 'mobil', 'instructions' => '{brand} iPhone ve iPad ile giriş rehberi. Safari üzerinden erişim, ana ekrana ekleme, kısıtlama sorunları çözümü.'],
        ['slug_suffix' => 'mobil-site-vs-uygulama', 'topic' => '{brand} Mobil Site vs Uygulama Karşılaştırma', 'category' => 'mobil', 'instructions' => '{brand} mobil site ve uygulama arasındaki farklar, avantaj/dezavantaj karşılaştırması, hangisi tercih edilmeli.'],

        // Ödeme
        ['slug_suffix' => 'papara-ile-yatirim', 'topic' => '{brand} Papara ile Yatırım', 'category' => 'odeme', 'instructions' => '{brand} Papara ile para yatırma rehberi. Hesap eşleme, minimum/maksimum limitler, işlem süreleri, komisyon bilgisi.'],
        ['slug_suffix' => 'kripto-ile-yatirim', 'topic' => '{brand} Kripto ile Yatırım Rehberi', 'category' => 'odeme', 'instructions' => '{brand} kripto para ile yatırım rehberi. Bitcoin, USDT, Ethereum ile para yatırma, cüzdan adresi, işlem süreleri.'],
        ['slug_suffix' => 'banka-havalesi-yatirim', 'topic' => '{brand} Banka Havalesi ile Yatırım', 'category' => 'odeme', 'instructions' => '{brand} banka havalesi/EFT ile para yatırma, hesap bilgileri, işlem süreleri, minimum tutar bilgisi.'],
        ['slug_suffix' => 'minimum-yatirim-tutari', 'topic' => '{brand} Minimum Yatırım ve Çekim Tutarları', 'category' => 'odeme', 'instructions' => '{brand} yatırım ve çekim limitleri, ödeme yöntemine göre minimum/maksimum tutarlar, detaylı tablo.'],
        ['slug_suffix' => 'cekim-sureleri', 'topic' => '{brand} Çekim Süreleri ve Yöntemleri', 'category' => 'odeme', 'instructions' => '{brand} para çekme yöntemleri, her yöntem için tahmini süreler, hesap doğrulama gereklilikleri.'],

        // Oyun
        ['slug_suffix' => 'canli-bahis-rehberi', 'topic' => '{brand} Canlı Bahis Rehberi', 'category' => 'oyun', 'instructions' => '{brand} canlı bahis bölümü incelemesi, mevcut sporlar, oran yapısı, canlı bahis ipuçları ve stratejiler.'],
        ['slug_suffix' => 'slot-oyunlari-rehberi', 'topic' => '{brand} Slot Oyunları Rehberi {year}', 'category' => 'oyun', 'instructions' => '{brand} popüler slot oyunları, RTP oranları, bonus özellikleri, en çok kazandıran slotlar, demo oyun imkanı.'],
        ['slug_suffix' => 'casino-masa-oyunlari', 'topic' => '{brand} Casino Masa Oyunları', 'category' => 'oyun', 'instructions' => '{brand} masa oyunları: blackjack, rulet, poker, bakara. Oyun kuralları, masalimitleri, strateji ipuçları.'],
        ['slug_suffix' => 'canli-casino-deneyimi', 'topic' => '{brand} Canlı Casino Deneyimi', 'category' => 'oyun', 'instructions' => '{brand} canlı casino bölümü, gerçek krupiyeler, oyun çeşitliliği, yayın kalitesi, masa limitleri incelemesi.'],
        ['slug_suffix' => 'sanal-bahis-rehberi', 'topic' => '{brand} Sanal Bahis Rehberi', 'category' => 'oyun', 'instructions' => '{brand} sanal spor bahisleri, sanal futbol, at yarışı, tazı yarışı, nasıl oynanır, kazanma taktikleri.'],

        // Güvenlik
        ['slug_suffix' => 'hesap-guvenligi', 'topic' => '{brand} Hesap Güvenliği Rehberi', 'category' => 'guvenlik', 'instructions' => '{brand} hesap güvenliği ipuçları, güçlü şifre oluşturma, hesap doğrulama, şüpheli aktivite bildirimi.'],
        ['slug_suffix' => '2fa-kurulum-rehberi', 'topic' => '{brand} İki Faktörlü Doğrulama (2FA) Kurulumu', 'category' => 'guvenlik', 'instructions' => '{brand} 2FA kurulum rehberi, Google Authenticator kullanımı, SMS doğrulama, güvenlik artırma adımları.'],
        ['slug_suffix' => 'guvenilir-mi-analizi', 'topic' => '{brand} Güvenilir Mi? Detaylı Analiz {year}', 'category' => 'guvenlik', 'instructions' => '{brand} güvenilirlik analizi, lisans bilgileri, kullanıcı deneyimleri, ödeme performansı, artı/eksi değerlendirme.'],
        ['slug_suffix' => 'lisans-bilgileri', 'topic' => '{brand} Lisans ve Yasal Durum Bilgileri', 'category' => 'guvenlik', 'instructions' => '{brand} lisans detayları, regülatör bilgisi, yasal çerçeve, kullanıcı hakları ve korumaları.'],

        // Genel
        ['slug_suffix' => 'uyelik-nasil-acilir', 'topic' => '{brand} Üyelik Nasıl Açılır? Adım Adım', 'category' => 'genel', 'instructions' => '{brand} kayıt ve üyelik açma rehberi, form doldurma, hesap doğrulama, ilk giriş adımları.'],
        ['slug_suffix' => 'musteri-hizmetleri', 'topic' => '{brand} Müşteri Hizmetleri İletişim', 'category' => 'genel', 'instructions' => '{brand} müşteri hizmetlerine ulaşma yolları: canlı destek, e-posta, Telegram, WhatsApp. Çalışma saatleri, yanıt süreleri.'],
        ['slug_suffix' => 'kullanici-yorumlari', 'topic' => '{brand} Kullanıcı Yorumları ve Değerlendirmeleri {year}', 'category' => 'genel', 'instructions' => '{brand} gerçek kullanıcı deneyimleri, olumlu/olumsuz yorumlar, genel memnuniyet analizi, puan tablosu.'],
        ['slug_suffix' => 'avantaj-ve-dezavantajlar', 'topic' => '{brand} Avantaj ve Dezavantajları {year}', 'category' => 'genel', 'instructions' => '{brand} detaylı artı/eksi analizi, rakiplerle kıyaslama, hangi kullanıcı profiline uygun, genel değerlendirme.'],
    ];

    /**
     * Writing tone variations for uniqueness.
     */
    private const TONE_POOL = [
        'resmi ve kurumsal',
        'samimi ve arkadaşça',
        'uzman ve teknik',
        'genç ve dinamik',
        'analitik ve karşılaştırmalı',
        'deneyim paylaşımı tarzında',
        'soru-cevap odaklı',
        'rehber ve öğretici',
    ];

    /**
     * Content angle variations for uniqueness.
     */
    private const ANGLE_POOL = [
        'kullanıcı deneyimi odaklı',
        'teknik detay odaklı',
        'karşılaştırmalı analiz',
        'adım adım rehber',
        'sorun-çözüm odaklı',
        'güncellik ve trend odaklı',
        'başlangıç seviyesi rehber',
        'ileri seviye ipuçları',
    ];

    /**
     * Generate new content using AI.
     */
    public function generateContent(string $type, string $topic, string $instructions, array $siteInfo = []): array
    {
        $prompt = $this->buildGeneratePrompt($type, $topic, $instructions, $siteInfo);

        // Append site custom prompt template if available
        $prompt = $this->appendSiteTemplate($prompt, $siteInfo);

        return $this->callAI($prompt);
    }

    /**
     * Check if a slug already exists to prevent duplicate content.
     */
    public function slugExists(string $slug, array $existingSlugs): bool
    {
        return in_array($slug, $existingSlugs, true);
    }

    /**
     * Generate landing page content with full SEO structure.
     */
    public function generateLandingPage(string $brandName, string $domain, array $siteInfo = [], array $existingSlugs = []): array
    {
        $prompt = $this->buildLandingPagePrompt($brandName, $domain, $siteInfo, $existingSlugs);
        $prompt = $this->appendSiteTemplate($prompt, $siteInfo);

        return $this->callAI($prompt, 16384);
    }

    /**
     * Generate cluster/supporting blog article.
     */
    public function generateClusterArticle(string $brandName, string $domain, string $articleTopic, string $articleInstructions, array $siteInfo = [], array $clusterSlugs = [], array $existingSlugs = []): array
    {
        $prompt = $this->buildClusterArticlePrompt($brandName, $domain, $articleTopic, $articleInstructions, $siteInfo, $clusterSlugs, $existingSlugs);
        $prompt = $this->appendSiteTemplate($prompt, $siteInfo);

        return $this->callAI($prompt, 16384);
    }

    /**
     * Generate a static/legal page (about, contact, privacy, disclaimer).
     */
    public function generateStaticPage(string $brandName, string $domain, string $pageType, string $pageInstructions, array $siteInfo = []): array
    {
        $prompt = $this->buildStaticPagePrompt($brandName, $domain, $pageType, $pageInstructions, $siteInfo);

        return $this->callAI($prompt, 4096);
    }

    /**
     * Spin existing content into a new unique version.
     */
    public function spinContent(array $original, string $instructions): array
    {
        $prompt = $this->buildSpinPrompt($original, $instructions);

        return $this->callAI($prompt);
    }

    /**
     * Generate a daily blog post with unique tone and angle.
     */
    public function generateDailyPost(string $brandName, string $domain, string $topic, string $instructions, array $existingSlugs = [], array $siteInfo = []): array
    {
        $prompt = $this->buildDailyPostPrompt($brandName, $domain, $topic, $instructions, $existingSlugs, $siteInfo);
        $prompt = $this->appendSiteTemplate($prompt, $siteInfo);
        $prompt = $this->appendDuplicatePrevention($prompt, $existingSlugs);

        return $this->callAI($prompt, 16384);
    }

    /**
     * Get available (unused) topics for a site.
     */
    public function getAvailableTopics(string $brandName, array $existingSlugs): array
    {
        $slug = Str::slug($brandName);
        $available = [];

        foreach (self::TOPIC_POOL as $topic) {
            $fullSlug = $slug . '-' . $topic['slug_suffix'];
            if (!in_array($fullSlug, $existingSlugs)) {
                $available[] = array_merge($topic, ['full_slug' => $fullSlug]);
            }
        }

        return $available;
    }

    /**
     * Pick N random unused topics from different categories.
     */
    public function pickTopics(string $brandName, array $existingSlugs, int $count = 2): array
    {
        $available = $this->getAvailableTopics($brandName, $existingSlugs);

        if (empty($available)) {
            return [];
        }

        // Group by category and pick from different categories first
        $byCategory = collect($available)->groupBy('category');
        $picked = [];
        $categories = $byCategory->keys()->shuffle();

        foreach ($categories as $category) {
            if (count($picked) >= $count) {
                break;
            }
            $items = $byCategory[$category]->shuffle();
            $picked[] = $items->first();
        }

        // If we still need more, pick randomly from remaining
        if (count($picked) < $count) {
            $pickedSlugs = collect($picked)->pluck('full_slug')->toArray();
            $remaining = collect($available)->whereNotIn('full_slug', $pickedSlugs)->shuffle();
            foreach ($remaining as $item) {
                if (count($picked) >= $count) {
                    break;
                }
                $picked[] = $item;
            }
        }

        return $picked;
    }

    // ─── TEMPLATE & DUPLICATE HELPERS ───

    /**
     * Append site-specific custom prompt template with placeholder replacements.
     */
    private function appendSiteTemplate(string $prompt, array $siteInfo): string
    {
        $template = $siteInfo['content_prompt_template'] ?? null;
        if (empty($template)) {
            return $prompt;
        }

        $replacements = [
            '{{domain}}' => $siteInfo['domain'] ?? '',
            '{{brand_name}}' => $siteInfo['brand_name'] ?? $siteInfo['name'] ?? '',
            '{{telegram}}' => $siteInfo['social_links']['telegram'] ?? '',
            '{{login_url}}' => $siteInfo['login_url'] ?? '',
            '{{entry_url}}' => $siteInfo['entry_url'] ?? '',
        ];

        $template = str_replace(array_keys($replacements), array_values($replacements), $template);

        return $prompt . "\n\n## SITE ÖZEL TALİMATLAR:\n" . $template;
    }

    /**
     * Append duplicate prevention instructions with existing titles.
     */
    private function appendDuplicatePrevention(string $prompt, array $existingSlugs): string
    {
        if (empty($existingSlugs)) {
            return $prompt;
        }

        $titles = implode(', ', array_slice($existingSlugs, 0, 50));

        return $prompt . "\n\n## TEKRAR ÖNLEME (ÇOK ÖNEMLİ):\n"
            . "- Aşağıdaki slug'larla aynı veya benzer slug ÜRETME: {$titles}\n"
            . "- Her bölüm farklı kelimelerle başlamalı\n"
            . "- Başlık ve FAQ soruları daha öncekilerden farklı olmalı\n"
            . "- Farklı açılar ve perspektifler kullan";
    }

    // ─── PROMPT BUILDERS ───

    /**
     * Build uniqueness seed for each site to ensure distinct content.
     */
    private function buildUniquenessSeed(string $brandName, string $domain): string
    {
        $toneIndex = crc32($domain) % count(self::TONE_POOL);
        $angleIndex = crc32($brandName . $domain) % count(self::ANGLE_POOL);
        $tone = self::TONE_POOL[$toneIndex];
        $angle = self::ANGLE_POOL[$angleIndex];

        $structureVariations = [
            'H2 başlıklarını soru formatında yaz',
            'H2 başlıklarını eylem odaklı yaz (fiil ile başlat)',
            'H2 başlıklarını "nasıl" ve "neden" ile başlat',
            'H2 başlıklarını kısa ve öz tut (3-5 kelime)',
            'H2 başlıklarını kullanıcı perspektifinden yaz',
            'H2 başlıklarını rakamlarla başlat (5 Adımda, 3 Yöntem gibi)',
        ];
        $structureIndex = crc32($domain . date('Y')) % count($structureVariations);
        $structure = $structureVariations[$structureIndex];

        return <<<SEED

## ÖZGÜNLÜK KURALLARI (ÇOK ÖNEMLİ):
- Yazım tonu: {$tone}
- İçerik açısı: {$angle}
- Başlık stili: {$structure}
- Bu site için tamamen özgün bir yapı ve anlatım kullan
- Başka sitelerdeki benzer içeriklerden farklı ol
- Domain: {$domain}, Brand: {$brandName} — bu siteye özel doğal referanslar ekle
SEED;
    }

    private function buildLandingPagePrompt(string $brandName, string $domain, array $siteInfo, array $existingSlugs = []): string
    {
        $year = date('Y');
        $country = $siteInfo['country'] ?? 'Türkiye';
        $uniqueness = $this->buildUniquenessSeed($brandName, $domain);
        $socialSection = $this->buildSocialLinksSection($siteInfo);

        $existingSlugsText = '';
        if (!empty($existingSlugs)) {
            $slugList = implode(', ', array_slice($existingSlugs, 0, 50));
            $existingSlugsText = "\n\n## MEVCUT İÇERİKLER (TEKRARLAMA):\nBu konularda zaten içerik var, aynı konuları ele alma, farklı açılar bul: {$slugList}";
        }

        $slug = Str::slug($brandName);
        $clusterLinks = "- /{$slug}-giris/ (giriş sayfası)\n- /{$slug}-bonus/ (bonus sayfası)\n- /{$slug}-mobil-giris/ (mobil giriş)\n- /{$slug}-guncel-adres/ (güncel adres)\n- /{$slug}-yeni-giris/ (yeni giriş)";

        return <<<PROMPT
# PARAVAN SEO CONTENT AUTOMATION

Sen profesyonel bir SEO içerik uzmanısın. Aşağıdaki değişkenlere göre ana landing page içeriği üreteceksin.

VARIABLES:
BRAND_NAME = {$brandName}
DOMAIN = {$domain}
YEAR = {$year}
COUNTRY_TARGET = {$country}
{$uniqueness}

## KURALLAR (ÇOK ÖNEMLİ):
- Keyword stuffing YAPMA
- Marka tekrarını abartma (paragraf başına max 1 kez)
- Her paragraf özgün olsun, cümle tekrarı YAPMA
- Spam algısı oluşturma
- Gerçekçi ve kurumsal ton kullan
- Google Helpful Content uyumlu yaz
- Clickbait başlık kullanma, abartılı vaat yazma
- İçerik insan yazmış gibi doğal olsun
- AI tespitine yakalanacak kalıplardan kaçın
- Cümle varyasyonu kullan, paragraf uzunlukları değişken olsun
- Heading hiyerarşisine dikkat et (H1 > H2 > H3)

## PRIMARY KEYWORDS:
- {$brandName} giriş
- {$brandName} güncel giriş
- {$brandName} yeni adres
- {$brandName} resmi giriş

## SECONDARY KEYWORDS:
- {$brandName} mobil giriş
- {$brandName} bonus
- {$brandName} kampanya
- {$brandName} giriş sorunu
- {$brandName} adres değişti mi

## İSTENEN İÇERİK YAPISI:

H1: {$brandName} Giriş {$year} – Güncel Adres

INTRO: 3-4 paragraf, featured snippet uyumlu, detaylı giriş

H2 Alt Başlıkları (hepsini kullan, her H2 altında minimum 3 paragraf):
- {$brandName} Güncel Giriş Adresi Nedir?
- {$brandName} Yeni Adres Neden Değişir?
- {$brandName} Giriş Sorunu Çözümü
- {$brandName} Adım Adım Giriş Rehberi
- Mobilde {$brandName} Giriş
- {$brandName} Bonus ve Kampanyalar
- {$brandName} Güvenli Giriş Rehberi
- {$brandName} Avantaj ve Dezavantajları

## ZORUNLU EK BÖLÜMLER:
1. **Karşılaştırma Tablosu**: HTML <table> ile özellik karşılaştırma tablosu (en az 5 satır, 3 sütun)
2. **Artı/Eksi Listesi**: <div class="pros-cons"> içinde artılar ve eksiler listesi
3. **Adım Adım Görsel Rehber**: Numaralı adımlarla giriş yapma rehberi (<ol> ile)
4. **Kullanıcı Yorumları Bölümü**: <div class="user-reviews"> içinde 3-4 örnek kullanıcı yorumu

## İÇ LİNK YAPISI:
İçerikte doğal anchor text ile şu sayfalara link ver:
{$clusterLinks}
Linkleri <a href="/slug">anchor text</a> formatında ekle.
{$socialSection}
{$existingSlugsText}

Toplam kelime sayısı: **2500-3500 kelime** (bu çok önemli, kısa yazma!)

## FAQ SECTION (15 soru-cevap):
Kısa ve net cevaplı. Her cevap 2-3 cümle.
Sorular kullanıcının gerçekten arayacağı sorular olsun.
Farklı kategorilerden soru seç (giriş, bonus, mobil, ödeme, güvenlik).

## FOOTER LEGAL TEXT:
7258 sayılı kanun hakkında kısa, nötr bilgilendirme ekle.
Yasal yönlendirme yapma. Teşvik edici dil kullanma.

## FORMAT:
Yanıtını MUTLAKA aşağıdaki JSON formatında ver. Başka hiçbir şey yazma.

{
  "title": "H1 başlığı (50-60 karakter, ana kelime başta)",
  "slug": "anasayfa",
  "content": "<h1>...</h1><p>...</p><h2>...</h2><table>...</table>... şeklinde tam HTML içerik. Karşılaştırma tablosu, artı/eksi listesi, adım adım rehber, kullanıcı yorumları bölümlerini dahil et. FAQ bölümünü de HTML içinde <h2>Sıkça Sorulan Sorular</h2> altında yaz. Legal text'i de en sona ekle. Inline stil KULLANMA.",
  "meta_title": "SEO title (50-60 karakter, ana kelime başta, spam görünmemeli)",
  "meta_description": "Meta description (140-160 karakter, doğal CTA içermeli)",
  "meta_keywords": "{$brandName} giriş, {$brandName} güncel giriş, {$brandName} yeni adres",
  "excerpt": "Kısa özet (max 200 karakter)",
  "seo_titles": ["5 adet alternatif SEO title (50-60 karakter)"],
  "meta_descriptions": ["5 adet alternatif meta description (140-160 karakter)"],
  "faq_schema": [{"question": "Soru?", "answer": "Cevap."}]
}
PROMPT;
    }

    private function buildClusterArticlePrompt(string $brandName, string $domain, string $articleTopic, string $articleInstructions, array $siteInfo, array $clusterSlugs = [], array $existingSlugs = []): string
    {
        $year = date('Y');
        $country = $siteInfo['country'] ?? 'Türkiye';
        $uniqueness = $this->buildUniquenessSeed($brandName, $domain);
        $socialSection = $this->buildSocialLinksSection($siteInfo);

        $slug = Str::slug($brandName);

        // Build internal link targets
        $defaultLinks = [
            "/{$slug}-giris/" => 'ana giriş sayfası',
            "/{$slug}-bonus/" => 'bonus sayfası',
            "/{$slug}-mobil-giris/" => 'mobil giriş',
        ];

        if (!empty($clusterSlugs)) {
            foreach (array_slice($clusterSlugs, 0, 5) as $cs) {
                $defaultLinks["/{$cs}/"] = $cs;
            }
        }

        $linksList = '';
        foreach ($defaultLinks as $href => $desc) {
            $linksList .= "- {$href} ({$desc})\n";
        }

        $existingSlugsText = '';
        if (!empty($existingSlugs)) {
            $slugList = implode(', ', array_slice($existingSlugs, 0, 30));
            $existingSlugsText = "\n\n## MEVCUT İÇERİKLER (TEKRARLAMA):\nBu konularda zaten içerik var: {$slugList}. Aynı konuları tekrarlama, farklı açılardan yaz.";
        }

        return <<<PROMPT
# SEO CLUSTER ARTICLE GENERATION

Sen profesyonel bir SEO içerik uzmanısın. Aşağıdaki konuda destek blog yazısı üreteceksin.

BRAND_NAME = {$brandName}
DOMAIN = {$domain}
YEAR = {$year}
COUNTRY_TARGET = {$country}
{$uniqueness}

KONU: {$articleTopic}
TALİMATLAR: {$articleInstructions}

## KURALLAR:
- Keyword stuffing YAPMA
- Marka adını doğal şekilde kullan (paragraf başına max 1)
- Her paragraf özgün, tekrar yok
- Gerçekçi ve bilgilendirici ton
- Google Helpful Content uyumlu
- AI tespitine yakalanacak kalıplardan kaçın
- Cümle yapıları ve paragraf uzunlukları değişken olsun

## YAPI:
- H1 başlık
- 3-4 paragraflık giriş
- 5-6 adet H2 alt başlık
- Her H2 altında minimum 2-3 paragraf
- Toplam: **1500-2200 kelime** (bu çok önemli, kısa yazma!)

## ZORUNLU EK BÖLÜMLER:
1. **HTML Tablo**: Konuyla ilgili karşılaştırma veya bilgi tablosu (<table> ile, en az 4 satır)
2. **Sıralı Liste**: Adım adım işlem veya öneriler (<ol> ile)
3. **Bilgi Kutusu**: Önemli bir ipucu veya uyarı (<div class="info-box"><strong>Bilgi:</strong> ...</div>)

## İÇ LİNK YAPISI (2-3 iç link zorunlu):
İçerikte doğal anchor text ile şu sayfalara link ver:
{$linksList}
Linkleri <a href="/slug">anchor text</a> formatında ekle.

## FAQ SECTION (8-10 soru-cevap):
Kısa ve net cevaplı. Her cevap 2-3 cümle.
Sorular gerçekçi ve kullanıcı odaklı olsun.
{$socialSection}
{$existingSlugsText}

## FORMAT:
{
  "title": "Yazı başlığı (50-65 karakter)",
  "slug": "url-friendly-slug",
  "content": "Tam HTML içerik (h1, h2, h3, p, ul, ol, li, strong, a, table, div etiketleri). Tablo, sıralı liste ve bilgi kutusu dahil. FAQ dahil. Inline stil KULLANMA.",
  "meta_title": "SEO title (50-60 karakter)",
  "meta_description": "Meta description (140-160 karakter)",
  "excerpt": "Kısa özet (max 200 karakter)",
  "faq_schema": [{"question": "?", "answer": "."}]
}
PROMPT;
    }

    private function buildDailyPostPrompt(string $brandName, string $domain, string $topic, string $instructions, array $existingSlugs = [], array $siteInfo = []): string
    {
        $year = date('Y');
        $month = $this->getTurkishMonth();
        $socialSection = $this->buildSocialLinksSection($siteInfo);

        // Pick random tone and angle for variety
        $tone = self::TONE_POOL[array_rand(self::TONE_POOL)];
        $angle = self::ANGLE_POOL[array_rand(self::ANGLE_POOL)];

        $slug = Str::slug($brandName);
        $internalLinks = "- /{$slug}-giris/ (ana giriş)\n- /{$slug}-bonus/ (bonus)\n- /{$slug}-mobil-giris/ (mobil giriş)\n- /anasayfa (ana sayfa)";

        $existingSlugsText = '';
        if (!empty($existingSlugs)) {
            $slugList = implode(', ', array_slice($existingSlugs, 0, 30));
            $existingSlugsText = "\n\nMEVCUT İÇERİKLER (bunları tekrarlama): {$slugList}";
        }

        $topicWithBrand = str_replace('{brand}', $brandName, $topic);
        $instructionsWithBrand = str_replace('{brand}', $brandName, $instructions);

        return <<<PROMPT
# GÜNLÜK BLOG POST ÜRETİMİ

Sen profesyonel bir SEO içerik uzmanısın. Günlük taze blog yazısı üreteceksin.

BRAND_NAME = {$brandName}
DOMAIN = {$domain}
YEAR = {$year}
CURRENT_DATE_REF = {$month} {$year} güncel

KONU: {$topicWithBrand}
TALİMATLAR: {$instructionsWithBrand}

## YAZIM TONU VE AÇISI:
- Ton: {$tone}
- Açı: {$angle}
- "{$month} {$year} güncel" ifadesini doğal şekilde içeriğe yerleştir
- Güncel ve taze bir içerik izlenimi ver

## KURALLAR:
- Keyword stuffing YAPMA
- Marka adını doğal şekilde kullan (paragraf başına max 1)
- Her paragraf özgün, tekrar yok
- Google Helpful Content uyumlu
- AI tespitine yakalanacak kalıplardan kaçın
- Cümle yapıları ve paragraf uzunlukları değişken olsun
- İnsan yazmış gibi doğal olsun

## YAPI:
- H1 başlık (güncellik referansı içermeli)
- 2-3 paragraflık giriş
- 4-5 adet H2 alt başlık
- Her H2 altında 2-3 paragraf
- Toplam: **1200-1800 kelime**

## ZORUNLU EK BÖLÜMLER:
1. **Bilgi Kutusu**: <div class="info-box"><strong>Bilgi:</strong> ...</div>
2. **Sıralı Liste veya Tablo**: Konuya uygun HTML <table> veya <ol>

## İÇ LİNK YAPISI (2-3 iç link zorunlu):
{$internalLinks}
Linkleri <a href="/slug">doğal anchor text</a> formatında ekle.

## FAQ SECTION (6-8 soru-cevap):
Kısa ve net cevaplı. Gerçekçi sorular.
{$socialSection}
{$existingSlugsText}

## FORMAT:
{
  "title": "Yazı başlığı (50-65 karakter)",
  "slug": "url-friendly-slug",
  "content": "Tam HTML içerik. Bilgi kutusu ve tablo/liste dahil. FAQ dahil. Inline stil KULLANMA.",
  "meta_title": "SEO title (50-60 karakter, güncellik referansı)",
  "meta_description": "Meta description (140-160 karakter)",
  "excerpt": "Kısa özet (max 200 karakter)",
  "faq_schema": [{"question": "?", "answer": "."}],
  "featured_image_prompt": "Bu içerik için uygun, profesyonel, metin içermeyen dijital illüstrasyon prompt'u (İngilizce, DALL-E 3 için)"
}
PROMPT;
    }

    private function buildStaticPagePrompt(string $brandName, string $domain, string $pageType, string $pageInstructions, array $siteInfo): string
    {
        $year = date('Y');

        return <<<PROMPT
# STATIC PAGE GENERATION

Sen profesyonel bir web içerik yazarısın.

BRAND_NAME = {$brandName}
DOMAIN = {$domain}
YEAR = {$year}
PAGE_TYPE = {$pageType}

TALİMATLAR: {$pageInstructions}

## KURALLAR:
- Kurumsal ve profesyonel ton
- SEO uyumlu ama doğal
- Spam içermeyen, kaliteli içerik
- 400-700 kelime

## FORMAT:
{
  "title": "Sayfa başlığı",
  "slug": "sayfa-slug",
  "content": "HTML içerik (h1, h2, p, ul, li, strong etiketleri). Inline stil KULLANMA.",
  "meta_title": "SEO title (50-60 karakter)",
  "meta_description": "Meta description (140-160 karakter)",
  "excerpt": "Kısa özet (max 200 karakter)"
}
PROMPT;
    }

    private function buildGeneratePrompt(string $type, string $topic, string $instructions, array $siteInfo): string
    {
        $siteName = $siteInfo['site_name'] ?? '';
        $siteType = $siteInfo['site_type'] ?? '';
        $country = $siteInfo['country'] ?? 'Türkiye';
        $pageType = $siteInfo['page_type'] ?? $type;
        $keywords = $siteInfo['keywords'] ?? $topic;

        return <<<PROMPT
Aşağıdaki bilgiler doğrultusunda SEO uyumlu, özgün ve doğal bir web sayfası içeriği üret.

ÖNEMLİ KURALLAR:
- İçerik doğal olmalı.
- Anahtar kelime spam yapma.
- Yapay veya tekrar eden kalıplar kullanma.
- Paragraf yapıları birbirine benzemesin.
- İnsan tarafından yazılmış gibi akıcı olsun.
- Gereksiz abartılı pazarlama dili kullanma.
- İçeriği blok yapısında üret.

SITE BİLGİLERİ:
Site Adı: {$siteName}
Site Türü: {$siteType}
Hedef Ülke: {$country}
Dil: Türkçe

SAYFA BİLGİLERİ:
Sayfa Türü: {$pageType}
Sayfa Başlığı: {$topic}
Ana Anahtar Kelimeler: {$keywords}

İÇERİK YAPISI ŞÖYLE OLMALI:

1) Giriş Paragrafı
- 2–3 paragraf
- Konuyu tanıtan doğal açıklama

2) Alt Başlık 1 (H2)
- Açıklayıcı paragraf

3) Alt Başlık 2 (H2)
- Liste veya madde işaretleri olabilir

4) Artılar ve Eksiler Bölümü
- Madde işaretli liste

5) Sıkça Sorulan Sorular (En az 4 soru-cevap)
- Gerçekçi ve açıklayıcı cevaplar

6) Kapanış Paragrafı

{$instructions}

FORMAT: Yanıtını MUTLAKA aşağıdaki JSON formatında ver. Başka hiçbir şey yazma.
{
  "title": "Sayfa başlığı",
  "slug": "sayfa-slug-url-friendly",
  "content": "<h2>...</h2><p>...</p> şeklinde HTML içerik (inline stil kullanma, sadece h2, p, ul, li, strong, em etiketleri)",
  "meta_title": "SEO meta title (max 60 karakter)",
  "meta_description": "SEO meta description (max 160 karakter)",
  "excerpt": "Kısa özet (max 200 karakter)"
}

Özgünlük önemli. Her içerik diğerinden farklı yapı ve anlatım stiline sahip olsun.
PROMPT;
    }

    private function buildSpinPrompt(array $original, string $instructions): string
    {
        $title = $original['title'] ?? '';
        $content = $original['content'] ?? '';

        return <<<PROMPT
Aşağıdaki içeriği tamamen yeniden yaz. Aynı konuyu ele al ama:
- Farklı cümle yapıları kullan
- Farklı paragraf düzeni oluştur
- Farklı kelime seçimleri yap
- Özgün ve doğal olsun
- SEO uyumlu kalsın
- Türkçe yaz

Orijinal Başlık: {$title}
Orijinal İçerik: {$content}

{$instructions}

FORMAT: Yanıtını MUTLAKA aşağıdaki JSON formatında ver. Başka hiçbir şey yazma.
{
  "title": "Yeni başlık",
  "slug": "yeni-slug-url-friendly",
  "content": "<h2>...</h2><p>...</p> şeklinde HTML içerik (inline stil kullanma)",
  "meta_title": "SEO meta title (max 60 karakter)",
  "meta_description": "SEO meta description (max 160 karakter)",
  "excerpt": "Kısa özet (max 200 karakter)"
}
PROMPT;
    }

    // ─── AI CALLERS ───

    private function callAI(string $prompt, int $maxTokens = 0): array
    {
        $provider = config('ai.provider', 'openai');
        if ($maxTokens === 0) {
            $maxTokens = (int) config("ai.{$provider}.max_tokens", 4096);
        }

        $response = match ($provider) {
            'anthropic' => $this->callAnthropic($prompt, $maxTokens),
            default => $this->callOpenAI($prompt, $maxTokens),
        };

        return $this->parseJsonResponse($response);
    }

    private function callOpenAI(string $prompt, int $maxTokens = 4096): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ai.openai.api_key'),
        ])->timeout(180)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('ai.openai.model'),
            'max_tokens' => $maxTokens,
            'messages' => [
                ['role' => 'system', 'content' => 'Sen profesyonel bir SEO uzmanı ve Türkçe içerik yazarısın. Google Helpful Content güncellemelerine uygun, doğal ve özgün içerik üretirsin. Yanıtlarını her zaman geçerli JSON formatında ver. Markdown veya code block KULLANMA, sadece düz JSON ver.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $response->throw();

        return $response->json('choices.0.message.content', '{}');
    }

    private function callAnthropic(string $prompt, int $maxTokens = 4096): string
    {
        $response = Http::withHeaders([
            'x-api-key' => config('ai.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(180)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('ai.anthropic.model'),
            'max_tokens' => $maxTokens,
            'system' => 'Sen profesyonel bir SEO uzmanı ve Türkçe içerik yazarısın. Google Helpful Content güncellemelerine uygun, doğal ve özgün içerik üretirsin. Yanıtlarını her zaman geçerli JSON formatında ver.',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $response->throw();

        return $response->json('content.0.text', '{}');
    }

    // ─── HELPERS ───

    private function parseJsonResponse(string $response): array
    {
        // Try to extract JSON from the response
        $decoded = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->validateContentStructure($decoded);
        }

        // Try to find JSON block in the response
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->validateContentStructure($decoded);
            }
        }

        throw new \RuntimeException('AI response could not be parsed as JSON: ' . substr($response, 0, 500));
    }

    private function validateContentStructure(array $data): array
    {
        $required = ['title', 'content'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \RuntimeException("AI response missing required field: {$field}");
            }
        }

        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'content' => $data['content'],
            'meta_title' => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? '',
            'meta_keywords' => $data['meta_keywords'] ?? '',
            'excerpt' => $data['excerpt'] ?? '',
            'seo_titles' => $data['seo_titles'] ?? [],
            'meta_descriptions' => $data['meta_descriptions'] ?? [],
            'faq_schema' => $data['faq_schema'] ?? [],
            'featured_image_prompt' => $data['featured_image_prompt'] ?? '',
        ];
    }

    /**
     * Format social links for inclusion in AI prompts.
     */
    private function buildSocialLinksSection(array $siteInfo): string
    {
        $socialLinks = $siteInfo['social_links'] ?? [];
        if (empty($socialLinks)) {
            return '';
        }

        $lines = [];
        $labels = [
            'telegram' => 'Telegram',
            'instagram' => 'Instagram',
            'x' => 'X (Twitter)',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'support_email' => 'E-posta',
        ];

        foreach ($socialLinks as $key => $url) {
            if (!empty($url) && isset($labels[$key])) {
                $lines[] = "- {$labels[$key]}: {$url}";
            }
        }

        if (empty($lines)) {
            return '';
        }

        $linkList = implode("\n", $lines);

        return <<<SECTION

## SOSYAL MEDYA VE İLETİŞİM BİLGİLERİ:
İçerikte doğal şekilde şu sosyal medya/iletişim bilgilerini yer yer belirt:
{$linkList}
Özellikle giriş sorunu, destek ve güncel adres bölümlerinde bu kanalları referans göster.
Telegram ve WhatsApp linklerini "güncel adres buradan takip edilebilir" gibi doğal cümlelerle kullan.
SECTION;
    }

    private function getTurkishMonth(): string
    {
        $months = [
            1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
            5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
            9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
        ];

        return $months[(int) date('n')];
    }

    private function slugify(string $text): string
    {
        return Str::slug($text);
    }
}
