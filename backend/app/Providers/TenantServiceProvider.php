<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\TenantManager;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register the TenantManager as a singleton in the service container.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class);
    }
}
