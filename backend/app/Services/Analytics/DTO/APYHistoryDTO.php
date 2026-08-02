<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class APYHistoryDTO
{
    /**
     * @param array<int, ChartPointDTO> $chartPoints
     */
    public function __construct(
        public float $averageApy,
        public float $highestApy,
        public float $lowestApy,
        public array $chartPoints = []
    ) {}

    public function toArray(): array
    {
        return [
            'average_apy' => $this->averageApy,
            'highest_apy' => $this->highestApy,
            'lowest_apy' => $this->lowestApy,
            'points' => array_map(fn (ChartPointDTO $point) => $point->toArray(), $this->chartPoints),
        ];
    }
}
