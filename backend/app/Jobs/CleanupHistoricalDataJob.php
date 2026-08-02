<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Analytics\Contracts\HistoricalDataInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupHistoricalDataJob implements ShouldQueue
{
    use Queueable;

    public function handle(HistoricalDataInterface $historicalData): void
    {
        $historicalData->cleanupOldData();
    }
}
