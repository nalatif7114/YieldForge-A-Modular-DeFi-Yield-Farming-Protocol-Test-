<?php

declare(strict_types=1);

namespace App\Services\Indexer\DTO;

readonly class IndexerMetricsDTO
{
    public function __construct(
        public float $eventsPerSec,
        public float $blocksPerSec,
        public float $avgRpcLatencyMs,
        public float $projectionLatencyMs,
        public float $queueLatencyMs,
        public float $replayDurationMs,
        public float $cacheHitRatio,
        public int $retryCount,
        public int $failedProjections
    ) {}

    public function toArray(): array
    {
        return [
            'events_per_sec' => $this->eventsPerSec,
            'blocks_per_sec' => $this->blocksPerSec,
            'avg_rpc_latency_ms' => $this->avgRpcLatencyMs,
            'projection_latency_ms' => $this->projectionLatencyMs,
            'queue_latency_ms' => $this->queueLatencyMs,
            'replay_duration_ms' => $this->replayDurationMs,
            'cache_hit_ratio' => $this->cacheHitRatio,
            'retry_count' => $this->retryCount,
            'failed_projections' => $this->failedProjections,
        ];
    }
}
