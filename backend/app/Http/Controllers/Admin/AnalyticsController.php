<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private GoogleAnalyticsService $analytics,
        private GoogleSearchConsoleService $gsc,
    ) {}

    /**
     * Get analytics data for a specific site.
     */
    public function show(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);

        $propertyId = $site->ga_property_id;
        if (!$propertyId) {
            return response()->json([
                'error' => 'GA Property ID is not configured for this site.',
            ], 422);
        }

        $period = $request->query('period', '7d');
        [$startDate, $endDate] = $this->parsePeriod($period);
        $hostname = $site->domain;

        $visitors = $this->analytics->getVisitors($propertyId, $startDate, $endDate, $hostname);
        $topPages = $this->analytics->getTopPages($propertyId, $startDate, $endDate, 20, $hostname);
        $dailyData = $this->analytics->getDailyVisitors($propertyId, $startDate, $endDate, $hostname);

        return response()->json([
            'data' => [
                'site' => $site->domain,
                'period' => $period,
                'summary' => $visitors,
                'top_pages' => $topPages,
                'daily' => $dailyData,
            ],
        ]);
    }

    /**
     * Get aggregated analytics summary across all sites.
     */
    public function summary(Request $request): JsonResponse
    {
        $period = $request->query('period', '7d');
        [$startDate, $endDate] = $this->parsePeriod($period);
        [$gscStart, $gscEnd] = $this->parseGscPeriod($period);

        $sites = Site::where('is_active', true)
            ->whereNotNull('ga_property_id')
            ->get();

        $totals = [
            'active_users' => 0,
            'page_views' => 0,
            'sessions' => 0,
            'clicks' => 0,
            'impressions' => 0,
            'sites_count' => $sites->count(),
        ];

        $perSite = [];

        foreach ($sites as $site) {
            $siteData = [
                'site_id' => $site->id,
                'domain' => $site->domain,
            ];

            // GA data (filter by hostname for shared properties)
            try {
                $hostname = $site->domain;
                $data = $this->analytics->getVisitors($site->ga_property_id, $startDate, $endDate, $hostname);
                $totals['active_users'] += $data['active_users'];
                $totals['page_views'] += $data['page_views'];
                $totals['sessions'] += $data['sessions'];

                $siteData['active_users'] = $data['active_users'];
                $siteData['page_views'] = $data['page_views'];

                // Daily data for sparklines
                $daily = $this->analytics->getDailyVisitors($site->ga_property_id, $startDate, $endDate, $hostname);
                $siteData['daily'] = $daily;
            } catch (\Exception $e) {
                $siteData['ga_error'] = $e->getMessage();
                $siteData['active_users'] = 0;
                $siteData['page_views'] = 0;
                $siteData['daily'] = [];
            }

            // GSC data
            try {
                $siteUrl = 'https://' . $site->domain;
                $gscData = $this->gsc->getPerformanceSummary($siteUrl, $gscStart, $gscEnd);
                $siteData['clicks'] = $gscData['clicks'] ?? 0;
                $siteData['impressions'] = $gscData['impressions'] ?? 0;
                $totals['clicks'] += $siteData['clicks'];
                $totals['impressions'] += $siteData['impressions'];
            } catch (\Exception $e) {
                $siteData['clicks'] = 0;
                $siteData['impressions'] = 0;
            }

            $perSite[] = $siteData;
        }

        return response()->json([
            'data' => [
                'period' => $period,
                'totals' => $totals,
                'per_site' => $perSite,
            ],
        ]);
    }

    private function parsePeriod(string $period): array
    {
        $endDate = 'today';

        return match ($period) {
            '30d' => ['30daysAgo', $endDate],
            '90d' => ['90daysAgo', $endDate],
            default => ['7daysAgo', $endDate],
        };
    }

    private function parseGscPeriod(string $period): array
    {
        $end = date('Y-m-d');

        return match ($period) {
            '30d' => [date('Y-m-d', strtotime('-30 days')), $end],
            '90d' => [date('Y-m-d', strtotime('-90 days')), $end],
            default => [date('Y-m-d', strtotime('-7 days')), $end],
        };
    }
}
