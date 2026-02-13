<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopOfferRequest;
use App\Models\GlobalTopOffer;
use App\Models\Site;
use App\Models\TopOffer;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TopOfferController extends Controller
{
    public function __construct(private TenantManager $tenantManager) {}

    // ---------------------------------------------------------------
    // Global Top Offers (landlord DB)
    // ---------------------------------------------------------------

    /**
     * List all global top offers.
     */
    public function index(Request $request): JsonResponse
    {
        // When called without siteId, this handles global top offers
        if (!$request->route('siteId')) {
            return $this->globalIndex($request);
        }

        return $this->siteIndex($request, (int) $request->route('siteId'));
    }

    /**
     * Create a top offer (global or site-specific based on route).
     */
    public function store(StoreTopOfferRequest $request): JsonResponse
    {
        if (!$request->route('siteId')) {
            return $this->globalStore($request);
        }

        return $this->siteStore($request, (int) $request->route('siteId'));
    }

    /**
     * Update a top offer (global or site-specific based on route).
     */
    public function update(Request $request, mixed ...$params): JsonResponse
    {
        if (!$request->route('siteId')) {
            // Global: params[0] is the offer ID
            return $this->globalUpdate($request, (int) $params[0]);
        }

        // Site-specific: params[0] is siteId (already in route), params[1] is offer ID
        $siteId = (int) $request->route('siteId');
        $offerId = (int) end($params);

        return $this->siteUpdate($request, $siteId, $offerId);
    }

    /**
     * Delete a top offer (global or site-specific based on route).
     */
    public function destroy(Request $request, mixed ...$params): JsonResponse
    {
        if (!$request->route('siteId')) {
            return $this->globalDestroy((int) $params[0]);
        }

        $siteId = (int) $request->route('siteId');
        $offerId = (int) end($params);

        return $this->siteDestroy($siteId, $offerId);
    }

    /**
     * Show a site-specific top offer.
     */
    public function show(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $offer = TopOffer::findOrFail($id);

        return response()->json([
            'data' => $offer,
        ]);
    }

    // ---------------------------------------------------------------
    // Global Top Offer Methods
    // ---------------------------------------------------------------

    private function globalIndex(Request $request): JsonResponse
    {
        $offers = GlobalTopOffer::orderBy('sort_order', 'asc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($offers);
    }

    private function globalStore(StoreTopOfferRequest $request): JsonResponse
    {
        $offer = GlobalTopOffer::create($request->validated());
        $this->clearOffersCache();

        return response()->json([
            'data' => $offer,
            'message' => 'Global top offer created successfully.',
        ], 201);
    }

    private function globalUpdate(Request $request, int $id): JsonResponse
    {
        $offer = GlobalTopOffer::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['sometimes', 'url', 'max:500'],
            'bonus_text' => ['sometimes', 'string', 'max:255'],
            'cta_text' => ['sometimes', 'string', 'max:255'],
            'target_url' => ['sometimes', 'url', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $offer->update($validated);
        $this->clearOffersCache();

        return response()->json([
            'data' => $offer->fresh(),
            'message' => 'Global top offer updated successfully.',
        ]);
    }

    private function globalDestroy(int $id): JsonResponse
    {
        $offer = GlobalTopOffer::findOrFail($id);
        $offer->delete();
        $this->clearOffersCache();

        return response()->json([
            'message' => 'Global top offer deleted successfully.',
        ]);
    }

    // ---------------------------------------------------------------
    // Site-Specific Top Offer Methods
    // ---------------------------------------------------------------

    private function siteIndex(Request $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $offers = TopOffer::orderBy('sort_order', 'asc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($offers);
    }

    private function siteStore(StoreTopOfferRequest $request, int $siteId): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $offer = TopOffer::create($request->validated());
        $this->clearOffersCache($siteId);

        return response()->json([
            'data' => $offer,
            'message' => 'Site top offer created successfully.',
        ], 201);
    }

    private function siteUpdate(Request $request, int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $offer = TopOffer::findOrFail($id);

        $validated = $request->validate([
            'slug' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['sometimes', 'url', 'max:500'],
            'bonus_text' => ['sometimes', 'string', 'max:255'],
            'cta_text' => ['sometimes', 'string', 'max:255'],
            'target_url' => ['sometimes', 'url', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $offer->update($validated);
        $this->clearOffersCache($siteId);

        return response()->json([
            'data' => $offer->fresh(),
            'message' => 'Site top offer updated successfully.',
        ]);
    }

    private function siteDestroy(int $siteId, int $id): JsonResponse
    {
        $this->resolveSiteTenant($siteId);

        $offer = TopOffer::findOrFail($id);
        $offer->delete();
        $this->clearOffersCache($siteId);

        return response()->json([
            'message' => 'Site top offer deleted successfully.',
        ]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function resolveSiteTenant(int $siteId): Site
    {
        $site = Site::findOrFail($siteId);
        $this->tenantManager->setTenant($site);

        return $site;
    }

    /**
     * Clear top_offers cache for all active sites.
     * Called after any global or site-specific offer change.
     */
    private function clearOffersCache(?int $siteId = null): void
    {
        if ($siteId) {
            Cache::forget("top_offers:site:{$siteId}");
            return;
        }

        // Global offer changed — clear cache for every site
        Site::where('is_active', true)->pluck('id')->each(function ($id) {
            Cache::forget("top_offers:site:{$id}");
        });
    }
}
