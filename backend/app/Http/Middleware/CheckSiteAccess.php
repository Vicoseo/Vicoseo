<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Master users have access to all sites
        if ($user->role === 'master') {
            return $next($request);
        }

        $siteId = $request->route('siteId') ?? $request->route('site');

        if ($siteId && !$user->canAccessSite((int) $siteId)) {
            return response()->json(['message' => 'You do not have access to this site.'], 403);
        }

        return $next($request);
    }
}
