<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\AnalyticsSnapshot;
use App\Models\IndexedBlock;

class HealthScoreCalculator
{
    public function getAnalyticsDelayMs(): float
    {
        /** @var AnalyticsSnapshot|null $latest */
        $latest = AnalyticsSnapshot::orderByDesc('timestamp')->first();
        if (!$latest) {
            return 0.0;
        }

        $diffSec = max(0, now()->getTimestamp() - $latest->timestamp->getTimestamp());

        return round($diffSec * 1000.0, 2);
    }

    public function getHealthScore(): float
    {
        $delayMs = $this->getAnalyticsDelayMs();
        if ($delayMs <= 300000) { // < 5 mins
            return 100.0;
        }
        if ($delayMs <= 900000) { // < 15 mins
            return 85.0;
        }

        return 60.0;
    }
}
