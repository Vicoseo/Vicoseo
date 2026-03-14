<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\CloudflareService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait ClearsCaches
{
    /**
     * Clear Next.js fetch cache, restart frontend, and purge Cloudflare CDN cache.
     */
    private function clearAllCaches(?string $domain = null): void
    {
        // Clear Laravel API cache
        Cache::flush();

        // Clear Next.js fetch cache
        $cachePath = '/var/www/multi-tenant-cms/frontend/.next/cache/fetch-cache';
        if (is_dir($cachePath)) {
            exec("rm -rf {$cachePath}");
        }

        // Restart PM2 frontend process
        exec('sudo /usr/bin/pm2 restart cms-frontend 2>&1');

        // Purge Cloudflare CDN cache for the domain
        if ($domain) {
            try {
                app(CloudflareService::class)->purgeCacheByDomain($domain);
            } catch (\Exception $e) {
                Log::warning("Cloudflare cache purge failed for {$domain}: " . $e->getMessage());
            }
        }
    }
}
