<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    public function index(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $categories = Category::ordered()
            ->withCount('posts')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function store(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $validated = $request->validate([
            'slug' => ['required', 'string', 'unique:tenant.categories,slug', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string', 'max:50000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category = Category::create($validated);

        return response()->json(['data' => $category, 'message' => 'Category created.'], 201);
    }

    public function show(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $category = Category::withCount('posts')->findOrFail($id);

        return response()->json(['data' => $category]);
    }

    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'unique:tenant.categories,slug,' . $category->id, 'max:255'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string', 'max:50000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category->update($validated);

        return response()->json(['data' => $category->fresh(), 'message' => 'Category updated.']);
    }

    public function destroy(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }

    private function resolveSiteTenant(int $siteId): Site
    {
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);
        return $site;
    }
}
