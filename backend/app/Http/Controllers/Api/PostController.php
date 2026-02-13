<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
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
            ->select(['id', 'slug', 'title', 'excerpt', 'featured_image', 'published_at', 'created_at', 'updated_at'])
            ->paginate($request->integer('per_page', 15));

        return response()->json($posts);
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

        return response()->json([
            'data' => $post,
        ]);
    }
}
