<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Services\Analytics\Contracts\MetricsAggregatorInterface;

class MetricsAggregator implements MetricsAggregatorInterface
{
    public function __construct(
        private readonly TimeSeriesBuilder $timeSeriesBuilder
    ) {}

    public function aggregateHourly(): void
    {
        $this->timeSeriesBuilder->generateHourly();
    }

    public function aggregateDaily(): void
    {
        $this->timeSeriesBuilder->generateDaily();
    }
}
