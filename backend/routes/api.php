<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Public API routes (for Next.js frontend) - tenant-scoped via middleware
Route::middleware('tenant')->prefix('v1')->group(function () {
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

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout']);
        Route::get('/me', [Admin\AuthController::class, 'me']);

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

        // Per-site resources
        Route::prefix('sites/{siteId}')->group(function () {
            Route::post('ai-generate', [Admin\AiGenerateController::class, 'generate']);

            Route::apiResource('pages', Admin\PageController::class);
            Route::apiResource('posts', Admin\PostController::class);
            Route::apiResource('top-offers', Admin\TopOfferController::class)->names([
                'index' => 'admin.site-top-offers.index',
                'store' => 'admin.site-top-offers.store',
                'show' => 'admin.site-top-offers.show',
                'update' => 'admin.site-top-offers.update',
                'destroy' => 'admin.site-top-offers.destroy',
            ]);
            Route::apiResource('redirects', Admin\RedirectController::class);
            Route::apiResource('footer-links', Admin\FooterLinkController::class)->except(['show']);
        });
    });
});
