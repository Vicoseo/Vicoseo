<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchConsoleController extends Controller
{
    public function __construct(private GoogleSearchConsoleService $gsc) {}

    /**
     * Get search performance summary for a site.
     */
    public function performance(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $siteUrl = 'https://' . $site->domain;

        $period = $request->query('period', '28d');
        [$startDate, $endDate] = $this->parsePeriod($period);

        $summary = $this->gsc->getPerformanceSummary($siteUrl, $startDate, $endDate);
        $queries = $this->gsc->getSearchPerformance($siteUrl, $startDate, $endDate);

        return response()->json([
            'data' => [
                'summary' => $summary,
                'queries' => $queries,
                'period' => $period,
            ],
        ]);
    }

    /**
     * Get sitemaps for a site.
     */
    public function sitemaps(int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $siteUrl = 'https://' . $site->domain;

        $sitemaps = $this->gsc->getSitemaps($siteUrl);

        return response()->json(['data' => $sitemaps]);
    }

    /**
     * Submit sitemap for a site.
     */
    public function submitSitemap(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $siteUrl = 'https://' . $site->domain;

        $sitemapUrl = $request->input('sitemap_url', "https://{$site->domain}/sitemap.xml");

        $result = $this->gsc->submitSitemap($siteUrl, $sitemapUrl);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get page-level data for a site.
     */
    public function pages(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $siteUrl = 'https://' . $site->domain;

        $period = $request->query('period', '28d');
        [$startDate, $endDate] = $this->parsePeriod($period);

        $pages = $this->gsc->getErrors($siteUrl, $startDate, $endDate);

        return response()->json(['data' => $pages]);
    }

    private function parsePeriod(string $period): array
    {
        $end = date('Y-m-d');

        return match ($period) {
            '7d' => [date('Y-m-d', strtotime('-7 days')), $end],
            '90d' => [date('Y-m-d', strtotime('-90 days')), $end],
            default => [date('Y-m-d', strtotime('-28 days')), $end],
        };
    }
}
