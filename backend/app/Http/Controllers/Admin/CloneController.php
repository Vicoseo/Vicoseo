<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CloneSiteJob;
use App\Models\BotTask;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class CloneController extends Controller
{
    /**
     * Clone a single site to a new domain.
     */
    public function cloneSingle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_site_id' => ['required', 'integer', 'exists:landlord.sites,id'],
            'target_domain' => ['required', 'string', 'max:255', 'unique:landlord.sites,domain'],
        ]);

        $task = BotTask::create([
            'type' => 'clone',
            'status' => 'pending',
            'source_site_id' => $validated['source_site_id'],
            'payload' => ['target_domain' => $validated['target_domain']],
        ]);

        CloneSiteJob::dispatch(
            $validated['source_site_id'],
            $validated['target_domain'],
            $task->id
        );

        return response()->json([
            'data' => $task,
            'message' => 'Clone job dispatched.',
        ], 201);
    }

    /**
     * Clone a site to multiple domains (bulk).
     */
    public function cloneBulk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_site_id' => ['required', 'integer', 'exists:landlord.sites,id'],
            'domains' => ['required', 'string'],
        ]);

        $domains = array_filter(
            array_map('trim', preg_split('/[\r\n,]+/', $validated['domains'])),
            fn ($d) => $d !== ''
        );

        if (empty($domains)) {
            return response()->json(['message' => 'No valid domains provided.'], 422);
        }

        // Check for existing domains
        $existing = Site::whereIn('domain', $domains)->pluck('domain')->toArray();
        $domains = array_diff($domains, $existing);

        if (empty($domains)) {
            return response()->json(['message' => 'All domains already exist.'], 422);
        }

        $batchId = Str::uuid()->toString();
        $tasks = [];
        $jobs = [];

        foreach ($domains as $domain) {
            $task = BotTask::create([
                'type' => 'clone',
                'status' => 'pending',
                'source_site_id' => $validated['source_site_id'],
                'batch_id' => $batchId,
                'payload' => ['target_domain' => $domain],
            ]);

            $tasks[] = $task;
            $jobs[] = new CloneSiteJob(
                $validated['source_site_id'],
                $domain,
                $task->id
            );
        }

        Bus::batch($jobs)
            ->name("clone-bulk-{$batchId}")
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'data' => [
                'batch_id' => $batchId,
                'tasks' => $tasks,
                'skipped_domains' => $existing,
            ],
            'message' => count($tasks) . ' clone jobs dispatched.',
        ], 201);
    }
}
