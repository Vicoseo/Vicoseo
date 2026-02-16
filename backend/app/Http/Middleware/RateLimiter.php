<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter as CacheRateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimiter
{
    public function __construct(private CacheRateLimiter $limiter) {}

    public function handle(Request $request, Closure $next, string $type = 'public'): Response
    {
        $key = $this->resolveKey($request, $type);
        $maxAttempts = $type === 'admin' ? 60 : 120;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
            ], 429)->withHeaders([
                'Retry-After' => $this->limiter->availableIn($key),
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $this->limiter->hit($key, 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $this->limiter->remaining($key, $maxAttempts),
        ]);
    }

    private function resolveKey(Request $request, string $type): string
    {
        $identifier = $request->user()?->id ?? $request->ip();

        return "rate_limit:{$type}:{$identifier}";
    }
}
