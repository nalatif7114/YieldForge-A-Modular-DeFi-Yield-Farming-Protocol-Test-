<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\ApiKey;
use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\RefreshToken;
use App\Models\SecurityEvent;
use App\Models\User;

class SecurityDashboardService
{
    public function getDashboardMetrics(): array
    {
        $totalUsers = User::count();
        $activeApiKeys = ApiKey::whereNull('expires_at')
            ->orWhere('expires_at', '>', now())
            ->count();

        $activeSessions = RefreshToken::where('revoked', false)
            ->where('expires_at', '>', now())
            ->count();

        $failedLogins24h = LoginAttempt::where('successful', false)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $recentAuditLogs = AuditLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentSecurityEvents = SecurityEvent::orderByDesc('created_at')
            ->limit(10)
            ->get();

        $status = $failedLogins24h > 20 ? 'warning' : 'healthy';

        return [
            'total_users' => $totalUsers,
            'active_api_keys' => $activeApiKeys,
            'active_sessions' => $activeSessions,
            'failed_logins_24h' => $failedLogins24h,
            'status' => $status,
            'recent_audit_logs' => $recentAuditLogs,
            'recent_security_events' => $recentSecurityEvents,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
