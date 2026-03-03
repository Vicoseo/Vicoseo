<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    private const CACHE_TTL = 7200;      // 2 hours
    private const COOLDOWN_TTL = 300;    // 5 minutes

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

        $propertyId = $site->ga_measurement_id;
        if (!$propertyId) {
            return response()->json([
                'error' => 'GA Measurement ID is not configured for this site.',
            ], 422);
        }

        $period = $request->query('period', '7d');
        [$startDate, $endDate] = $this->parsePeriod($period);
        $hostname = $site->domain;

        try {
            $visitors = $this->analytics->getVisitors($propertyId, $startDate, $endDate, $hostname);
            $topPages = $this->analytics->getTopPages($propertyId, $startDate, $endDate, 20, $hostname);
            $dailyData = $this->analytics->getDailyVisitors($propertyId, $startDate, $endDate, $hostname);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'site' => $site->domain,
                    'period' => $period,
                    'summary' => ['active_users' => 0, 'page_views' => 0, 'sessions' => 0, 'avg_session_duration' => 0],
                    'top_pages' => [],
                    'daily' => [],
                    'error' => $e->getMessage(),
                ],
            ]);
        }

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
     * Uses a snapshot cache (2h TTL). Pass ?refresh=1 to force update (5min cooldown).
     */
    public function summary(Request $request): JsonResponse
    {
        $period = $request->query('period', '7d');
        $forceRefresh = $request->boolean('refresh');

        $cacheKey = "analytics_summary:{$period}";
        $cooldownKey = "analytics_cooldown:{$period}";

        // Force refresh requested
        if ($forceRefresh) {
            if (Cache::has($cooldownKey)) {
                $remaining = Cache::get($cooldownKey) - time();
                return response()->json([
                    'data' => Cache::get($cacheKey),
                    'cooldown' => true,
                    'cooldown_remaining' => max(0, $remaining),
                    'message' => 'Lütfen bekleyin, veriler yakın zamanda güncellendi.',
                ]);
            }

            // Set cooldown
            Cache::put($cooldownKey, time() + self::COOLDOWN_TTL, self::COOLDOWN_TTL);

            // Fetch fresh data
            $data = $this->fetchSummaryData($period);

            // Don't cache error responses (missing credentials, etc.)
            if (!isset($data['error'])) {
                Cache::put($cacheKey, $data, self::CACHE_TTL);
            }

            return response()->json([
                'data' => $data,
                'refreshed' => true,
                'cooldown' => false,
            ]);
        }

        // Return cached data or fetch if empty
        $cached = Cache::get($cacheKey);
        if ($cached) {
            $cooldownRemaining = 0;
            if (Cache::has($cooldownKey)) {
                $cooldownRemaining = max(0, Cache::get($cooldownKey) - time());
            }

            return response()->json([
                'data' => $cached,
                'from_cache' => true,
                'cooldown_remaining' => $cooldownRemaining,
            ]);
        }

        // No cache exists — fetch for the first time
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'data' => $this->emptySummary($period),
                'from_cache' => false,
                'cooldown' => true,
                'cooldown_remaining' => max(0, Cache::get($cooldownKey) - time()),
            ]);
        }

        Cache::put($cooldownKey, time() + self::COOLDOWN_TTL, self::COOLDOWN_TTL);
        $data = $this->fetchSummaryData($period);

        if (!isset($data['error'])) {
            Cache::put($cacheKey, $data, self::CACHE_TTL);
        }

        return response()->json([
            'data' => $data,
            'refreshed' => true,
            'cooldown' => false,
        ]);
    }

    private function fetchSummaryData(string $period): array
    {
        $credPath = config('google.service_account_path');
        if (!$credPath || !file_exists($credPath)) {
            Log::error('Google service account file not found: ' . ($credPath ?: 'not configured'));

            return $this->emptySummary($period) + [
                'error' => 'Google service account yapılandırılmamış. Lütfen service-account.json dosyasını yükleyin.',
            ];
        }

        if (!class_exists(\Google\Client::class)) {
            Log::error('Google API client package is not installed');

            return $this->emptySummary($period) + [
                'error' => 'Google API paketleri yüklü değil. Sunucuda "composer install" çalıştırın.',
            ];
        }

        [$startDate, $endDate] = $this->parsePeriod($period);
        [$gscStart, $gscEnd] = $this->parseGscPeriod($period);

        $sites = Site::where('is_active', true)
            ->whereNotNull('ga_measurement_id')
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

            try {
                $hostname = $site->domain;
                $data = $this->analytics->getVisitors($site->ga_measurement_id, $startDate, $endDate, $hostname);
                $totals['active_users'] += $data['active_users'];
                $totals['page_views'] += $data['page_views'];
                $totals['sessions'] += $data['sessions'];

                $siteData['active_users'] = $data['active_users'];
                $siteData['page_views'] = $data['page_views'];

                $daily = $this->analytics->getDailyVisitors($site->ga_measurement_id, $startDate, $endDate, $hostname);
                $siteData['daily'] = $daily;
            } catch (\Exception $e) {
                $siteData['ga_error'] = $e->getMessage();
                $siteData['active_users'] = 0;
                $siteData['page_views'] = 0;
                $siteData['daily'] = [];
            }

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

        $failedCount = collect($perSite)->filter(fn ($s) => isset($s['ga_error']))->count();

        $result = [
            'period' => $period,
            'totals' => $totals,
            'per_site' => $perSite,
            'last_updated' => now()->toIso8601String(),
        ];

        if ($failedCount > 0 && $failedCount === count($perSite)) {
            $result['error'] = "Hiçbir site için analitik verisi alınamadı ({$failedCount} site hata verdi).";
        }

        return $result;
    }

    private function emptySummary(string $period): array
    {
        return [
            'period' => $period,
            'totals' => ['active_users' => 0, 'page_views' => 0, 'sessions' => 0, 'clicks' => 0, 'impressions' => 0, 'sites_count' => 0],
            'per_site' => [],
            'last_updated' => null,
        ];
    }

    public function diagnostics(): JsonResponse
    {
        $checks = [];

        // 1. Service account file
        $credPath = config('google.service_account_path');
        $checks['service_account_path'] = $credPath;
        $checks['service_account_exists'] = $credPath && file_exists($credPath);

        if ($checks['service_account_exists']) {
            $json = json_decode(file_get_contents($credPath), true);
            $checks['service_account_email'] = $json['client_email'] ?? 'unknown';
            $checks['service_account_project'] = $json['project_id'] ?? 'unknown';
        }

        // 2. Required packages
        $checks['packages'] = [
            'google/apiclient' => class_exists(\Google\Client::class),
            'google/apiclient-services' => class_exists(\Google\Service\AnalyticsData::class),
            'google/analytics-admin' => class_exists(\Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient::class),
        ];

        // 3. Sites with GA measurement IDs
        $sites = Site::where('is_active', true)->whereNotNull('ga_measurement_id')->get(['id', 'domain', 'ga_measurement_id']);
        $checks['sites_with_ga'] = $sites->map(fn ($s) => [
            'id' => $s->id,
            'domain' => $s->domain,
            'measurement_id' => $s->ga_measurement_id,
        ]);

        // 4. Test API call (first site only)
        $checks['api_test'] = null;
        if ($checks['service_account_exists'] && $checks['packages']['google/apiclient'] && $sites->isNotEmpty()) {
            try {
                $testSite = $sites->first();
                $data = $this->analytics->getVisitors($testSite->ga_measurement_id, '7daysAgo', 'today', $testSite->domain);
                $checks['api_test'] = [
                    'success' => true,
                    'site' => $testSite->domain,
                    'result' => $data,
                ];
            } catch (\Exception $e) {
                $checks['api_test'] = [
                    'success' => false,
                    'site' => $sites->first()->domain,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // 5. Cache status
        $checks['cache'] = [
            'summary_7d' => Cache::has('analytics_summary:7d'),
            'summary_30d' => Cache::has('analytics_summary:30d'),
            'measurement_id_map' => Cache::has('ga:measurement_id_map'),
        ];

        return response()->json(['diagnostics' => $checks]);
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
