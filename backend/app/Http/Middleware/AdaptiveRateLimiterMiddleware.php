<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AdaptiveRateLimiterMiddleware
{
    public function handle(Request $request, Closure $next, int $maxRequests = 60, int $decaySeconds = 60): Response
    {
        $user = $request->attributes->get('auth_user');
        $apiKey = $request->attributes->get('api_key');

        if ($user) {
            $identifier = "user:{$user->id}";
            $maxRequests = $user->hasRole('admin') ? 1000 : 300;
        } elseif ($apiKey) {
            $identifier = "apikey:{$apiKey->id}";
            $maxRequests = 600;
        } else {
            $identifier = "ip:" . ($request->ip() ?? '127.0.0.1');
        }

        $cacheKey = "rate_limit:{$identifier}:" . floor(time() / $decaySeconds);
        $currentCount = (int) Cache::get($cacheKey, 0);

        if ($currentCount >= $maxRequests) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too Many Requests: Rate limit exceeded.',
            ], 429, [
                'X-RateLimit-Limit' => (string) $maxRequests,
                'X-RateLimit-Remaining' => '0',
                'Retry-After' => (string) $decaySeconds,
            ]);
        }

        Cache::put($cacheKey, $currentCount + 1, $decaySeconds);

        $response = $next($request);
        $response->headers->set('X-RateLimit-Limit', (string) $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', (string) max(0, $maxRequests - ($currentCount + 1)));

        return $response;
    }
}
