<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\RefreshToken;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Console\Command;

class SecurityAuditCommand extends Command
{
    protected $signature = 'security:audit';

    protected $description = 'Perform security health audit and output summary of authentication, API keys, and events';

    public function handle(): int
    {
        $this->info('=== YieldForge Security Audit Report ===');

        $users = User::count();
        $activeKeys = ApiKey::whereNull('expires_at')->orWhere('expires_at', '>', now())->count();
        $activeTokens = RefreshToken::where('revoked', false)->where('expires_at', '>', now())->count();
        $failedLogins = LoginAttempt::where('successful', false)->where('created_at', '>=', now()->subHours(24))->count();
        $auditLogs = AuditLog::count();
        $securityEvents = SecurityEvent::count();

        $this->line("Total Users: {$users}");
        $this->line("Active API Keys: {$activeKeys}");
        $this->line("Active Refresh Tokens: {$activeTokens}");
        $this->line("Failed Logins (Last 24h): {$failedLogins}");
        $this->line("Total Audit Logs: {$auditLogs}");
        $this->line("Total Security Events: {$securityEvents}");

        if ($failedLogins > 10) {
            $this->warn("SECURITY WARNING: High number of failed login attempts detected ({$failedLogins} in 24h).");
        } else {
            $this->info('STATUS: Security posture healthy. No critical anomalies detected.');
        }

        return Command::SUCCESS;
    }
}
