<?php

declare(strict_types=1);

namespace App\Services;

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

        $this->service = new AnalyticsData($client);

        return $this->service;
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
        $cacheKey = "ga:visitors:{$propertyId}:{$hostname}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.analytics.cache_ttl', 3600), function () use ($propertyId, $startDate, $endDate, $hostname) {
            return $this->runVisitorReport($propertyId, $startDate, $endDate, $hostname);
        });
    }

    /**
     * Get top pages for a GA4 property.
     */
    public function getTopPages(string $propertyId, string $startDate, string $endDate, int $limit = 20, ?string $hostname = null): array
    {
        $cacheKey = "ga:top_pages:{$propertyId}:{$hostname}:{$startDate}:{$endDate}:{$limit}";

        return Cache::remember($cacheKey, config('google.analytics.cache_ttl', 3600), function () use ($propertyId, $startDate, $endDate, $limit, $hostname) {
            return $this->runTopPagesReport($propertyId, $startDate, $endDate, $limit, $hostname);
        });
    }

    /**
     * Get daily visitor breakdown for a GA4 property.
     */
    public function getDailyVisitors(string $propertyId, string $startDate, string $endDate, ?string $hostname = null): array
    {
        $cacheKey = "ga:daily:{$propertyId}:{$hostname}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.analytics.cache_ttl', 3600), function () use ($propertyId, $startDate, $endDate, $hostname) {
            return $this->runDailyReport($propertyId, $startDate, $endDate, $hostname);
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
