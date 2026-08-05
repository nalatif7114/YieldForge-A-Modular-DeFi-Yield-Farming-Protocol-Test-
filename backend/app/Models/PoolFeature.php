<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolFeature extends Model
{
    protected $fillable = [
        'pool_id',
        'total_staked_formatted',
        'active_stakers_count',
        'transaction_velocity',
        'utilization_rate',
        'feature_version',
    ];

    protected $casts = [
        'pool_id' => 'integer',
        'active_stakers_count' => 'integer',
        'transaction_velocity' => 'float',
        'utilization_rate' => 'float',
    ];
}
