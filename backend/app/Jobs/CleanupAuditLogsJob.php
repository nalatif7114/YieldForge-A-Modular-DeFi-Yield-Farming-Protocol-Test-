<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupAuditLogsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $daysToKeep = 90
    ) {}

    public function handle(): void
    {
        AuditLog::where('created_at', '<', now()->subDays($this->daysToKeep))->delete();
    }
}
