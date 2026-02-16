<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * List all posts for a given site.
     */
    public function index(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $posts = Post::orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($posts);
    }

    /**
     * Create a new post for a given site.
     */
    public function store(StorePostRequest $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $post = Post::create($request->validated());

        return response()->json([
            'data' => $post,
            'message' => 'Post created successfully.',
        ], 201);
    }

    /**
     * Get a single post for a given site.
     */
    public function show(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $post = Post::findOrFail($id);

        return response()->json([
            'data' => $post,
        ]);
    }

    /**
     * Update a post for a given site.
     */
    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'unique:tenant.posts,slug,' . $post->id],
            'title' => ['sometimes', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['sometimes', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $post->update($validated);

        return response()->json([
            'data' => $post->fresh(),
            'message' => 'Post updated successfully.',
        ]);
    }

    /**
     * Delete a post for a given site.
     */
    public function destroy(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.',
        ]);
    }

    /**
     * List revisions for a post.
     */
    public function revisions(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $post = Post::findOrFail($id);

        $revisions = $post->revisions()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json(['data' => $revisions]);
    }

    /**
     * Revert a post field to a previous revision.
     */
    public function revert(int $siteId, int $id, int $revisionId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $post = Post::findOrFail($id);
        $post->revertTo($revisionId);

        return response()->json([
            'data' => $post->fresh(),
            'message' => 'Post reverted successfully.',
        ]);
    }

    /**
     * Resolve the site and switch the tenant database connection.
     */
    private function resolveSiteTenant(int $siteId): Site
    {
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);

        return $site;
    }
}
