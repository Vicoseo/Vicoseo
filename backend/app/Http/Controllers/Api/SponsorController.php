<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SponsorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $category = $request->query('category');
        $cacheKey = 'sponsors:active' . ($category ? ":{$category}" : '');

        $sponsors = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($category) {
            $query = Sponsor::where('is_active', true)
                ->orderBy('sort_order', 'asc');

            if ($category && in_array($category, ['vip', 'popular', 'kutu'])) {
                $query->where('category', $category);
            }

            return $query->get();
        });

        return response()->json([
            'data' => $sponsors,
        ]);
    }
}
