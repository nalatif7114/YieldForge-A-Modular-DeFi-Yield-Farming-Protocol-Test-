<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolAnalytics extends Model
{
    protected $table = 'pool_analytics';

    protected $fillable = [
        'pool_id',
        'analytics_version',
        'tvl_raw',
        'tvl_formatted',
        'active_stakers',
        'average_stake_formatted',
        'average_lock_duration',
        'average_apy',
        'deposit_volume_formatted',
        'withdrawal_volume_formatted',
        'utilization_rate',
        'pool_growth_percentage',
        'timestamp',
    ];

    protected $casts = [
        'active_stakers' => 'integer',
        'average_lock_duration' => 'integer',
        'average_apy' => 'float',
        'utilization_rate' => 'float',
        'pool_growth_percentage' => 'float',
        'timestamp' => 'datetime',
    ];
}
