<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\LoginAttempt;
use App\Models\SecurityEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class DetectSuspiciousActivityJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Detect IP addresses with > 10 failed login attempts in last 1 hour
        $suspiciousIps = LoginAttempt::query()
            ->select('ip_address', DB::raw('count(*) as failures'))
            ->where('successful', false)
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('ip_address')
            ->having('failures', '>', 10)
            ->get();

        foreach ($suspiciousIps as $item) {
            SecurityEvent::create([
                'event_type' => 'BruteForceDetected',
                'severity' => 'critical',
                'ip_address' => $item->ip_address,
                'details' => ['failed_attempts_last_hour' => $item->failures],
            ]);
        }
    }
}
