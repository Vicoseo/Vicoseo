<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Auto-cleanup: delete logs older than 7 days
        AdminLog::where('created_at', '<', now()->subDays(7))->delete();

        $query = AdminLog::with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->input('action') . '%');
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->input('resource_type'));
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to') . ' 23:59:59');
        }

        $logs = $query->paginate($request->integer('per_page', 50));

        return response()->json($logs);
    }
}
