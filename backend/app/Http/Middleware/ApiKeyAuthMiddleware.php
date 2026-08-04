<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Security\ApiKeyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthMiddleware
{
    public function __construct(
        private readonly ApiKeyService $apiKeyService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-Key');
        if (!$key) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && str_starts_with($authHeader, 'Bearer yf_')) {
                $key = substr($authHeader, 7);
            }
        }

        if (!$key) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing X-API-Key or Bearer yf_... header.',
            ], 401);
        }

        $apiKey = $this->apiKeyService->authenticate($key, $request->ip());
        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, expired, or unauthorized API Key.',
            ], 401);
        }

        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }
}
