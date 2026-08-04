<?php

declare(strict_types=1);

namespace App\Services\Monitoring\DTO;

readonly class MonitoringDashboardDTO
{
    public function __construct(
        public int $healthScore,
        public string $overallStatus,
        public array $indexer,
        public QueueMetricsDTO $queue,
        public CacheMetricsDTO $cache,
        public RpcMetricsDTO $rpc,
        public array $alertsSummary,
        public array $protocolKpis,
        public string $timestamp
    ) {}

    public function toArray(): array
    {
        return [
            'health_score' => $this->healthScore,
            'overall_status' => $this->overallStatus,
            'indexer' => $this->indexer,
            'queue' => $this->queue->toArray(),
            'cache' => $this->cache->toArray(),
            'rpc' => $this->rpc->toArray(),
            'alerts_summary' => $this->alertsSummary,
            'protocol_kpis' => $this->protocolKpis,
            'timestamp' => $this->timestamp,
        ];
    }
}
