<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SponsorController extends Controller
{
    public function index(): JsonResponse
    {
        $sponsors = Cache::remember('sponsors:active', now()->addMinutes(5), function () {
            return Sponsor::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        return response()->json([
            'data' => $sponsors,
        ]);
    }
}
