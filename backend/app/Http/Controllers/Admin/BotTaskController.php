<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BotTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotTaskController extends Controller
{
    /**
     * List bot tasks with optional type/status filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BotTask::with(['sourceSite:id,domain,name', 'targetSite:id,domain,name'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->input('batch_id'));
        }

        $tasks = $query->paginate($request->integer('per_page', 20));

        return response()->json($tasks);
    }

    /**
     * Get a single bot task's details (for polling).
     */
    public function show(int $id): JsonResponse
    {
        $task = BotTask::with(['sourceSite:id,domain,name', 'targetSite:id,domain,name'])
            ->findOrFail($id);

        return response()->json([
            'data' => $task,
        ]);
    }
}
