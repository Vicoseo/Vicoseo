<?php

declare(strict_types=1);

namespace App\Services;

use Google\Analytics\Admin\V1alpha\AnalyticsAdminServiceClient;
use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Dimension;
use Google\Service\AnalyticsData\Filter;
use Google\Service\AnalyticsData\FilterExpression;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\RunReportRequest;
use Google\Service\AnalyticsData\StringFilter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    private ?AnalyticsData $service = null;

    private function getService(): AnalyticsData
    {
        if ($this->service) {
            return $this->service;
        }

        $client = new Client();
        $client->setAuthConfig(config('google.service_account_path'));
        $client->addScope(AnalyticsData::ANALYTICS_READONLY);
        $client->setHttpClient(new \GuzzleHttp\Client([
            'timeout' => 15,
            'connect_timeout' => 5,
        ]));

        $this->service = new AnalyticsData($client);

        return $this->service;
    }

    /**
     * Resolve a GA4 Measurement ID (G-XXXXXXX) to a numeric Property ID.
     */
    public function resolvePropertyId(string $measurementId): string
    {
        // Already numeric — return as-is
        if (is_numeric($measurementId)) {
            return $measurementId;
        }

        $map = $this->getMeasurementIdMap();

        if (isset($map[$measurementId])) {
            return $map[$measurementId];
        }

        // Force refresh cache and try again
        $map = $this->buildMeasurementIdMap();

        if (isset($map[$measurementId])) {
            return $map[$measurementId];
        }

        throw new \RuntimeException("Cannot resolve Measurement ID {$measurementId} to a numeric Property ID. Ensure the service account has access to this GA4 property.");
    }

    /**
     * Get the cached measurement-id → property-id map.
     */
    private function getMeasurementIdMap(): array
    {
        return Cache::remember('ga:measurement_id_map', 86400, function () {
            return $this->buildMeasurementIdMap();
        });
    }

    /**
     * Build mapping by listing all properties and their data streams via GA Admin API.
     */
    private function buildMeasurementIdMap(): array
    {
        $credPath = config('google.service_account_path');
        $adminClient = new AnalyticsAdminServiceClient([
            'credentials' => $credPath,
            'transport' => 'rest',
        ]);

        // Manual fallback for properties not discoverable via Admin API
        // (e.g. service account has property-level access but not account-level)
        $map = [
            'G-TTZXWHEXWD' => '526284686', // risespor.com
        ];

        try {
            $accounts = $adminClient->listAccountSummaries();

            foreach ($accounts as $account) {
                foreach ($account->getPropertySummaries() as $propSummary) {
                    // e.g. "properties/525078596"
                    $propertyResource = $propSummary->getProperty();
                    $numericId = str_replace('properties/', '', $propertyResource);

                    try {
                        $streams = $adminClient->listDataStreams($propertyResource);
                        foreach ($streams as $stream) {
                            $webData = $stream->getWebStreamData();
                            if ($webData && $webData->getMeasurementId()) {
                                $map[$webData->getMeasurementId()] = $numericId;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning("GA Admin: could not list streams for {$propertyResource}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("GA Admin: could not list account summaries: " . $e->getMessage());
        }

        // Persist refreshed map in cache
        Cache::put('ga:measurement_id_map', $map, 86400);

        return $map;
    }

    /**
     * Build a hostname dimension filter.
     */
    private function hostnameFilter(?string $hostname): ?FilterExpression
    {
        if (!$hostname) {
            return null;
        }

        return new FilterExpression([
            'filter' => new Filter([
                'fieldName' => 'hostName',
                'stringFilter' => new StringFilter([
                    'value' => $hostname,
                    'matchType' => 'EXACT',
                ]),
            ]),
        ]);
    }

    /**
     * Get visitor metrics for a GA4 property, optionally filtered by hostname.
     */
    public function getVisitors(string $propertyId, string $startDate, string $endDate, ?string $hostname = null): array
    {
        $numericId = $this->resolvePropertyId($propertyId);
        $cacheKey = "ga:visitors:{$numericId}:{$hostname}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.analytics.cache_ttl', 3600), function () use ($numericId, $startDate, $endDate, $hostname) {
            return $this->runVisitorReport($numericId, $startDate, $endDate, $hostname);
        });
    }

    /**
     * Get top pages for a GA4 property.
     */
    public function getTopPages(string $propertyId, string $startDate, string $endDate, int $limit = 20, ?string $hostname = null): array
    {
        $numericId = $this->resolvePropertyId($propertyId);
        $cacheKey = "ga:top_pages:{$numericId}:{$hostname}:{$startDate}:{$endDate}:{$limit}";

        return Cache::remember($cacheKey, config('google.analytics.cache_ttl', 3600), function () use ($numericId, $startDate, $endDate, $limit, $hostname) {
            return $this->runTopPagesReport($numericId, $startDate, $endDate, $limit, $hostname);
        });
    }

    /**
     * Get daily visitor breakdown for a GA4 property.
     */
    public function getDailyVisitors(string $propertyId, string $startDate, string $endDate, ?string $hostname = null): array
    {
        $numericId = $this->resolvePropertyId($propertyId);
        $cacheKey = "ga:daily:{$numericId}:{$hostname}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.analytics.cache_ttl', 3600), function () use ($numericId, $startDate, $endDate, $hostname) {
            return $this->runDailyReport($numericId, $startDate, $endDate, $hostname);
        });
    }

    private function runVisitorReport(string $propertyId, string $startDate, string $endDate, ?string $hostname = null): array
    {
        $params = [
            'dateRanges' => [new DateRange(['startDate' => $startDate, 'endDate' => $endDate])],
            'metrics' => [
                new Metric(['name' => 'activeUsers']),
                new Metric(['name' => 'screenPageViews']),
                new Metric(['name' => 'sessions']),
                new Metric(['name' => 'averageSessionDuration']),
            ],
        ];

        $filter = $this->hostnameFilter($hostname);
        if ($filter) {
            $params['dimensionFilter'] = $filter;
        }

        $request = new RunReportRequest($params);
        $response = $this->getService()->properties->runReport("properties/{$propertyId}", $request);
        $row = $response->getRows()[0] ?? null;

        if (!$row) {
            return [
                'active_users' => 0,
                'page_views' => 0,
                'sessions' => 0,
                'avg_session_duration' => 0,
            ];
        }

        $values = $row->getMetricValues();

        return [
            'active_users' => (int) ($values[0]->getValue() ?? 0),
            'page_views' => (int) ($values[1]->getValue() ?? 0),
            'sessions' => (int) ($values[2]->getValue() ?? 0),
            'avg_session_duration' => round((float) ($values[3]->getValue() ?? 0), 1),
        ];
    }

    private function runTopPagesReport(string $propertyId, string $startDate, string $endDate, int $limit, ?string $hostname = null): array
    {
        $params = [
            'dateRanges' => [new DateRange(['startDate' => $startDate, 'endDate' => $endDate])],
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [
                new Metric(['name' => 'screenPageViews']),
                new Metric(['name' => 'activeUsers']),
            ],
            'limit' => $limit,
        ];

        $filter = $this->hostnameFilter($hostname);
        if ($filter) {
            $params['dimensionFilter'] = $filter;
        }

        $request = new RunReportRequest($params);
        $response = $this->getService()->properties->runReport("properties/{$propertyId}", $request);
        $pages = [];

        foreach ($response->getRows() ?? [] as $row) {
            $dims = $row->getDimensionValues();
            $vals = $row->getMetricValues();
            $pages[] = [
                'path' => $dims[0]->getValue(),
                'page_views' => (int) $vals[0]->getValue(),
                'users' => (int) $vals[1]->getValue(),
            ];
        }

        return $pages;
    }

    private function runDailyReport(string $propertyId, string $startDate, string $endDate, ?string $hostname = null): array
    {
        $params = [
            'dateRanges' => [new DateRange(['startDate' => $startDate, 'endDate' => $endDate])],
            'dimensions' => [new Dimension(['name' => 'date'])],
            'metrics' => [
                new Metric(['name' => 'activeUsers']),
                new Metric(['name' => 'screenPageViews']),
            ],
        ];

        $filter = $this->hostnameFilter($hostname);
        if ($filter) {
            $params['dimensionFilter'] = $filter;
        }

        $request = new RunReportRequest($params);
        $response = $this->getService()->properties->runReport("properties/{$propertyId}", $request);
        $days = [];

        foreach ($response->getRows() ?? [] as $row) {
            $dims = $row->getDimensionValues();
            $vals = $row->getMetricValues();
            $date = $dims[0]->getValue();
            $days[] = [
                'date' => substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2),
                'users' => (int) $vals[0]->getValue(),
                'page_views' => (int) $vals[1]->getValue(),
            ];
        }

        usort($days, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return $days;
    }
}
