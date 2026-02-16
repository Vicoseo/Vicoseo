<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private GoogleAnalyticsService $analytics) {}

    /**
     * Get analytics data for a specific site.
     */
    public function show(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);

        $propertyId = $site->ga_measurement_id;
        if (!$propertyId) {
            return response()->json([
                'error' => 'GA Measurement ID is not configured for this site.',
            ], 422);
        }

        $period = $request->query('period', '7d');
        [$startDate, $endDate] = $this->parsePeriod($period);

        $visitors = $this->analytics->getVisitors($propertyId, $startDate, $endDate);
        $topPages = $this->analytics->getTopPages($propertyId, $startDate, $endDate);
        $dailyData = $this->analytics->getDailyVisitors($propertyId, $startDate, $endDate);

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

        $sites = Site::where('is_active', true)
            ->whereNotNull('ga_measurement_id')
            ->get();

        $totals = [
            'active_users' => 0,
            'page_views' => 0,
            'sessions' => 0,
            'sites_count' => $sites->count(),
        ];

        $perSite = [];

        foreach ($sites as $site) {
            try {
                $data = $this->analytics->getVisitors($site->ga_measurement_id, $startDate, $endDate);
                $totals['active_users'] += $data['active_users'];
                $totals['page_views'] += $data['page_views'];
                $totals['sessions'] += $data['sessions'];

                $perSite[] = [
                    'site_id' => $site->id,
                    'domain' => $site->domain,
                    'active_users' => $data['active_users'],
                    'page_views' => $data['page_views'],
                ];
            } catch (\Exception $e) {
                $perSite[] = [
                    'site_id' => $site->id,
                    'domain' => $site->domain,
                    'error' => $e->getMessage(),
                ];
            }
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
}
