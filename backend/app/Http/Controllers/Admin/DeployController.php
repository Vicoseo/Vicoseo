<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class DeployController extends Controller
{
    /**
     * Clear all caches and restart the frontend process.
     */
    public function restartFrontend(): JsonResponse
    {
        // Clear Laravel caches
        Cache::flush();
        Artisan::call('cache:clear');

        // Restart PM2 frontend process
        $output = '';
        $exitCode = 0;
        exec('sudo /usr/bin/pm2 restart cms-frontend 2>&1', $outputLines, $exitCode);
        $output = implode("\n", $outputLines);

        if ($exitCode !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizlendi fakat frontend yeniden başlatılamadı.',
                'output' => $output,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Güncellemeler yayınlandı. Site birkaç saniye içinde güncellenecek.',
        ]);
    }
}
