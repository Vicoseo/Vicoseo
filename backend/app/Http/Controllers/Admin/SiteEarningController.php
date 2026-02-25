<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteEarning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteEarningController extends Controller
{
    public function index(int $siteId): JsonResponse
    {
        Site::findOrFail($siteId);

        $earnings = SiteEarning::where('site_id', $siteId)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $earnings]);
    }

    public function store(Request $request, int $siteId): JsonResponse
    {
        Site::findOrFail($siteId);

        $validated = $request->validate([
            'image' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['site_id'] = $siteId;

        $earning = SiteEarning::create($validated);

        return response()->json([
            'data' => $earning,
            'message' => 'Kazanç eklendi.',
        ], 201);
    }

    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        Site::findOrFail($siteId);

        $earning = SiteEarning::where('site_id', $siteId)->findOrFail($id);

        $validated = $request->validate([
            'image' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $earning->update($validated);

        return response()->json([
            'data' => $earning->fresh(),
            'message' => 'Kazanç güncellendi.',
        ]);
    }

    public function destroy(int $siteId, int $id): JsonResponse
    {
        Site::findOrFail($siteId);

        $earning = SiteEarning::where('site_id', $siteId)->findOrFail($id);
        $earning->delete();

        return response()->json([
            'message' => 'Kazanç silindi.',
        ]);
    }
}
