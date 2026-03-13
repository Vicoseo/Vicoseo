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
     * Scrape images from a given URL and optionally generate AI content for each.
     */
    public function scrape(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);

        $validated = $request->validate([
            'url' => ['required', 'url'],
            'generate_content' => ['sometimes', 'boolean'],
        ]);

        $url = $validated['url'];
        $generateContent = $validated['generate_content'] ?? false;

        try {
            // Fetch the page HTML
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                ->get($url);

            if (!$response->successful()) {
                return response()->json(['error' => 'URL erişilemedi: ' . $response->status()], 422);
            }

            $html = $response->body();
            $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);

            // Extract images and their alt text / surrounding text
            $images = $this->extractImages($html, $baseUrl);

            if (empty($images)) {
                return response()->json(['error' => 'Sayfada görsel bulunamadı.'], 422);
            }

            // Download images to storage
            $domainSlug = Str::slug($site->domain, '-');
            $downloadedImages = [];
            $downloadErrors = [];

            foreach ($images as $i => $img) {
                try {
                    $imgResponse = Http::timeout(15)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                        ->get($img['src']);

                    if (!$imgResponse->successful()) {
                        $downloadErrors[] = "HTTP {$imgResponse->status()}: {$img['src']}";
                        continue;
                    }

                    $body = $imgResponse->body();
                    if (strlen($body) < 100) {
                        $downloadErrors[] = "Çok küçük dosya ({$body} bytes): {$img['src']}";
                        continue;
                    }

                    $contentType = $imgResponse->header('Content-Type') ?? 'image/jpeg';
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
                    $downloadErrors[] = "{$img['src']}: {$e->getMessage()}";
                    continue;
                }
            }

            if (empty($downloadedImages)) {
                $errorDetail = !empty($downloadErrors)
                    ? ' Hatalar: ' . implode('; ', array_slice($downloadErrors, 0, 5))
                    : '';
                return response()->json([
                    'error' => "Görseller indirilemedi. {$images[0]['src']} gibi " . count($images) . " görsel denendi.{$errorDetail}",
                    'debug' => [
                        'images_found' => count($images),
                        'errors' => $downloadErrors,
                        'first_image_url' => $images[0]['src'] ?? null,
                    ],
                ], 422);
            }

            // Optionally generate AI content for each image
            $generatedPosts = [];
            if ($generateContent) {
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

                foreach ($downloadedImages as $img) {
                    try {
                        $topic = $img['title'] ?: "{$brandName} Görsel İnceleme";
                        $instructions = "Bu görsele dayalı SEO uyumlu bir blog yazısı oluştur. Görsel: {$img['alt']}. Görsel yolu: {$img['path']}";

                        $result = $this->aiContentService->generateDailyPost(
                            $brandName,
                            $domain,
                            $topic,
                            $instructions,
                            $existingSlugs,
                            $siteInfo,
                        );

                        $slug = $result['slug'] ?? Str::slug($result['title'] ?? $topic);

                        // Prevent duplicate slugs
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
                        $generatedPosts[] = [
                            'title' => $result['title'],
                            'slug' => $slug,
                            'image' => $img['path'],
                        ];
                    } catch (\Throwable $e) {
                        $generatedPosts[] = [
                            'title' => $img['title'],
                            'error' => $e->getMessage(),
                            'image' => $img['path'],
                        ];
                    }
                }
            }

            return response()->json([
                'message' => count($downloadedImages) . ' görsel indirildi.',
                'images' => $downloadedImages,
                'posts' => $generatedPosts,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Image scrape error', [
                'site_id' => $siteId,
                'url' => $url ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json([
                'error' => 'Scraping hatası: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()) . ':' . $e->getLine(),
                ],
            ], 500);
        }
    }

    /**
     * Extract image URLs and alt text from HTML.
     */
    private function extractImages(string $html, string $baseUrl): array
    {
        $images = [];
        $seen = [];

        // Match <img> tags
        preg_match_all('/<img[^>]+>/i', $html, $matches);

        foreach ($matches[0] as $imgTag) {
            $src = null;
            $alt = '';

            // Try data-src first (lazy loading), then src
            if (preg_match('/data-src=["\']([^"\']+)["\']/i', $imgTag, $m)) {
                $src = $m[1];
            } elseif (preg_match('/src=["\']([^"\']+)["\']/i', $imgTag, $m)) {
                $src = $m[1];
            }

            if (preg_match('/alt=["\']([^"\']*?)["\']/i', $imgTag, $m)) {
                $alt = $m[1];
            }

            if (!$src) {
                continue;
            }

            // Skip tiny icons, tracking pixels, data URIs
            if (str_starts_with($src, 'data:') || preg_match('/\.(svg|ico)(\?|$)/i', $src)) {
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

            // Deduplicate
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
}
