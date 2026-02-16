<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Sitemap data endpoints - tenant-scoped
Route::middleware('tenant')->prefix('v1')->group(function () {
    Route::get('/sitemap/index', [Api\SitemapController::class, 'index']);
    Route::get('/sitemap/pages', [Api\SitemapController::class, 'pages']);
    Route::get('/sitemap/posts', [Api\SitemapController::class, 'posts']);
});

// Public API routes (for Next.js frontend) - tenant-scoped via middleware
Route::middleware(['tenant', 'rate.limit:public'])->prefix('v1')->group(function () {
    Route::get('/site/config', [Api\SiteController::class, 'config']);
    Route::get('/pages', [Api\PageController::class, 'index']);
    Route::get('/pages/{slug}', [Api\PageController::class, 'show']);
    Route::get('/posts', [Api\PostController::class, 'index']);
    Route::get('/posts/{slug}', [Api\PostController::class, 'show']);
    Route::get('/top-offers', [Api\TopOfferController::class, 'index']);
    Route::get('/go/{slug}', [Api\RedirectController::class, 'handle']);
});

// Public sponsors (global, no tenant middleware needed)
Route::prefix('v1')->group(function () {
    Route::get('/sponsors', [Api\SponsorController::class, 'index']);
});

// Admin API routes - authenticated via Sanctum
Route::prefix('admin')->group(function () {
    Route::post('/login', [Admin\AuthController::class, 'login']);
    Route::post('/verify-2fa', [Admin\AuthController::class, 'verifyTwoFactor']);

    Route::middleware(['auth:sanctum', 'ip.restriction', 'log.admin', 'rate.limit:admin'])->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout']);
        Route::get('/me', [Admin\AuthController::class, 'me']);

        // File upload
        Route::post('upload', [Admin\UploadController::class, 'store']);

        // 2FA management
        Route::prefix('2fa')->group(function () {
            Route::post('enable', [Admin\TwoFactorController::class, 'enable']);
            Route::post('verify', [Admin\TwoFactorController::class, 'verify']);
            Route::post('disable', [Admin\TwoFactorController::class, 'disable']);
        });

        // Admin user management (master only)
        Route::middleware('role:master')->group(function () {
            Route::apiResource('users', Admin\UserController::class);
            Route::post('users/{id}/reset-password', [Admin\UserController::class, 'updatePassword']);
            Route::post('users/{id}/reset-2fa', [Admin\UserController::class, 'resetTwoFactor']);
        });

        // Admin logs
        Route::get('logs', [Admin\AdminLogController::class, 'index']);

        // System logs (backend Laravel logs)
        Route::get('logs/backend', [Admin\LogController::class, 'backend']);
        Route::get('logs/summary', [Admin\LogController::class, 'summary']);

        // Analytics summary (all sites)
        Route::get('analytics/summary', [Admin\AnalyticsController::class, 'summary']);

        // Site management
        Route::apiResource('sites', Admin\SiteController::class);

        // Sponsors
        Route::apiResource('sponsors', Admin\SponsorController::class);

        // Global top offers
        Route::apiResource('global-top-offers', Admin\TopOfferController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names([
                'index' => 'admin.global-top-offers.index',
                'store' => 'admin.global-top-offers.store',
                'update' => 'admin.global-top-offers.update',
                'destroy' => 'admin.global-top-offers.destroy',
            ]);

        // Bulk content generation
        Route::post('bulk-content/start', [Admin\BulkContentController::class, 'start']);
        Route::get('bulk-content/progress/{batchId}', [Admin\BulkContentController::class, 'progress']);

        // Deploy actions
        Route::post('deploy/restart-frontend', [Admin\DeployController::class, 'restartFrontend']);

        // Domain management (Namesilo + Cloudflare)
        Route::prefix('domains')->group(function () {
            Route::get('balance', [Admin\DomainController::class, 'balance']);
            Route::get('search', [Admin\DomainController::class, 'search']);
            Route::post('purchase', [Admin\DomainController::class, 'purchase']);
            Route::post('setup', [Admin\DomainController::class, 'setup']);
            Route::get('cloudflare/zones', [Admin\DomainController::class, 'cfZones']);
            Route::get('cloudflare/zones/{zoneId}', [Admin\DomainController::class, 'cfZoneDetail']);
            Route::post('cloudflare/zones/{zoneId}/dns', [Admin\DomainController::class, 'cfAddDns']);
            Route::post('retry-activation', [Admin\DomainController::class, 'retryActivation']);
            Route::post('fix-pending', [Admin\DomainController::class, 'fixPendingZone']);
            Route::get('status/{domain}', [Admin\DomainController::class, 'domainStatus']);
        });

        // Site provisioning (SSL + Nginx)
        Route::post('sites/{siteId}/provision', [Admin\SiteProvisionController::class, 'provision']);
        Route::get('sites/{siteId}/provision-status', [Admin\SiteProvisionController::class, 'status']);

        // Per-site resources
        Route::prefix('sites/{siteId}')->group(function () {
            Route::post('ai-generate', [Admin\AiGenerateController::class, 'generate']);

            Route::apiResource('pages', Admin\PageController::class);
            Route::get('pages/{id}/revisions', [Admin\PageController::class, 'revisions']);
            Route::post('pages/{id}/revisions/{revisionId}/revert', [Admin\PageController::class, 'revert']);

            Route::apiResource('posts', Admin\PostController::class);
            Route::get('posts/{id}/revisions', [Admin\PostController::class, 'revisions']);
            Route::post('posts/{id}/revisions/{revisionId}/revert', [Admin\PostController::class, 'revert']);

            Route::apiResource('top-offers', Admin\TopOfferController::class)->names([
                'index' => 'admin.site-top-offers.index',
                'store' => 'admin.site-top-offers.store',
                'show' => 'admin.site-top-offers.show',
                'update' => 'admin.site-top-offers.update',
                'destroy' => 'admin.site-top-offers.destroy',
            ]);
            Route::apiResource('redirects', Admin\RedirectController::class);
            Route::apiResource('footer-links', Admin\FooterLinkController::class)->except(['show']);

            // Content schedules
            Route::apiResource('content-schedules', Admin\ContentScheduleController::class)->except(['show']);

            // Analytics
            Route::get('analytics', [Admin\AnalyticsController::class, 'show']);

            // Google Search Console
            Route::prefix('gsc')->group(function () {
                Route::get('performance', [Admin\SearchConsoleController::class, 'performance']);
                Route::get('sitemaps', [Admin\SearchConsoleController::class, 'sitemaps']);
                Route::post('sitemaps/submit', [Admin\SearchConsoleController::class, 'submitSitemap']);
                Route::get('pages', [Admin\SearchConsoleController::class, 'pages']);
            });
        });
    });
});
