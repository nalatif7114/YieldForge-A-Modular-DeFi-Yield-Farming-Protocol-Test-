<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSnapshot extends Model
{
    protected $fillable = [
        'chain_id',
        'network',
        'snapshot_type',
        'analytics_version',
        'total_tvl_raw',
        'total_tvl_formatted',
        'average_apy',
        'active_stakers',
        'total_rewards_raw',
        'metric_name',
        'metric_value',
        'aggregation_window',
        'source',
        'metadata',
        'timestamp',
    ];

    protected $casts = [
        'chain_id' => 'integer',
        'average_apy' => 'float',
        'active_stakers' => 'integer',
        'metric_value' => 'float',
        'metadata' => 'array',
        'timestamp' => 'datetime',
    ];
}
