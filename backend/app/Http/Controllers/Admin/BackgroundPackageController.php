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
        $packages = BackgroundPackage::with('site:id,name,domain')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 50));

        return response()->json($packages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'image_url' => ['required', 'string', 'max:500'],
            'site_id' => ['nullable', 'integer', 'exists:landlord.sites,id'],
            'overlay_color' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package = BackgroundPackage::create($validated);

        return response()->json([
            'data' => $package->load('site:id,name,domain'),
            'message' => 'Background package created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $package = BackgroundPackage::with('site:id,name,domain')->findOrFail($id);

        return response()->json([
            'data' => $package,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $package = BackgroundPackage::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'image_url' => ['sometimes', 'string', 'max:500'],
            'site_id' => ['nullable', 'integer', 'exists:landlord.sites,id'],
            'overlay_color' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package->update($validated);

        return response()->json([
            'data' => $package->fresh()->load('site:id,name,domain'),
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
