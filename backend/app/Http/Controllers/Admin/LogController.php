<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    /**
     * Get backend Laravel log entries.
     */
    public function backend(Request $request): JsonResponse
    {
        $level = $request->query('level', '');
        $perPage = $request->integer('per_page', 50);

        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $content = File::get($logFile);
        $entries = $this->parseLogEntries($content);

        if ($level) {
            $entries = array_filter($entries, fn ($e) => strtolower($e['level']) === strtolower($level));
            $entries = array_values($entries);
        }

        // Most recent first
        $entries = array_reverse($entries);
        $total = count($entries);
        $entries = array_slice($entries, 0, $perPage);

        return response()->json([
            'data' => $entries,
            'total' => $total,
        ]);
    }

    /**
     * Get error summary (counts by level).
     */
    public function summary(): JsonResponse
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return response()->json(['data' => ['error' => 0, 'warning' => 0, 'info' => 0]]);
        }

        $content = File::get($logFile);
        $entries = $this->parseLogEntries($content);

        $counts = [
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
            'critical' => 0,
        ];

        foreach ($entries as $entry) {
            $level = strtolower($entry['level']);
            if (isset($counts[$level])) {
                $counts[$level]++;
            }
        }

        return response()->json(['data' => $counts]);
    }

    /**
     * Parse Laravel log file into structured entries.
     */
    private function parseLogEntries(string $content): array
    {
        $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+\w+\.(\w+):\s+(.*?)(?=\[\d{4}-\d{2}-\d{2}|\z)/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $entries = [];
        foreach (array_slice($matches, -500) as $match) {
            $message = trim($match[3]);
            // Truncate very long messages
            if (strlen($message) > 2000) {
                $message = substr($message, 0, 2000) . '...';
            }

            $entries[] = [
                'timestamp' => $match[1],
                'level' => $match[2],
                'message' => $message,
            ];
        }

        return $entries;
    }
}
