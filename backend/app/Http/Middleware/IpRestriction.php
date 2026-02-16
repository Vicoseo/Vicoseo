<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpRestriction
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->ip_restriction_enabled && !empty($user->allowed_ips)) {
            $clientIp = $request->ip();
            $allowedIps = is_array($user->allowed_ips) ? $user->allowed_ips : [];

            if (!in_array($clientIp, $allowedIps, true)) {
                return response()->json([
                    'message' => 'Access denied. Your IP address is not allowed.',
                ], 403);
            }
        }

        return $next($request);
    }
}
