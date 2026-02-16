<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sponsors = Sponsor::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 50));

        return response()->json($sponsors);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+$/', 'unique:landlord.sponsors,name'],
            'url' => ['required', 'url', 'max:500'],
            'primary_color' => ['required', 'string', 'max:20'],
            'web_background' => ['nullable', 'string', 'max:500'],
            'mobile_background' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'string', 'max:500'],
            'rating' => ['required', 'integer', 'min:0', 'max:5'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'category' => ['nullable', 'string', 'in:vip,popular,kutu'],
            'promotions' => ['nullable', 'array'],
            'promotions.*.highlight' => ['required_with:promotions', 'string', 'max:255'],
            'promotions.*.text' => ['required_with:promotions', 'string', 'max:255'],
        ]);

        $sponsor = Sponsor::create($validated);

        return response()->json([
            'data' => $sponsor,
            'message' => 'Sponsor created successfully.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $sponsor = Sponsor::findOrFail($id);

        return response()->json([
            'data' => $sponsor,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $sponsor = Sponsor::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9]+$/', 'unique:landlord.sponsors,name,' . $sponsor->id],
            'url' => ['sometimes', 'url', 'max:500'],
            'primary_color' => ['sometimes', 'string', 'max:20'],
            'web_background' => ['nullable', 'string', 'max:500'],
            'mobile_background' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'string', 'max:500'],
            'rating' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'category' => ['nullable', 'string', 'in:vip,popular,kutu'],
            'promotions' => ['nullable', 'array'],
            'promotions.*.highlight' => ['required_with:promotions', 'string', 'max:255'],
            'promotions.*.text' => ['required_with:promotions', 'string', 'max:255'],
        ]);

        $sponsor->update($validated);

        return response()->json([
            'data' => $sponsor->fresh(),
            'message' => 'Sponsor updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->delete();

        return response()->json([
            'message' => 'Sponsor deleted successfully.',
        ]);
    }
}
