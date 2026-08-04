<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Security;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\User;
use App\Services\Security\ApiKeyService;
use App\Services\Security\AuditLoggerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyManagementController extends Controller
{
    public function __construct(
        private readonly ApiKeyService $apiKeyService,
        private readonly AuditLoggerService $auditLogger
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->attributes->get('auth_user');

        $query = ApiKey::query()->orderByDesc('created_at');
        if ($user && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scopes' => 'nullable|array',
            'ip_allowlist' => 'nullable|array',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        /** @var User|null $user */
        $user = $request->attributes->get('auth_user');

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $name = (string) $request->input('name');
        $scopes = (array) ($request->input('scopes') ?? ['monitoring.view', 'analytics.view']);
        $ipAllowlist = $request->input('ip_allowlist') ? (array) $request->input('ip_allowlist') : null;
        $expiresInDays = $request->input('expires_in_days') ? (int) $request->input('expires_in_days') : 90;

        $result = $this->apiKeyService->createApiKey(
            user: $user,
            name: $name,
            scopes: $scopes,
            ipAllowlist: $ipAllowlist,
            expiresInDays: $expiresInDays
        );

        $this->auditLogger->logAction($user, 'ApiKeyCreated', 'ApiKey', ['name' => $name], $request->ip(), $request->userAgent());

        return response()->json([
            'status' => 'success',
            'data' => [
                'api_key' => $result['model'],
                'raw_secret_key' => $result['raw_key'],
                'warning' => 'Store the raw_secret_key safely. It will not be shown again.',
            ],
        ], 201);
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->attributes->get('auth_user');

        /** @var ApiKey|null $apiKey */
        $apiKey = ApiKey::find($id);

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => "API Key ID #{$id} not found.",
            ], 404);
        }

        if ($user && !$user->hasRole('admin') && $apiKey->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: Cannot delete another user\'s API key.',
            ], 403);
        }

        $apiKey->delete();

        $this->auditLogger->logAction($user, 'ApiKeyRevoked', 'ApiKey', ['id' => $id], $request->ip(), $request->userAgent());

        return response()->json([
            'status' => 'success',
            'message' => "API Key ID #{$id} revoked successfully.",
        ]);
    }
}
