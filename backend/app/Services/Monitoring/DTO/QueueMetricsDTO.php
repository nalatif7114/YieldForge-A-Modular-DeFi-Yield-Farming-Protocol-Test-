<?php

declare(strict_types=1);

namespace App\Services\Monitoring\DTO;

readonly class QueueMetricsDTO
{
    public function __construct(
        public string $connection,
        public int $pendingJobs,
        public int $failedJobs,
        public int $processedJobsCount,
        public float $throughputPerMinute,
        public string $status
    ) {}

    public function toArray(): array
    {
        return [
            'connection' => $this->connection,
            'pending_jobs' => $this->pendingJobs,
            'failed_jobs' => $this->failedJobs,
            'processed_jobs_count' => $this->processedJobsCount,
            'throughput_per_minute' => round($this->throughputPerMinute, 2),
            'status' => $this->status,
        ];
    }
}
