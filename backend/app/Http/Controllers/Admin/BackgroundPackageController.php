<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackgroundPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BackgroundPackageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $packages = BackgroundPackage::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 50));

        return response()->json($packages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:landlord.background_packages,slug'],
            'description' => ['nullable', 'string'],
            'image_url' => ['required', 'string', 'max:500'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
            'overlay_color' => ['sometimes', 'string', 'max:100'],
            'overlay_blend' => ['sometimes', 'string', 'in:multiply,overlay,screen,normal'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package = BackgroundPackage::create($validated);

        return response()->json([
            'data' => $package,
            'message' => 'Background package created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $package = BackgroundPackage::findOrFail($id);

        return response()->json([
            'data' => $package,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $package = BackgroundPackage::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'slug' => ['sometimes', 'string', 'max:100', 'unique:landlord.background_packages,slug,' . $package->id],
            'description' => ['nullable', 'string'],
            'image_url' => ['sometimes', 'string', 'max:500'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
            'overlay_color' => ['sometimes', 'string', 'max:100'],
            'overlay_blend' => ['sometimes', 'string', 'in:multiply,overlay,screen,normal'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package->update($validated);

        return response()->json([
            'data' => $package->fresh(),
            'message' => 'Background package updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $package = BackgroundPackage::findOrFail($id);
        $package->delete();

        return response()->json([
            'message' => 'Background package deleted successfully.',
        ]);
    }
}
