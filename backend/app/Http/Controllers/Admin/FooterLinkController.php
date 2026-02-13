<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FooterLinkController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    /**
     * List all footer links for a given site.
     */
    public function index(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $footerLinks = FooterLink::orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 50));

        return response()->json($footerLinks);
    }

    /**
     * Create a new footer link for a given site.
     */
    public function store(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $validated = $request->validate([
            'link_text' => ['required', 'string', 'max:255'],
            'link_url' => ['required', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $footerLink = FooterLink::create($validated);

        return response()->json([
            'data' => $footerLink,
            'message' => 'Footer link created successfully.',
        ], 201);
    }

    /**
     * Update a footer link for a given site.
     */
    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $footerLink = FooterLink::findOrFail($id);

        $validated = $request->validate([
            'link_text' => ['sometimes', 'string', 'max:255'],
            'link_url' => ['sometimes', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $footerLink->update($validated);

        return response()->json([
            'data' => $footerLink->fresh(),
            'message' => 'Footer link updated successfully.',
        ]);
    }

    /**
     * Delete a footer link for a given site.
     */
    public function destroy(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $footerLink = FooterLink::findOrFail($id);
        $footerLink->delete();

        return response()->json([
            'message' => 'Footer link deleted successfully.',
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
