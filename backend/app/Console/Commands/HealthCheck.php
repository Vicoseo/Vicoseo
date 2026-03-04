<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HealthCheck extends Command
{
    protected $signature = 'health:check
        {--domain= : Check a specific domain only}';

    protected $description = 'Check DNS, HTTP and API health for all active sites';

    public function handle(): int
    {
        $query = Site::where('is_active', true)->orderBy('id');

        if ($domain = $this->option('domain')) {
            $query->where('domain', $domain);
        }

        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->warn('No active sites found.');
            return self::SUCCESS;
        }

        $this->info("Checking {$sites->count()} site(s)...");
        $this->newLine();

        $results = [];
        $issues = [];

        foreach ($sites as $site) {
            $result = $this->checkSite($site);
            $results[] = $result;

            if (!$result['healthy']) {
                $issues[] = $result;
            }
        }

        // Display results table
        $this->table(
            ['Domain', 'DNS', 'HTTP', 'API', 'Status'],
            array_map(fn ($r) => [
                $r['domain'],
                $r['dns'] ? 'OK' : 'FAIL',
                $r['http'] ? 'OK' : 'FAIL',
                $r['api'] ? 'OK' : 'FAIL',
                $r['healthy'] ? 'HEALTHY' : 'ISSUE',
            ], $results)
        );

        $this->newLine();
        $healthyCount = count($results) - count($issues);
        $this->info("{$healthyCount}/{$sites->count()} sites healthy.");

        if (!empty($issues)) {
            $this->newLine();
            $this->error(count($issues) . ' site(s) have issues:');
            foreach ($issues as $issue) {
                $errors = implode(', ', $issue['errors']);
                $this->warn("  {$issue['domain']}: {$errors}");
            }

            Log::warning('Health check found issues', [
                'issues' => array_map(fn ($i) => [
                    'domain' => $i['domain'],
                    'errors' => $i['errors'],
                ], $issues),
            ]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function checkSite(Site $site): array
    {
        $domain = $site->domain;
        $errors = [];

        // DNS check
        $dnsOk = !empty(dns_get_record($domain, DNS_A));
        if (!$dnsOk) {
            $errors[] = 'DNS resolution failed';
        }

        // HTTP check
        $httpOk = false;
        if ($dnsOk) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get("https://{$domain}/");
                $httpOk = $response->status() < 500;
            } catch (\Exception $e) {
                $errors[] = 'HTTP: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'HTTP skipped (DNS failed)';
        }

        // API check
        $apiOk = false;
        if ($httpOk) {
            try {
                $apiUrl = config('app.url') . '/api/v1/site/config';
                $response = Http::timeout(10)
                    ->withHeaders(['X-Tenant-Domain' => $domain])
                    ->get($apiUrl);
                $apiOk = $response->successful();
                if (!$apiOk) {
                    $errors[] = "API returned {$response->status()}";
                }
            } catch (\Exception $e) {
                $errors[] = 'API: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'API skipped (HTTP failed)';
        }

        return [
            'domain' => $domain,
            'dns' => $dnsOk,
            'http' => $httpOk,
            'api' => $apiOk,
            'healthy' => $dnsOk && $httpOk && $apiOk,
            'errors' => $errors,
        ];
    }
}
