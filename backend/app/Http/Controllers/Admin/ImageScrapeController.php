<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\AIContentService;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageScrapeController extends Controller
{
    public function __construct(
        private TenantManager $tenantManager,
        private AIContentService $aiContentService,
    ) {}

    /**
     * Scrape images from a URL.
     *
     * Mode 1 (default): Fetch page HTML, find images related to the site's brand name
     * Mode 2 (wp_api): Use WordPress REST API to get posts with featured images
     * Mode 3 (keyword): Search for images matching a custom keyword
     */
    public function scrape(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);

        $validated = $request->validate([
            'url' => ['required', 'url'],
            'keyword' => ['nullable', 'string', 'max:100'],
            'max_images' => ['nullable', 'integer', 'min:1', 'max:50'],
            'generate_content' => ['sometimes', 'boolean'],
        ]);

        $url = $validated['url'];
        $keyword = $validated['keyword'] ?? $this->extractBrandKeyword($site->name);
        $maxImages = $validated['max_images'] ?? 20;
        $generateContent = $validated['generate_content'] ?? false;

        try {
            // Ensure storage directory exists
            $domainSlug = Str::slug($site->domain, '-');
            $storagePath = storage_path("app/public/posts/{$domainSlug}");
            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0775, true);
                chown($storagePath, 'www-data');
            }

            // Try WordPress REST API first
            $images = $this->tryWordPressApi($url, $keyword, $maxImages);

            // Fallback to HTML scraping
            if (empty($images)) {
                $images = $this->scrapeHtml($url, $keyword, $maxImages);
            }

            if (empty($images)) {
                return response()->json([
                    'error' => "Sayfada \"{$keyword}\" ile ilgili görsel bulunamadı. Farklı bir URL veya anahtar kelime deneyin.",
                ], 422);
            }

            // Download images
            $downloadedImages = [];
            $downloadErrors = [];

            foreach ($images as $i => $img) {
                if (count($downloadedImages) >= $maxImages) {
                    break;
                }

                try {
                    $imgResponse = Http::timeout(20)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                            'Referer' => $url,
                        ])
                        ->get($img['src']);

                    if (!$imgResponse->successful()) {
                        $downloadErrors[] = "HTTP {$imgResponse->status()}: " . basename($img['src']);
                        continue;
                    }

                    $body = $imgResponse->body();
                    if (strlen($body) < 500) {
                        continue; // Skip tiny files (tracking pixels etc.)
                    }

                    $contentType = $imgResponse->header('Content-Type') ?? '';
                    $ext = match (true) {
                        str_contains($contentType, 'png') => 'png',
                        str_contains($contentType, 'webp') => 'webp',
                        str_contains($contentType, 'gif') => 'gif',
                        default => 'jpg',
                    };

                    $filename = "scraped-{$i}.{$ext}";
                    $path = "posts/{$domainSlug}/{$filename}";

                    Storage::disk('public')->put($path, $body);

                    $downloadedImages[] = [
                        'path' => "/storage/{$path}",
                        'alt' => $img['alt'],
                        'title' => $img['title'] ?? $img['alt'],
                        'original_src' => $img['src'],
                    ];
                } catch (\Throwable $e) {
                    $downloadErrors[] = basename($img['src']) . ': ' . Str::limit($e->getMessage(), 80);
                }
            }

            if (empty($downloadedImages)) {
                return response()->json([
                    'error' => 'Görseller indirilemedi.',
                    'details' => array_slice($downloadErrors, 0, 5),
                ], 422);
            }

            // Generate AI content for each image if requested
            $generatedPosts = [];
            if ($generateContent) {
                $generatedPosts = $this->generatePostsForImages($site, $downloadedImages);
            }

            return response()->json([
                'message' => count($downloadedImages) . ' görsel indirildi.' .
                    (!empty($generatedPosts) ? ' ' . count(array_filter($generatedPosts, fn($p) => empty($p['error']))) . ' yazı oluşturuldu.' : ''),
                'images' => $downloadedImages,
                'posts' => $generatedPosts,
                'keyword' => $keyword,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Image scrape error', [
                'site_id' => $siteId,
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getFile() . ':' . $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Scraping hatası: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Try WordPress REST API to get posts with featured images.
     */
    private function tryWordPressApi(string $url, string $keyword, int $maxImages): array
    {
        $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
        $apiUrl = $baseUrl . '/wp-json/wp/v2/posts?_embed&per_page=' . min($maxImages, 20);

        // Add search filter if keyword provided
        if ($keyword) {
            $apiUrl .= '&search=' . urlencode($keyword);
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get($apiUrl);

            if (!$response->successful()) {
                return [];
            }

            $posts = $response->json();
            if (!is_array($posts)) {
                return [];
            }

            $images = [];
            foreach ($posts as $post) {
                $featuredUrl = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
                $title = html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');

                if ($featuredUrl && $title) {
                    $images[] = [
                        'src' => $featuredUrl,
                        'alt' => $title,
                        'title' => $title,
                        'wp_post' => true,
                    ];
                }
            }

            return $images;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Scrape HTML for images related to keyword.
     */
    private function scrapeHtml(string $url, string $keyword, int $maxImages): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])
            ->get($url);

        if (!$response->successful()) {
            return [];
        }

        $html = $response->body();
        $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);

        $allImages = $this->extractImagesFromHtml($html, $baseUrl);

        if (empty($allImages)) {
            return [];
        }

        // Filter: images related to keyword (in alt, src, or nearby text)
        $keywordLower = mb_strtolower($keyword);
        $keywordVariants = $this->generateKeywordVariants($keyword);

        $relevant = [];
        $other = [];

        foreach ($allImages as $img) {
            $haystack = mb_strtolower($img['alt'] . ' ' . $img['src'] . ' ' . ($img['context'] ?? ''));

            $isRelevant = false;
            foreach ($keywordVariants as $variant) {
                if (str_contains($haystack, $variant)) {
                    $isRelevant = true;
                    break;
                }
            }

            if ($isRelevant) {
                $relevant[] = $img;
            } else {
                $other[] = $img;
            }
        }

        // Return relevant first, then fill with others if needed
        $result = $relevant;
        if (count($result) < $maxImages && !empty($other)) {
            // Add non-relevant images that look like content images (not icons/banners)
            foreach ($other as $img) {
                if (count($result) >= $maxImages) {
                    break;
                }
                $result[] = $img;
            }
        }

        return array_slice($result, 0, $maxImages);
    }

    /**
     * Extract images from HTML.
     */
    private function extractImagesFromHtml(string $html, string $baseUrl): array
    {
        $images = [];
        $seen = [];

        preg_match_all('/<img[^>]+>/i', $html, $matches);

        foreach ($matches[0] as $imgTag) {
            $src = null;
            $alt = '';

            // Try data-src (lazy loading), data-lazy-src, then src
            foreach (['data-src', 'data-lazy-src', 'src'] as $attr) {
                if (preg_match("/{$attr}=[\"']([^\"']+)[\"']/i", $imgTag, $m)) {
                    $src = $m[1];
                    break;
                }
            }

            if (preg_match('/alt=["\']([^"\']*?)["\'/]/i', $imgTag, $m)) {
                $alt = $m[1];
            }

            if (!$src) {
                continue;
            }

            // Skip data URIs, SVGs, ICOs, tiny tracking pixels
            if (str_starts_with($src, 'data:') || preg_match('/\.(svg|ico)(\?|$)/i', $src)) {
                continue;
            }

            // Skip common non-content images
            if (preg_match('/(gravatar|avatar|emoji|smilies|wp-includes|ad-|banner-ad|pixel|tracking)/i', $src)) {
                continue;
            }

            // Make absolute URL
            if (str_starts_with($src, '//')) {
                $src = 'https:' . $src;
            } elseif (str_starts_with($src, '/')) {
                $src = $baseUrl . $src;
            } elseif (!str_starts_with($src, 'http')) {
                $src = $baseUrl . '/' . $src;
            }

            $key = md5($src);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $images[] = [
                'src' => $src,
                'alt' => $alt ?: 'Görsel',
                'title' => $alt ?: null,
            ];
        }

        return $images;
    }

    /**
     * Generate keyword variants for matching (handles Turkish chars, common patterns).
     */
    private function generateKeywordVariants(string $keyword): array
    {
        $lower = mb_strtolower($keyword);
        $variants = [$lower];

        // Without spaces
        $variants[] = str_replace(' ', '', $lower);

        // With dash
        $variants[] = str_replace(' ', '-', $lower);

        // Common brand name patterns (e.g., "Prensbet" -> "prens-bet", "prens bet")
        if (preg_match('/^(.+?)(bet|casino|bahis|slot|poker)$/i', $lower, $m)) {
            $variants[] = $m[1] . ' ' . $m[2];
            $variants[] = $m[1] . '-' . $m[2];
            $variants[] = $m[1];
        }

        return array_unique($variants);
    }

    /**
     * Extract the core brand keyword from site name.
     */
    private function extractBrandKeyword(string $siteName): string
    {
        // Remove common suffixes like "Giriş", "Online", "Bet" domain parts
        $name = preg_replace('/\s*(giriş|giris|online|güncel|yeni)\s*/i', '', $siteName);

        return trim($name) ?: $siteName;
    }

    /**
     * Generate AI blog posts for each downloaded image.
     */
    private function generatePostsForImages(Site $site, array $images): array
    {
        $this->tenantManager->connectToTenant($site);
        $brandName = $site->name;
        $domain = $site->domain;

        $existingSlugs = \DB::connection('tenant')
            ->table('posts')
            ->pluck('slug')
            ->toArray();

        $siteInfo = [
            'domain' => $domain,
            'name' => $brandName,
            'brand_name' => $brandName,
            'content_prompt_template' => $site->content_prompt_template,
            'social_links' => $site->social_links ?? [],
            'login_url' => $site->login_url ?? '',
            'entry_url' => $site->entry_url ?? '',
        ];

        $posts = [];

        foreach ($images as $img) {
            try {
                $topic = $img['title'] ?: "{$brandName} Görsel İnceleme";
                $instructions = "Bu görsele dayalı SEO uyumlu bir blog yazısı oluştur. Görsel başlığı: \"{$img['alt']}\". Featured image olarak \"{$img['path']}\" kullanılacak.";

                $result = $this->aiContentService->generateDailyPost(
                    $brandName,
                    $domain,
                    $topic,
                    $instructions,
                    $existingSlugs,
                    $siteInfo,
                );

                $slug = $result['slug'] ?? Str::slug($result['title'] ?? $topic);

                if (in_array($slug, $existingSlugs)) {
                    $slug .= '-' . Str::random(4);
                }

                \DB::connection('tenant')->table('posts')->insert([
                    'slug' => $slug,
                    'title' => $result['title'] ?? $topic,
                    'excerpt' => $result['excerpt'] ?? '',
                    'content' => $result['content'] ?? '',
                    'featured_image' => $img['path'],
                    'meta_title' => $result['meta_title'] ?? '',
                    'meta_description' => $result['meta_description'] ?? '',
                    'is_published' => true,
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $existingSlugs[] = $slug;
                $posts[] = [
                    'title' => $result['title'],
                    'slug' => $slug,
                    'image' => $img['path'],
                ];
            } catch (\Throwable $e) {
                $posts[] = [
                    'title' => $img['title'] ?? 'Hata',
                    'error' => Str::limit($e->getMessage(), 100),
                    'image' => $img['path'],
                ];
            }
        }

        return $posts;
    }
}
