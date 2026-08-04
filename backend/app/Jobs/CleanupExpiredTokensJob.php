<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\RefreshToken;
use App\Models\WalletNonce;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupExpiredTokensJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        RefreshToken::where('expires_at', '<', now())->orWhere('revoked', true)->delete();
        WalletNonce::where('expires_at', '<', now())->orWhere('used', true)->delete();
    }
}
