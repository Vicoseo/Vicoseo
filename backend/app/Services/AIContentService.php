<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIContentService
{
    /**
     * Generate new content using AI.
     */
    public function generateContent(string $type, string $topic, string $instructions, array $siteInfo = []): array
    {
        $prompt = $this->buildGeneratePrompt($type, $topic, $instructions, $siteInfo);

        return $this->callAI($prompt);
    }

    /**
     * Spin existing content into a new unique version.
     */
    public function spinContent(array $original, string $instructions): array
    {
        $prompt = $this->buildSpinPrompt($original, $instructions);

        return $this->callAI($prompt);
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

    private function callAI(string $prompt): array
    {
        $provider = config('ai.provider', 'openai');

        $response = match ($provider) {
            'anthropic' => $this->callAnthropic($prompt),
            default => $this->callOpenAI($prompt),
        };

        return $this->parseJsonResponse($response);
    }

    private function callOpenAI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ai.openai.api_key'),
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('ai.openai.model'),
            'max_tokens' => config('ai.openai.max_tokens'),
            'messages' => [
                ['role' => 'system', 'content' => 'Sen bir SEO uzmanı ve Türkçe içerik yazarısın. Yanıtlarını her zaman geçerli JSON formatında ver.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $response->throw();

        return $response->json('choices.0.message.content', '{}');
    }

    private function callAnthropic(string $prompt): string
    {
        $response = Http::withHeaders([
            'x-api-key' => config('ai.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('ai.anthropic.model'),
            'max_tokens' => config('ai.anthropic.max_tokens'),
            'system' => 'Sen bir SEO uzmanı ve Türkçe içerik yazarısın. Yanıtlarını her zaman geçerli JSON formatında ver.',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $response->throw();

        return $response->json('content.0.text', '{}');
    }

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
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['title']),
            'content' => $data['content'],
            'meta_title' => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? '',
            'excerpt' => $data['excerpt'] ?? '',
        ];
    }
}
