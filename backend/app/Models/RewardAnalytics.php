<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardAnalytics extends Model
{
    protected $table = 'reward_analytics';

    protected $fillable = [
        'analytics_version',
        'total_rewards_distributed_raw',
        'total_rewards_distributed_formatted',
        'reward_velocity',
        'top_earners',
        'timestamp',
    ];

    protected $casts = [
        'reward_velocity' => 'float',
        'top_earners' => 'array',
        'timestamp' => 'datetime',
    ];
}
