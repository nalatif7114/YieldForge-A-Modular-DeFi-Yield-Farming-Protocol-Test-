<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringSnapshot extends Model
{
    protected $table = 'monitoring_snapshots';

    protected $fillable = [
        'health_score',
        'indexer_lag',
        'rpc_latency_ms',
        'queue_pending_jobs',
        'queue_failed_jobs',
        'cache_hit_ratio',
        'active_alerts_count',
        'metrics',
        'timestamp',
    ];

    protected $casts = [
        'health_score' => 'integer',
        'indexer_lag' => 'integer',
        'rpc_latency_ms' => 'float',
        'queue_pending_jobs' => 'integer',
        'queue_failed_jobs' => 'integer',
        'cache_hit_ratio' => 'float',
        'active_alerts_count' => 'integer',
        'metrics' => 'array',
        'timestamp' => 'datetime',
    ];
}
