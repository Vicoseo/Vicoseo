<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(private TenantManager $tenantManager) {}

    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->header('X-Tenant-Domain') ?? $request->getHost();

        // Strip port number if present
        $domain = strtolower(explode(':', $domain)[0]);

        // Skip tenant resolution for admin routes
        if ($request->is('api/admin/*')) {
            return $next($request);
        }

        $site = $this->tenantManager->resolveDomain($domain);

        if (!$site) {
            return response()->json([
                'error' => 'Site not found',
                'message' => "No site configured for domain: {$domain}",
            ], 404);
        }

        // Store site in request for controllers
        $request->attributes->set('site', $site);

        return $next($request);
    }
}
