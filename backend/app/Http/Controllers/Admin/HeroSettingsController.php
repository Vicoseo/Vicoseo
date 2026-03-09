<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackgroundPackage;
use App\Models\Post;
use App\Models\Site;
use App\Services\HeroService;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeroSettingsController extends Controller
{
    public function __construct(
        private readonly TenantManager $tenantManager,
        private readonly HeroService $heroService,
    ) {}

    /**
     * Get site hero settings + active background packages.
     */
    public function getSiteHero(int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $packages = BackgroundPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => [
                'hero_settings' => $site->hero_settings ?? [],
                'packages' => $packages,
            ],
        ]);
    }

    /**
     * Update site hero settings.
     */
    public function updateSiteHero(Request $request, int $siteId): JsonResponse
    {
        $site = Site::findOrFail($siteId);

        $validated = $request->validate([
            'background_package_id' => ['nullable', 'integer', 'exists:landlord.background_packages,id'],
            'overlay_color' => ['nullable', 'string', 'max:100'],
            'overlay_blend' => ['nullable', 'string', 'in:multiply,overlay,screen,normal'],
            'accent_color' => ['nullable', 'string', 'max:50'],
            'show_badge' => ['nullable', 'boolean'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'show_cta' => ['nullable', 'boolean'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'show_date' => ['nullable', 'boolean'],
            'show_reading_time' => ['nullable', 'boolean'],
            'show_category' => ['nullable', 'boolean'],
            'slogan' => ['nullable', 'string', 'max:200'],
            'featured_image_in_hero' => ['nullable', 'boolean'],
            'layout' => ['nullable', 'string', 'in:centered,left'],
        ]);

        $site->update(['hero_settings' => $validated]);

        return response()->json([
            'data' => $site->fresh()->hero_settings,
            'message' => 'Site hero settings updated.',
        ]);
    }

    /**
     * Get post hero settings + site defaults + packages.
     */
    public function getPostHero(int $siteId, int $postId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);

        $post = Post::findOrFail($postId);
        $packages = BackgroundPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => [
                'post_hero_settings' => $post->hero_settings ?? [],
                'site_hero_settings' => $site->hero_settings ?? [],
                'packages' => $packages,
            ],
        ]);
    }

    /**
     * Update post hero settings.
     */
    public function updatePostHero(Request $request, int $siteId, int $postId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);

        $post = Post::findOrFail($postId);

        $validated = $request->validate([
            'background_package_id' => ['nullable', 'integer', 'exists:landlord.background_packages,id'],
            'overlay_color' => ['nullable', 'string', 'max:100'],
            'overlay_blend' => ['nullable', 'string', 'in:multiply,overlay,screen,normal'],
            'accent_color' => ['nullable', 'string', 'max:50'],
            'show_badge' => ['nullable', 'boolean'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'show_cta' => ['nullable', 'boolean'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'show_date' => ['nullable', 'boolean'],
            'show_reading_time' => ['nullable', 'boolean'],
            'show_category' => ['nullable', 'boolean'],
            'slogan' => ['nullable', 'string', 'max:200'],
            'featured_image_in_hero' => ['nullable', 'boolean'],
            'layout' => ['nullable', 'string', 'in:centered,left'],
            'custom_background_url' => ['nullable', 'string', 'max:500'],
        ]);

        // If all values are null/empty, clear hero settings entirely
        $hasValues = collect($validated)->filter(fn ($v) => $v !== null)->isNotEmpty();
        $post->update(['hero_settings' => $hasValues ? $validated : null]);

        return response()->json([
            'data' => $post->fresh()->hero_settings,
            'message' => 'Post hero settings updated.',
        ]);
    }

    /**
     * Preview merged hero for a post (site defaults + post overrides).
     */
    public function previewMerged(int $siteId, int $postId): JsonResponse
    {
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);

        $post = Post::findOrFail($postId);
        $hero = $this->heroService->mergeHeroSettings($site, $post);

        return response()->json([
            'data' => $hero,
        ]);
    }
}
