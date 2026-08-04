<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Security;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Services\Security\AuditLoggerService;
use App\Services\Security\JwtService;
use App\Services\Security\SiweAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiweAuthController extends Controller
{
    public function __construct(
        private readonly SiweAuthService $siweAuthService,
        private readonly JwtService $jwtService,
        private readonly AuditLoggerService $auditLogger
    ) {}

    public function nonce(Request $request): JsonResponse
    {
        $wallet = (string) $request->query('wallet_address', '');
        if ($wallet === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Query parameter wallet_address is required.',
            ], 422);
        }

        $nonceRecord = $this->siweAuthService->generateNonce($wallet);

        return response()->json([
            'status' => 'success',
            'data' => [
                'nonce' => $nonceRecord->nonce,
                'wallet_address' => $nonceRecord->wallet_address,
                'expires_at' => $nonceRecord->expires_at->toIso8601String(),
                'statement' => 'Sign in with Ethereum to YieldForge Protocol Operations Platform.',
            ],
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'signature' => 'required|string',
            'nonce' => 'required|string',
        ]);

        $wallet = (string) $request->input('wallet_address');
        $signature = (string) $request->input('signature');
        $nonce = (string) $request->input('nonce');
        $ip = $request->ip();

        $user = $this->siweAuthService->verifySignature($wallet, $signature, $nonce);

        if (!$user) {
            $this->auditLogger->logSecurityEvent('FailedWalletAuth', 'warning', $ip, ['wallet' => $wallet]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid wallet signature, expired nonce, or replayed request.',
            ], 401);
        }

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

        $this->auditLogger->logAction($user, 'WalletLogin', 'Auth', ['wallet' => $wallet], $ip, $request->userAgent());

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
}
