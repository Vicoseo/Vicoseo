<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    private ?Site $currentSite = null;

    /**
     * Resolve a domain to its corresponding Site and configure the tenant connection.
     */
    public function resolveDomain(string $domain): ?Site
    {
        $site = Cache::remember(
            "site:domain:{$domain}",
            now()->addMinutes(10),
            fn (): ?Site => Site::where('domain', $domain)->where('is_active', true)->first()
        );

        if ($site) {
            $this->setTenant($site);
        }

        return $site;
    }

    /**
     * Set the given site as the current tenant and configure its database connection.
     */
    public function setTenant(Site $site): void
    {
        $this->currentSite = $site;
        $this->configureTenantConnection($site);
    }

    /**
     * Get the currently resolved site, or null if none has been set.
     */
    public function getCurrentSite(): ?Site
    {
        return $this->currentSite;
    }

    /**
     * Dynamically configure the 'tenant' database connection to point at the given site's database.
     */
    private function configureTenantConnection(Site $site): void
    {
        $landlordConfig = Config::get('database.connections.landlord');

        Config::set('database.connections.tenant', array_merge($landlordConfig, [
            'database' => $site->db_name,
        ]));

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    /**
     * Create a new tenant database and run tenant-specific migrations.
     */
    public function createTenantDatabase(Site $site): void
    {
        DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$site->db_name}`");

        $this->setTenant($site);

        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);
    }

    /**
     * Clear the cached site lookup for a given domain.
     */
    public function clearTenantCache(string $domain): void
    {
        Cache::forget("site:domain:{$domain}");
    }
}
