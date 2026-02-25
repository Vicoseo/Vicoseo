<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get(['id', 'slug', 'name', 'description', 'meta_title', 'meta_description', 'sort_order', 'created_at', 'updated_at']);

        return response()->json(['data' => $categories]);
    }

    public function show(string $slug): JsonResponse
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json([
                'error' => 'Not Found',
                'message' => "Category not found: {$slug}",
            ], 404);
        }

        $posts = Post::published()
            ->latest()
            ->where('category_id', $category->id)
            ->select(['id', 'slug', 'title', 'excerpt', 'featured_image', 'published_at', 'updated_at'])
            ->paginate(20);

        return response()->json([
            'data' => $category,
            'posts' => $posts,
        ]);
    }
}
