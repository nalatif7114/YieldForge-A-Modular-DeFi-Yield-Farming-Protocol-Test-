<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    protected $table = 'daily_statistics';

    protected $fillable = [
        'analytics_version',
        'timestamp',
        'tvl_formatted',
        'apy',
        'volume_formatted',
        'tx_count',
        'active_users',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'apy' => 'float',
        'tx_count' => 'integer',
        'active_users' => 'integer',
    ];
}
