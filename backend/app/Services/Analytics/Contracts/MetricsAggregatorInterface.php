<?php

declare(strict_types=1);

namespace App\Services\Analytics\Contracts;

interface MetricsAggregatorInterface
{
    public function aggregateHourly(): void;

    public function aggregateDaily(): void;
}
