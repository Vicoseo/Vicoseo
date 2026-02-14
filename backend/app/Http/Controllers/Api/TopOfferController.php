<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GlobalTopOffer;
use App\Models\Site;
use App\Models\TopOffer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TopOfferController extends Controller
{
    /**
     * Return merged list of global + site-specific active top offers, sorted by sort_order.
     * Cached for 5 minutes.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');

        if (!$site->show_sponsors) {
            return response()->json(['data' => []]);
        }

        $cacheKey = "top_offers:site:{$site->id}";

        $offers = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $globalOffers = GlobalTopOffer::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(fn (GlobalTopOffer $offer) => [
                    'id' => $offer->id,
                    'slug' => $offer->slug,
                    'logo_url' => $offer->logo_url,
                    'bonus_text' => $offer->bonus_text,
                    'cta_text' => $offer->cta_text,
                    'target_url' => $offer->target_url,
                    'sort_order' => $offer->sort_order,
                    'is_active' => $offer->is_active,
                    'source' => 'global',
                ]);

            $siteOffers = TopOffer::active()
                ->ordered()
                ->get()
                ->map(fn (TopOffer $offer) => [
                    'id' => $offer->id,
                    'slug' => $offer->slug,
                    'logo_url' => $offer->logo_url,
                    'bonus_text' => $offer->bonus_text,
                    'cta_text' => $offer->cta_text,
                    'target_url' => $offer->target_url,
                    'sort_order' => $offer->sort_order,
                    'is_active' => $offer->is_active,
                    'source' => 'site',
                ]);

            return $globalOffers->merge($siteOffers)
                ->sortBy('sort_order')
                ->values()
                ->all();
        });

        return response()->json([
            'data' => $offers,
        ]);
    }
}
