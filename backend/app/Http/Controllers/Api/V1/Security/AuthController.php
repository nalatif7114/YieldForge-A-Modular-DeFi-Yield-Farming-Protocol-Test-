<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Security;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\Security\AuditLoggerService;
use App\Services\Security\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly AuditLoggerService $auditLogger
    ) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');
        $ip = $request->ip();

        /** @var User|null $user */
        $user = User::with('roles.permissions')->where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $this->auditLogger->logLoginAttempt($email, $ip, false);
            $this->auditLogger->logSecurityEvent('FailedLoginAttempt', 'warning', $ip, ['email' => $email]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account is disabled.',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);
        $this->auditLogger->logLoginAttempt($email, $ip, true);
        $this->auditLogger->logAction($user, 'UserLogin', 'Auth', ['email' => $email], $ip, $request->userAgent());

        $accessToken = $this->jwtService->issueAccessToken($user);

        $rawRefreshToken = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $rawRefreshToken),
            'device_info' => $request->userAgent(),
            'ip_address' => $ip,
            'expires_at' => now()->addDays(14),
            'revoked' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $rawRefreshToken,
                'token_type' => 'Bearer',
                'expires_in_seconds' => 3600,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'wallet_address' => $user->wallet_address,
                    'roles' => $user->roles->pluck('slug')->all(),
                ],
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->attributes->get('auth_user');

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $roles = $user->roles->pluck('slug')->all();
        $permissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $perm) {
                $permissions[] = $perm->slug;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'wallet_address' => $user->wallet_address,
                'roles' => array_values(array_unique($roles)),
                'permissions' => array_values(array_unique($permissions)),
                'last_login_at' => $user->last_login_at?->toIso8601String(),
            ],
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $rawToken = (string) $request->input('refresh_token');
        $tokenHash = hash('sha256', $rawToken);

        /** @var RefreshToken|null $refreshToken */
        $refreshToken = RefreshToken::with('user.roles.permissions')
            ->where('token_hash', $tokenHash)
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$refreshToken || !$refreshToken->user || !$refreshToken->user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, revoked, or expired refresh token.',
            ], 401);
        }

        $user = $refreshToken->user;
        $accessToken = $this->jwtService->issueAccessToken($user);

        $newRawRefreshToken = Str::random(64);
        $refreshToken->update(['revoked' => true]);

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $newRawRefreshToken),
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(14),
            'revoked' => false,
        ]);

        $this->auditLogger->logAction($user, 'TokenRefresh', 'Auth', [], $request->ip(), $request->userAgent());

        return response()->json([
            'status' => 'success',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $newRawRefreshToken,
                'token_type' => 'Bearer',
                'expires_in_seconds' => 3600,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->attributes->get('auth_user');

        if ($user) {
            RefreshToken::where('user_id', $user->id)->update(['revoked' => true]);
            $this->auditLogger->logAction($user, 'UserLogout', 'Auth', [], $request->ip(), $request->userAgent());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }
}
