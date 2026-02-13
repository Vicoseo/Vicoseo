<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * List all published pages with basic fields, paginated.
     */
    public function index(Request $request): JsonResponse
    {
        $pages = Page::published()
            ->ordered()
            ->select(['id', 'slug', 'title', 'sort_order', 'created_at', 'updated_at'])
            ->paginate($request->integer('per_page', 15));

        return response()->json($pages);
    }

    /**
     * Get a single published page by slug with full content and meta.
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->first();

        if (!$page) {
            return response()->json([
                'error' => 'Not Found',
                'message' => "Page not found: {$slug}",
            ], 404);
        }

        return response()->json([
            'data' => $page,
        ]);
    }
}
