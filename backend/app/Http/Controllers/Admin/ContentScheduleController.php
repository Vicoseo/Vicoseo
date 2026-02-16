<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentScheduleController extends Controller
{
    public function index(int $siteId): JsonResponse
    {
        $schedules = ContentSchedule::where('site_id', $siteId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $schedules]);
    }

    public function store(Request $request, int $siteId): JsonResponse
    {
        $validated = $request->validate([
            'content_type' => ['sometimes', 'string', 'in:daily_post'],
            'frequency' => ['required', 'in:daily,custom'],
            'run_at' => ['required', 'date_format:H:i'],
            'interval_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
            'topics' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['site_id'] = $siteId;

        $schedule = ContentSchedule::create($validated);

        return response()->json([
            'data' => $schedule,
            'message' => 'Content schedule created.',
        ], 201);
    }

    public function update(Request $request, int $siteId, int $id): JsonResponse
    {
        $schedule = ContentSchedule::where('site_id', $siteId)->findOrFail($id);

        $validated = $request->validate([
            'frequency' => ['sometimes', 'in:daily,custom'],
            'run_at' => ['sometimes', 'date_format:H:i'],
            'interval_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
            'topics' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $schedule->update($validated);

        return response()->json([
            'data' => $schedule->fresh(),
            'message' => 'Content schedule updated.',
        ]);
    }

    public function destroy(int $siteId, int $id): JsonResponse
    {
        $schedule = ContentSchedule::where('site_id', $siteId)->findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Content schedule deleted.']);
    }
}
