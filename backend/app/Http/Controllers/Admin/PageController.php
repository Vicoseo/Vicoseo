<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Models\Page;
use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * List all pages for a given site.
     */
    public function index(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $pages = Page::orderBy('sort_order', 'asc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($pages);
    }

    /**
     * Create a new page for a given site.
     */
    public function store(StorePageRequest $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $page = Page::create($request->validated());

        return response()->json([
            'data' => $page,
            'message' => 'Page created successfully.',
        ], 201);
    }

    /**
     * Get a single page for a given site.
     */
    public function show(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $page = Page::findOrFail($id);

        return response()->json([
            'data' => $page,
        ]);
    }

    /**
     * Update a page for a given site.
     */
    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'unique:tenant.pages,slug,' . $page->id],
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'is_published' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $page->update($validated);

        return response()->json([
            'data' => $page->fresh(),
            'message' => 'Page updated successfully.',
        ]);
    }

    /**
     * Delete a page for a given site.
     */
    public function destroy(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully.',
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
