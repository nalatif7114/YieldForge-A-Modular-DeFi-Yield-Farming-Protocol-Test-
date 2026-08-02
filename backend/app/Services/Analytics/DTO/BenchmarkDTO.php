<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class BenchmarkDTO
{
    public function __construct(
        public float $tvl24hChangePercentage,
        public float $tvl7dChangePercentage,
        public float $apy30dAverage,
        public string $historicalHighTvlFormatted,
        public string $historicalLowTvlFormatted
    ) {}

    public function toArray(): array
    {
        return [
            'tvl_24h_change_percentage' => $this->tvl24hChangePercentage,
            'tvl_7d_change_percentage' => $this->tvl7dChangePercentage,
            'apy_30d_average' => $this->apy30dAverage,
            'historical_high_tvl_formatted' => $this->historicalHighTvlFormatted,
            'historical_low_tvl_formatted' => $this->historicalLowTvlFormatted,
        ];
    }
}
