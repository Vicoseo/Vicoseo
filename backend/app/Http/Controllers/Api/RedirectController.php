<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RedirectController extends Controller
{
    /**
     * Find redirect by slug, increment click_count, and return target_url with 302 status hint.
     */
    public function handle(string $slug): JsonResponse
    {
        $redirect = Redirect::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$redirect) {
            return response()->json([
                'error' => 'Not Found',
                'message' => "Redirect not found: {$slug}",
            ], 404);
        }

        // Increment click count and update last_clicked_at atomically
        DB::connection('tenant')
            ->table('redirects')
            ->where('id', $redirect->id)
            ->update([
                'click_count' => DB::raw('click_count + 1'),
                'last_clicked_at' => now(),
            ]);

        return response()->json([
            'data' => [
                'target_url' => $redirect->target_url,
                'status' => $redirect->status_code ?? 302,
            ],
        ]);
    }
}
