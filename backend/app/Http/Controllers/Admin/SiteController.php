<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiteRequest;
use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * List all sites, paginated.
     */
    public function index(Request $request): JsonResponse
    {
        $sites = Site::orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($sites);
    }

    /**
     * Create a new site, provision its tenant database, and run tenant migrations.
     */
    public function store(StoreSiteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Generate db_name from domain if not provided
        if (empty($validated['db_name'])) {
            $validated['db_name'] = 'tenant_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($validated['domain']));
        }

        $site = Site::create($validated);

        // Create the tenant database and run migrations
        $this->tenantManager->createTenantDatabase($site);

        return response()->json([
            'data' => $site,
            'message' => 'Site created successfully.',
        ], 201);
    }

    /**
     * Get a single site's details.
     */
    public function show(int $id): JsonResponse
    {
        $site = Site::findOrFail($id);

        return response()->json([
            'data' => $site,
        ]);
    }

    /**
     * Update a site's details.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $site = Site::findOrFail($id);

        $validated = $request->validate([
            'domain' => ['sometimes', 'string', 'unique:landlord.sites,domain,' . $site->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'favicon_url' => ['nullable', 'string', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'entry_url' => ['nullable', 'string', 'max:500'],
            'login_url' => ['nullable', 'string', 'max:500'],
            'show_sponsors' => ['sometimes', 'boolean'],
        ]);

        // Clear tenant cache on any update
        $this->tenantManager->clearTenantCache($site->domain);

        // If domain changed, also clear cache for the old domain
        if (isset($validated['domain']) && $validated['domain'] !== $site->domain) {
            $this->tenantManager->clearTenantCache($validated['domain']);
        }

        $site->update($validated);

        return response()->json([
            'data' => $site->fresh(),
            'message' => 'Site updated successfully.',
        ]);
    }

    /**
     * Delete a site record. Does NOT drop the tenant database for safety.
     */
    public function destroy(int $id): JsonResponse
    {
        $site = Site::findOrFail($id);

        $this->tenantManager->clearTenantCache($site->domain);

        $site->delete();

        return response()->json([
            'message' => 'Site deleted successfully. The tenant database was NOT dropped for safety.',
        ]);
    }
}
