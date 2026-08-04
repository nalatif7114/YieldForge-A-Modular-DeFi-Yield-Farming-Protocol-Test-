<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Security\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    public function __construct(
        private readonly JwtService $jwtService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing or malformed Authorization header.',
            ], 401);
        }

        $token = substr($authHeader, 7);
        $payload = $this->jwtService->validateToken($token);

        if (!$payload || !isset($payload['sub'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired JWT token.',
            ], 401);
        }

        /** @var User|null $user */
        $user = User::with('roles.permissions')->find($payload['sub']);
        if (!$user || !$user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'User account is inactive or disabled.',
            ], 401);
        }

        $request->attributes->set('auth_user', $user);
        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }
}
