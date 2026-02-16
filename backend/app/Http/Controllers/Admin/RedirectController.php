<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRedirectRequest;
use App\Models\Redirect;
use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * List all redirects for a given site.
     */
    public function index(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $redirects = Redirect::orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($redirects);
    }

    /**
     * Create a new redirect for a given site.
     */
    public function store(StoreRedirectRequest $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $redirect = Redirect::create($request->validated());

        return response()->json([
            'data' => $redirect,
            'message' => 'Redirect created successfully.',
        ], 201);
    }

    /**
     * Get a single redirect for a given site.
     */
    public function show(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $redirect = Redirect::findOrFail($id);

        return response()->json([
            'data' => $redirect,
        ]);
    }

    /**
     * Update a redirect for a given site.
     */
    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $redirect = Redirect::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'unique:tenant.redirects,slug,' . $redirect->id],
            'target_url' => ['sometimes', 'url', 'max:500'],
            'status_code' => ['sometimes', 'integer', 'in:301,302,307,308'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $redirect->update($validated);

        return response()->json([
            'data' => $redirect->fresh(),
            'message' => 'Redirect updated successfully.',
        ]);
    }

    /**
     * Delete a redirect for a given site.
     */
    public function destroy(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $redirect = Redirect::findOrFail($id);
        $redirect->delete();

        return response()->json([
            'message' => 'Redirect deleted successfully.',
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
