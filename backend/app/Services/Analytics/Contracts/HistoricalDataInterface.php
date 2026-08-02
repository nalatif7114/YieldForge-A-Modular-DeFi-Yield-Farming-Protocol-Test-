<?php

declare(strict_types=1);

namespace App\Services\Analytics\Contracts;

interface HistoricalDataInterface
{
    /**
     * @param string $metric
     * @param string $window
     * @return array<int, \App\Services\Analytics\DTO\ChartPointDTO>
     */
    public function getChartData(string $metric, string $window = '30d'): array;

    public function cleanupOldData(): int;
}
