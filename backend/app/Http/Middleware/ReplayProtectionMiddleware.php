<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ReplayProtectionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = $request->header('X-Nonce') ?? $request->input('nonce');

        if ($nonce) {
            $key = "replay_nonce:{$nonce}";
            if (Cache::has($key)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Replay attack detected: Nonce has already been processed.',
                ], 409);
            }
            Cache::put($key, true, 300); // 5 minutes TTL
        }

        return $next($request);
    }
}
