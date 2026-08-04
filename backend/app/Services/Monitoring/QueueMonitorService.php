<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use App\Services\Monitoring\DTO\QueueMetricsDTO;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

class QueueMonitorService
{
    public function __construct(
        private readonly ConfigRepository $config
    ) {}

    public function getMetrics(): QueueMetricsDTO
    {
        $connection = (string) $this->config->get('queue.default', 'database');

        $pendingJobs = 0;
        $failedJobs = 0;

        try {
            if (DB::getSchemaBuilder()->hasTable('jobs')) {
                $pendingJobs = (int) DB::table('jobs')->count();
            }
            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = (int) DB::table('failed_jobs')->count();
            }
        } catch (Throwable) {
            // Database fallback
        }

        $processedJobsCount = max(0, $pendingJobs + $failedJobs);
        $throughputPerMinute = $pendingJobs > 0 ? (float) ($pendingJobs / 5.0) : 0.0;

        $status = match (true) {
            $failedJobs > 5 => 'unhealthy',
            $failedJobs > 0 || $pendingJobs > 500 => 'degraded',
            default => 'healthy',
        };

        return new QueueMetricsDTO(
            connection: $connection,
            pendingJobs: $pendingJobs,
            failedJobs: $failedJobs,
            processedJobsCount: $processedJobsCount,
            throughputPerMinute: $throughputPerMinute,
            status: $status
        );
    }
}
