<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Services\Analytics\Contracts\AnalyticsCacheInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class AnalyticsCache implements AnalyticsCacheInterface
{
    public function __construct(
        private readonly CacheRepository $cache
    ) {}

    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $duration = $ttl ?? (int) config('blockchain.analytics_cache_duration', 300);

        return $this->cache->remember('analytics:' . $key, $duration, $callback);
    }

    public function invalidateAll(): void
    {
        // Flush analytics cache keys
        $keys = [
            'analytics:overview',
            'analytics:tvl_history_30d',
            'analytics:apy_history_30d',
            'analytics:protocol',
            'analytics:pools',
            'analytics:rewards',
            'analytics:health',
        ];

        foreach ($keys as $k) {
            $this->cache->forget($k);
        }
    }
}
