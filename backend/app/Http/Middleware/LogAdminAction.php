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
            $action = $this->describeAction($request);
            $details = $this->buildDetails($request, $response);

            AdminLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'resource_type' => $this->guessResourceType($request),
                'resource_id' => $request->route('id') ?? $request->route('siteId') ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'details' => $details,
            ]);
        } catch (\Throwable) {
            // Silently fail - logging should not break the request
        }

        return $response;
    }

    private function describeAction(Request $request): string
    {
        $path = $request->path();
        $method = $request->method();

        // Domain operations
        if (str_contains($path, '/domains/setup')) return 'domain.setup';
        if (str_contains($path, '/domains/purchase')) return 'domain.purchase';
        if (str_contains($path, '/domains/fix-pending')) return 'domain.fix_pending';
        if (str_contains($path, '/dns')) return $method === 'POST' ? 'dns.add' : 'dns.update';

        // Site operations
        if (str_contains($path, '/sites') && !str_contains($path, '/pages') && !str_contains($path, '/posts')) {
            if ($method === 'POST') return 'site.create';
            if ($method === 'DELETE') return 'site.delete';
            // Check for specific field changes
            $input = $request->all();
            if (array_key_exists('fallback_domain', $input)) return 'site.redirect_update';
            return 'site.update';
        }

        // User operations
        if (str_contains($path, '/users')) {
            if (str_contains($path, '/password')) return 'user.password_change';
            if (str_contains($path, '/2fa')) return 'user.2fa_reset';
            if ($method === 'POST') return 'user.create';
            if ($method === 'DELETE') return 'user.delete';
            return 'user.update';
        }

        // Content operations
        if (str_contains($path, '/pages')) {
            return $method === 'POST' ? 'page.create' : ($method === 'DELETE' ? 'page.delete' : 'page.update');
        }
        if (str_contains($path, '/posts')) {
            return $method === 'POST' ? 'post.create' : ($method === 'DELETE' ? 'post.delete' : 'post.update');
        }
        if (str_contains($path, '/bulk-content')) return 'content.bulk_generate';
        if (str_contains($path, '/ai-generate')) return 'content.ai_generate';

        // Auth
        if (str_contains($path, '/logout')) return 'auth.logout';

        $routeName = $request->route()?->getName() ?? $request->path();
        return $method . ' ' . $routeName;
    }

    private function buildDetails(Request $request, Response $response): array
    {
        $details = [
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
        ];

        $input = $request->all();
        $path = $request->path();

        // Site redirect change — log old → new
        if (str_contains($path, '/sites') && array_key_exists('fallback_domain', $input)) {
            $details['fallback_domain'] = $input['fallback_domain'] ?? '(kaldırıldı)';
        }

        // Domain setup/purchase — log domain name
        if (str_contains($path, '/domains/setup') || str_contains($path, '/domains/purchase')) {
            $details['domain'] = $input['domain'] ?? null;
        }

        // User create/update — log user details (never password)
        if (str_contains($path, '/users')) {
            $safe = array_intersect_key($input, array_flip(['name', 'email', 'role', 'is_active', 'ip_restriction_enabled']));
            if (!empty($safe)) $details['user_data'] = $safe;
            if (str_contains($path, '/password')) $details['password_changed'] = true;
        }

        // DNS record add
        if (str_contains($path, '/dns') && $request->method() === 'POST') {
            $details['dns'] = array_intersect_key($input, array_flip(['type', 'name', 'content', 'proxied']));
        }

        return $details;
    }

    private function guessResourceType(Request $request): ?string
    {
        $path = $request->path();

        if (str_contains($path, '/domains')) return 'domain';
        if (str_contains($path, '/sites')) return 'site';
        if (str_contains($path, '/sponsors')) return 'sponsor';
        if (str_contains($path, '/pages')) return 'page';
        if (str_contains($path, '/posts')) return 'post';
        if (str_contains($path, '/users')) return 'user';
        if (str_contains($path, '/redirects')) return 'redirect';

        return null;
    }
}
