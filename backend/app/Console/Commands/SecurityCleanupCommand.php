<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\RefreshToken;
use App\Models\WalletNonce;
use Illuminate\Console\Command;

class SecurityCleanupCommand extends Command
{
    protected $signature = 'security:cleanup {--days=90}';

    protected $description = 'Purge expired refresh tokens, used nonces, and stale audit logs';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Purging expired security records older than {$days} days...");

        $expiredTokens = RefreshToken::where('expires_at', '<', now())->orWhere('revoked', true)->delete();
        $expiredNonces = WalletNonce::where('expires_at', '<', now())->orWhere('used', true)->delete();
        $staleLogs = AuditLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Cleanup completed:");
        $this->line(" - Expired/Revoked Refresh Tokens Deleted: {$expiredTokens}");
        $this->line(" - Used/Expired Wallet Nonces Deleted: {$expiredNonces}");
        $this->line(" - Stale Audit Logs Deleted: {$staleLogs}");

        return Command::SUCCESS;
    }
}
