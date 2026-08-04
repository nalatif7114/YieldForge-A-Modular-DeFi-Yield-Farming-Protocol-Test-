<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use App\Models\BlockchainEvent;
use App\Models\MonitoringSnapshot;
use Illuminate\Database\Eloquent\Collection;

class ExportService
{
    /**
     * Export blockchain events in CSV or JSON format.
     *
     * @param string $format
     * @param int $limit
     * @return array{content: string, mime_type: string, filename: string}
     */
    public function exportEvents(string $format = 'json', int $limit = 500): array
    {
        /** @var Collection<int, BlockchainEvent> $events */
        $events = BlockchainEvent::query()
            ->orderByDesc('block_number')
            ->orderByDesc('log_index')
            ->limit($limit)
            ->get();

        if ($format === 'csv') {
            $csvHeader = "id,event_name,block_number,transaction_hash,contract_address,timestamp\n";
            $csvRows = [];
            foreach ($events as $e) {
                $csvRows[] = sprintf(
                    '%d,"%s",%d,"%s","%s","%s"',
                    $e->id,
                    $e->event_name,
                    $e->block_number,
                    $e->transaction_hash,
                    $e->contract_address,
                    $e->timestamp?->toIso8601String() ?? ''
                );
            }
            return [
                'content' => $csvHeader . implode("\n", $csvRows),
                'mime_type' => 'text/csv',
                'filename' => 'blockchain_events_export.csv',
            ];
        }

        return [
            'content' => json_encode($events->toArray(), JSON_PRETTY_PRINT),
            'mime_type' => 'application/json',
            'filename' => 'blockchain_events_export.json',
        ];
    }

    /**
     * Export monitoring metrics in CSV or JSON format.
     *
     * @param string $format
     * @param int $limit
     * @return array{content: string, mime_type: string, filename: string}
     */
    public function exportMetrics(string $format = 'json', int $limit = 500): array
    {
        /** @var Collection<int, MonitoringSnapshot> $snapshots */
        $snapshots = MonitoringSnapshot::query()
            ->orderByDesc('timestamp')
            ->limit($limit)
            ->get();

        if ($format === 'csv') {
            $csvHeader = "id,health_score,indexer_lag,rpc_latency_ms,queue_pending_jobs,queue_failed_jobs,cache_hit_ratio,active_alerts_count,timestamp\n";
            $csvRows = [];
            foreach ($snapshots as $s) {
                $csvRows[] = sprintf(
                    '%d,%d,%d,%.2f,%d,%d,%.2f,%d,"%s"',
                    $s->id,
                    $s->health_score,
                    $s->indexer_lag,
                    $s->rpc_latency_ms,
                    $s->queue_pending_jobs,
                    $s->queue_failed_jobs,
                    $s->cache_hit_ratio,
                    $s->active_alerts_count,
                    $s->timestamp?->toIso8601String() ?? ''
                );
            }
            return [
                'content' => $csvHeader . implode("\n", $csvRows),
                'mime_type' => 'text/csv',
                'filename' => 'monitoring_metrics_export.csv',
            ];
        }

        return [
            'content' => json_encode($snapshots->toArray(), JSON_PRETTY_PRINT),
            'mime_type' => 'application/json',
            'filename' => 'monitoring_metrics_export.json',
        ];
    }
}
