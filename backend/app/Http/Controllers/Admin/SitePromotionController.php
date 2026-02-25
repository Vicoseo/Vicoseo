<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SitePromotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SitePromotionController extends Controller
{
    public function index(int $siteId): JsonResponse
    {
        Site::findOrFail($siteId);

        $promotions = SitePromotion::where('site_id', $siteId)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $promotions]);
    }

    public function store(Request $request, int $siteId): JsonResponse
    {
        Site::findOrFail($siteId);

        $validated = $request->validate([
            'image' => ['required', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['site_id'] = $siteId;

        $promotion = SitePromotion::create($validated);

        return response()->json([
            'data' => $promotion,
            'message' => 'Promosyon eklendi.',
        ], 201);
    }

    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        Site::findOrFail($siteId);

        $promotion = SitePromotion::where('site_id', $siteId)->findOrFail($id);

        $validated = $request->validate([
            'image' => ['sometimes', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $promotion->update($validated);

        return response()->json([
            'data' => $promotion->fresh(),
            'message' => 'Promosyon güncellendi.',
        ]);
    }

    public function destroy(int $siteId, int $id): JsonResponse
    {
        Site::findOrFail($siteId);

        $promotion = SitePromotion::where('site_id', $siteId)->findOrFail($id);
        $promotion->delete();

        return response()->json([
            'message' => 'Promosyon silindi.',
        ]);
    }
}
