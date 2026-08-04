<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Security\RbacService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RbacMiddleware
{
    public function __construct(
        private readonly RbacService $rbacService
    ) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->attributes->get('auth_user');
        $apiKey = $request->attributes->get('api_key');

        $actor = $user ?? $apiKey;

        if (!$actor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated actor.',
            ], 401);
        }

        if (!$this->rbacService->hasPermission($actor, $permission)) {
            return response()->json([
                'status' => 'error',
                'message' => "Forbidden: Required permission [{$permission}] not granted.",
            ], 403);
        }

        return $next($request);
    }
}
