<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Sitemap Index — lists all sub-sitemaps.
     * GET /sitemap.xml
     */
    public function index(Request $request): Response
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');
        $baseUrl = "https://{$site->domain}";

        $postLastMod = Post::published()->max('updated_at');
        $pageLastMod = Page::published()->max('updated_at');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if ($pageLastMod) {
            $xml .= '<sitemap>';
            $xml .= "<loc>{$baseUrl}/page-sitemap.xml</loc>";
            $xml .= '<lastmod>' . $pageLastMod->toW3cString() . '</lastmod>';
            $xml .= '</sitemap>';
        }

        if ($postLastMod) {
            $xml .= '<sitemap>';
            $xml .= "<loc>{$baseUrl}/post-sitemap.xml</loc>";
            $xml .= '<lastmod>' . $postLastMod->toW3cString() . '</lastmod>';
            $xml .= '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600, s-maxage=3600',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * Page Sitemap — all published static pages.
     * GET /page-sitemap.xml
     */
    public function pages(Request $request): Response
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');
        $baseUrl = "https://{$site->domain}";

        $pages = Page::published()
            ->ordered()
            ->select(['slug', 'updated_at'])
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $homePage = $pages->firstWhere('slug', 'anasayfa');
        $xml .= '<url>';
        $xml .= "<loc>{$baseUrl}/</loc>";
        $xml .= '<lastmod>' . ($homePage ? $homePage->updated_at->toW3cString() : now()->toW3cString()) . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Other pages
        foreach ($pages as $page) {
            if ($page->slug === 'anasayfa') {
                continue;
            }

            $xml .= '<url>';
            $xml .= "<loc>{$baseUrl}/{$page->slug}</loc>";
            $xml .= '<lastmod>' . $page->updated_at->toW3cString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600, s-maxage=3600',
        ]);
    }

    /**
     * Post Sitemap — all published blog posts.
     * GET /post-sitemap.xml
     */
    public function posts(Request $request): Response
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');
        $baseUrl = "https://{$site->domain}";

        $posts = Post::published()
            ->latest()
            ->select(['slug', 'updated_at', 'published_at', 'featured_image'])
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        // Blog index page
        $xml .= '<url>';
        $xml .= "<loc>{$baseUrl}/blog</loc>";
        $xml .= '<lastmod>' . ($posts->first()?->updated_at?->toW3cString() ?? now()->toW3cString()) . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';

        // Individual posts
        foreach ($posts as $post) {
            $xml .= '<url>';
            $xml .= "<loc>{$baseUrl}/blog/{$post->slug}</loc>";
            $xml .= '<lastmod>' . $post->updated_at->toW3cString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';

            // Image tag for featured image
            if ($post->featured_image) {
                $imageUrl = str_starts_with($post->featured_image, 'http')
                    ? $post->featured_image
                    : $baseUrl . $post->featured_image;
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars($imageUrl) . '</image:loc>';
                $xml .= '</image:image>';
            }

            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600, s-maxage=3600',
        ]);
    }
}
