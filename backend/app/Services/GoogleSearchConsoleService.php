<?php

declare(strict_types=1);

namespace App\Services;

use Google\Client;
use Google\Service\SearchConsole;
use Google\Service\Webmasters;
use Google\Service\Webmasters\SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Cache;

class GoogleSearchConsoleService
{
    private ?Client $client = null;

    private function getClient(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new Client();
        $this->client->setAuthConfig(config('google.service_account_path'));
        $this->client->addScope(Webmasters::WEBMASTERS_READONLY);
        $this->client->addScope(Webmasters::WEBMASTERS);

        return $this->client;
    }

    /**
     * Get index status for a site (total indexed pages).
     */
    public function getIndexStatus(string $siteUrl): array
    {
        $cacheKey = "gsc:index:{$siteUrl}";

        return Cache::remember($cacheKey, config('google.search_console.cache_ttl', 3600), function () use ($siteUrl) {
            $service = new SearchConsole($this->getClient());

            try {
                $response = $service->urlInspection->index->inspect([
                    'inspectionUrl' => $siteUrl,
                    'siteUrl' => $siteUrl,
                ]);

                return [
                    'verdict' => $response->getInspectionResult()?->getIndexStatusResult()?->getVerdict() ?? 'UNKNOWN',
                    'coverage_state' => $response->getInspectionResult()?->getIndexStatusResult()?->getCoverageState() ?? 'UNKNOWN',
                ];
            } catch (\Exception $e) {
                return [
                    'verdict' => 'ERROR',
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Submit a sitemap for a site.
     */
    public function submitSitemap(string $siteUrl, string $sitemapUrl): array
    {
        $service = new Webmasters($this->getClient());

        try {
            $service->sitemaps->submit($siteUrl, $sitemapUrl);

            return ['success' => true, 'message' => 'Sitemap submitted successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get sitemaps for a site.
     */
    public function getSitemaps(string $siteUrl): array
    {
        $cacheKey = "gsc:sitemaps:{$siteUrl}";

        return Cache::remember($cacheKey, config('google.search_console.cache_ttl', 3600), function () use ($siteUrl) {
            $service = new Webmasters($this->getClient());

            try {
                $response = $service->sitemaps->listSitemaps($siteUrl);
                $sitemaps = [];

                foreach ($response->getSitemap() ?? [] as $sitemap) {
                    $sitemaps[] = [
                        'path' => $sitemap->getPath(),
                        'last_submitted' => $sitemap->getLastSubmitted(),
                        'is_pending' => $sitemap->getIsPending(),
                        'warnings' => $sitemap->getWarnings(),
                        'errors' => $sitemap->getErrors(),
                    ];
                }

                return $sitemaps;
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Get search performance data (clicks, impressions, CTR, position).
     */
    public function getSearchPerformance(string $siteUrl, string $startDate, string $endDate, int $rowLimit = 25): array
    {
        $cacheKey = "gsc:performance:{$siteUrl}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.search_console.cache_ttl', 3600), function () use ($siteUrl, $startDate, $endDate, $rowLimit) {
            $service = new Webmasters($this->getClient());

            $request = new SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions(['query']);
            $request->setRowLimit($rowLimit);

            try {
                $response = $service->searchanalytics->query($siteUrl, $request);
                $queries = [];

                foreach ($response->getRows() ?? [] as $row) {
                    $queries[] = [
                        'query' => $row->getKeys()[0] ?? '',
                        'clicks' => (int) $row->getClicks(),
                        'impressions' => (int) $row->getImpressions(),
                        'ctr' => round($row->getCtr() * 100, 2),
                        'position' => round($row->getPosition(), 1),
                    ];
                }

                return $queries;
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Get search performance summary (totals).
     */
    public function getPerformanceSummary(string $siteUrl, string $startDate, string $endDate): array
    {
        $cacheKey = "gsc:summary:{$siteUrl}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.search_console.cache_ttl', 3600), function () use ($siteUrl, $startDate, $endDate) {
            $service = new Webmasters($this->getClient());

            $request = new SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);

            try {
                $response = $service->searchanalytics->query($siteUrl, $request);
                $rows = $response->getRows() ?? [];

                if (empty($rows)) {
                    return ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0];
                }

                $row = $rows[0];

                return [
                    'clicks' => (int) $row->getClicks(),
                    'impressions' => (int) $row->getImpressions(),
                    'ctr' => round($row->getCtr() * 100, 2),
                    'position' => round($row->getPosition(), 1),
                ];
            } catch (\Exception $e) {
                return ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0, 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Get crawl errors for a site (page-level errors).
     */
    public function getErrors(string $siteUrl, string $startDate, string $endDate): array
    {
        $cacheKey = "gsc:errors:{$siteUrl}:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, config('google.search_console.cache_ttl', 3600), function () use ($siteUrl, $startDate, $endDate) {
            $service = new Webmasters($this->getClient());

            $request = new SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions(['page']);
            $request->setRowLimit(100);

            try {
                $response = $service->searchanalytics->query($siteUrl, $request);
                $pages = [];

                foreach ($response->getRows() ?? [] as $row) {
                    $pages[] = [
                        'page' => $row->getKeys()[0] ?? '',
                        'clicks' => (int) $row->getClicks(),
                        'impressions' => (int) $row->getImpressions(),
                    ];
                }

                return $pages;
            } catch (\Exception $e) {
                return [];
            }
        });
    }
}
