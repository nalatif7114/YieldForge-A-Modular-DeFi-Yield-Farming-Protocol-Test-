<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Security;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\RefreshToken;
use App\Models\SecurityEvent;
use App\Services\Security\SecurityDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityMonitoringController extends Controller
{
    public function __construct(
        private readonly SecurityDashboardService $securityDashboardService
    ) {}

    public function dashboard(): JsonResponse
    {
        $data = $this->securityDashboardService->getDashboardMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function audit(Request $request): JsonResponse
    {
        $action = $request->query('action');
        $limit = (int) $request->query('limit', 50);

        $query = AuditLog::with('user:id,name,email')->orderByDesc('created_at');
        if ($action && is_string($action)) {
            $query->where('action', $action);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->limit($limit)->get(),
        ]);
    }

    public function rateLimit(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'anonymous_limit' => 60,
                'authenticated_limit' => 300,
                'admin_limit' => 1000,
                'api_key_limit' => 600,
                'window_seconds' => 60,
                'limiter_driver' => config('cache.default'),
            ],
        ]);
    }

    public function sessions(): JsonResponse
    {
        $activeSessions = RefreshToken::with('user:id,name,email,wallet_address')
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $activeSessions,
        ]);
    }
}
