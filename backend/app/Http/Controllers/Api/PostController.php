<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BackgroundPackage;
use App\Models\Post;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * List published posts with basic fields, paginated, latest first.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::published()
            ->latest()
            ->select(['id', 'slug', 'title', 'excerpt', 'featured_image', 'category_id', 'published_at', 'created_at', 'updated_at'])
            ->paginate($request->integer('per_page', 15));

        return response()->json($posts);
    }

    /**
     * List popular posts based on analytics popularity score.
     * Falls back to latest posts if insufficient analytics data.
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = min($request->integer('limit', 6), 20);

        try {
            $posts = Post::published()
                ->hasPopularityData()
                ->popular()
                ->select(['id', 'slug', 'title', 'excerpt', 'featured_image', 'category_id', 'published_at', 'popularity_score', 'created_at', 'updated_at'])
                ->limit($limit)
                ->get();
        } catch (\Throwable) {
            // popularity_score column may not exist yet (pre-migration)
            $posts = collect();
        }

        // Fallback: fill remaining slots with latest posts
        if ($posts->count() < $limit) {
            $existingIds = $posts->pluck('id')->toArray();

            try {
                $fill = Post::published()
                    ->latest()
                    ->whereNotIn('id', $existingIds)
                    ->select(['id', 'slug', 'title', 'excerpt', 'featured_image', 'category_id', 'published_at', 'popularity_score', 'created_at', 'updated_at'])
                    ->limit($limit - $posts->count())
                    ->get();
            } catch (\Throwable) {
                // popularity_score column missing — select without it
                $fill = Post::published()
                    ->latest()
                    ->whereNotIn('id', $existingIds)
                    ->select(['id', 'slug', 'title', 'excerpt', 'featured_image', 'category_id', 'published_at', 'created_at', 'updated_at'])
                    ->limit($limit - $posts->count())
                    ->get();
            }

            $posts = $posts->concat($fill);
        }

        return response()->json([
            'data' => $posts->values(),
        ]);
    }

    /**
     * Get a single published post by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->first();

        if (!$post) {
            return response()->json([
                'error' => 'Not Found',
                'message' => "Post not found: {$slug}",
            ], 404);
        }

        $data = $post->toArray();

        // Inject hero data from background package assigned to this site
        $site = app(TenantManager::class)->getCurrentSite();
        if ($site) {
            $bg = BackgroundPackage::where('site_id', $site->id)
                ->where('is_active', true)
                ->first();

            if ($bg) {
                $data['hero'] = [
                    'background' => ['image_url' => $bg->image_url],
                    'overlay_color' => $bg->overlay_color,
                ];
            }
        }

        return response()->json([
            'data' => $data,
        ]);
    }
}
