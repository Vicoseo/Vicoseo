<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BackgroundPackage;
use App\Models\Post;
use App\Models\Site;

class HeroService
{
    private const DEFAULTS = [
        'background_package_id' => null,
        'overlay_color' => 'rgba(10, 10, 26, 0.70)',
        'overlay_blend' => 'multiply',
        'accent_color' => null,
        'show_badge' => false,
        'badge_text' => '',
        'show_cta' => true,
        'cta_text' => 'Hemen Giris Yap',
        'cta_url' => null,
        'show_date' => true,
        'show_reading_time' => true,
        'show_category' => true,
        'slogan' => null,
        'featured_image_in_hero' => false,
        'layout' => 'centered',
    ];

    /**
     * Merge site defaults + post overrides into a resolved hero config for the frontend.
     */
    public function mergeHeroSettings(Site $site, Post $post): ?array
    {
        $siteSettings = $site->hero_settings ?? [];
        $postSettings = $post->hero_settings ?? [];

        // If neither site nor post has hero settings, return null (backward compatible)
        if (empty($siteSettings) && empty($postSettings)) {
            return null;
        }

        // Merge: post overrides site, site overrides defaults
        $merged = [];
        foreach (self::DEFAULTS as $key => $default) {
            if (isset($postSettings[$key])) {
                $merged[$key] = $postSettings[$key];
            } elseif (isset($siteSettings[$key])) {
                $merged[$key] = $siteSettings[$key];
            } else {
                $merged[$key] = $default;
            }
        }

        // Fallbacks
        if (empty($merged['accent_color'])) {
            $merged['accent_color'] = $site->primary_color ?? '#ffd700';
        }
        if (empty($merged['cta_url'])) {
            $merged['cta_url'] = $site->entry_url ?? $site->login_url ?? '#';
        }

        // Resolve background package
        $background = null;
        if ($merged['background_package_id']) {
            $package = BackgroundPackage::where('id', $merged['background_package_id'])
                ->where('is_active', true)
                ->first();

            if ($package) {
                $background = [
                    'image_url' => $package->image_url,
                    'thumbnail_url' => $package->thumbnail_url,
                ];
                // Use package overlay if not explicitly overridden
                if (!isset($postSettings['overlay_color']) && !isset($siteSettings['overlay_color'])) {
                    $merged['overlay_color'] = $package->overlay_color;
                }
                if (!isset($postSettings['overlay_blend']) && !isset($siteSettings['overlay_blend'])) {
                    $merged['overlay_blend'] = $package->overlay_blend;
                }
            }
        }

        // Build resolved output for frontend
        return [
            'background' => $background,
            'overlay_color' => $merged['overlay_color'],
            'overlay_blend' => $merged['overlay_blend'],
            'accent_color' => $merged['accent_color'],
            'badge' => [
                'show' => (bool) $merged['show_badge'],
                'text' => $merged['badge_text'] ?? '',
            ],
            'cta' => [
                'show' => (bool) $merged['show_cta'],
                'text' => $merged['cta_text'] ?? 'Hemen Giris Yap',
                'url' => $merged['cta_url'],
            ],
            'show_date' => (bool) $merged['show_date'],
            'show_reading_time' => (bool) $merged['show_reading_time'],
            'show_category' => (bool) $merged['show_category'],
            'slogan' => $merged['slogan'],
            'featured_image_in_hero' => (bool) $merged['featured_image_in_hero'],
            'layout' => $merged['layout'],
        ];
    }
}
