<?php

declare(strict_types=1);

namespace App\Services\Monitoring\DTO;

readonly class CacheMetricsDTO
{
    public function __construct(
        public string $driver,
        public int $hits,
        public int $misses,
        public float $hitRatioPercentage,
        public int $cachedKeysCount,
        public array $namespaces,
        public string $status
    ) {}

    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'hits' => $this->hits,
            'misses' => $this->misses,
            'hit_ratio_percentage' => round($this->hitRatioPercentage, 2),
            'cached_keys_count' => $this->cachedKeysCount,
            'namespaces' => $this->namespaces,
            'status' => $this->status,
        ];
    }
}
