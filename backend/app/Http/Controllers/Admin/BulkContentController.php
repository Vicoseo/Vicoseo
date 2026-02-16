<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BulkContentJob;
use App\Models\BotTask;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BulkContentController extends Controller
{
    /**
     * Dispatch bulk content generation for all active sites (or a subset).
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => ['required', 'in:openai,anthropic'],
            'content_type' => ['required', 'in:pages,posts,daily,all'],
            'overwrite' => ['sometimes', 'boolean'],
            'daily_count' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'site_ids' => ['sometimes', 'array'],
            'site_ids.*' => ['integer', 'exists:landlord.sites,id'],
        ]);

        $provider = $request->input('provider');
        $contentType = $request->input('content_type');
        $overwrite = $request->boolean('overwrite', false);
        $dailyCount = $request->integer('daily_count', 2);

        // Validate API key
        $apiKey = config("ai.{$provider}.api_key");
        if (empty($apiKey)) {
            $label = $provider === 'openai' ? 'OPENAI_API_KEY' : 'ANTHROPIC_API_KEY';
            return response()->json([
                'message' => "{$label} .env dosyasında tanımlanmamış.",
            ], 422);
        }

        // Get target sites
        $query = Site::where('is_active', true);
        if ($request->has('site_ids')) {
            $query->whereIn('id', $request->input('site_ids'));
        }
        $sites = $query->get();

        if ($sites->isEmpty()) {
            return response()->json([
                'message' => 'Aktif site bulunamadı.',
            ], 422);
        }

        $batchId = (string) Str::uuid();

        // Create a BotTask per site and dispatch jobs
        $tasks = [];
        foreach ($sites as $site) {
            $botTask = BotTask::create([
                'type' => 'bulk_content',
                'status' => 'pending',
                'target_site_id' => $site->id,
                'batch_id' => $batchId,
                'payload' => [
                    'content_type' => $contentType,
                    'provider' => $provider,
                    'overwrite' => $overwrite,
                    'domain' => $site->domain,
                ],
                'progress' => 0,
            ]);

            BulkContentJob::dispatch(
                $site->id,
                $botTask->id,
                $contentType,
                $provider,
                $overwrite,
                $dailyCount,
            )->onQueue('content');

            $tasks[] = [
                'task_id' => $botTask->id,
                'site_id' => $site->id,
                'domain' => $site->domain,
            ];
        }

        return response()->json([
            'message' => count($sites) . ' site için içerik üretimi başlatıldı.',
            'batch_id' => $batchId,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Get progress of a bulk content batch.
     */
    public function progress(string $batchId): JsonResponse
    {
        $tasks = BotTask::where('batch_id', $batchId)
            ->orderBy('id')
            ->get(['id', 'target_site_id', 'status', 'progress', 'result', 'error_message', 'payload']);

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Batch bulunamadı.'], 404);
        }

        $total = $tasks->count();
        $completed = $tasks->where('status', 'completed')->count();
        $failed = $tasks->where('status', 'failed')->count();
        $processing = $tasks->where('status', 'processing')->count();
        $pending = $tasks->where('status', 'pending')->count();

        $overallProgress = $total > 0
            ? (int) round($tasks->avg('progress'))
            : 0;

        $isFinished = ($completed + $failed) === $total;

        return response()->json([
            'batch_id' => $batchId,
            'total' => $total,
            'completed' => $completed,
            'failed' => $failed,
            'processing' => $processing,
            'pending' => $pending,
            'overall_progress' => $overallProgress,
            'is_finished' => $isFinished,
            'tasks' => $tasks->map(fn ($t) => [
                'task_id' => $t->id,
                'site_id' => $t->target_site_id,
                'domain' => $t->payload['domain'] ?? '—',
                'status' => $t->status,
                'progress' => $t->progress,
                'result' => $t->result,
                'error' => $t->error_message,
            ]),
        ]);
    }
}
