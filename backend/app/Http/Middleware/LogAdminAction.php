<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AdminLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log mutating requests
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        $user = $request->user();
        if (!$user) {
            return $response;
        }

        try {
            $routeName = $request->route()?->getName() ?? $request->path();
            $action = $request->method() . ' ' . $routeName;

            AdminLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'resource_type' => $this->guessResourceType($request),
                'resource_id' => $request->route('id') ?? $request->route('siteId') ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'details' => [
                    'url' => $request->fullUrl(),
                    'status' => $response->getStatusCode(),
                ],
            ]);
        } catch (\Throwable) {
            // Silently fail - logging should not break the request
        }

        return $response;
    }

    private function guessResourceType(Request $request): ?string
    {
        $path = $request->path();

        if (str_contains($path, '/sites')) return 'site';
        if (str_contains($path, '/sponsors')) return 'sponsor';
        if (str_contains($path, '/pages')) return 'page';
        if (str_contains($path, '/posts')) return 'post';
        if (str_contains($path, '/users')) return 'user';
        if (str_contains($path, '/redirects')) return 'redirect';

        return null;
    }
}
