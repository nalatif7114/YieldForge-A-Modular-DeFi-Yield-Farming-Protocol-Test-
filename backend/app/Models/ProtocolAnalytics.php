<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtocolAnalytics extends Model
{
    protected $table = 'protocol_analytics';

    protected $fillable = [
        'analytics_version',
        'tvl_daily_change_percentage',
        'tvl_weekly_change_percentage',
        'tvl_monthly_change_percentage',
        'active_wallets_count',
        'new_wallets_count',
        'returning_wallets_count',
        'active_pools_count',
        'total_transactions_count',
        'capital_efficiency_ratio',
        'historical_high_tvl_formatted',
        'historical_low_tvl_formatted',
        'timestamp',
    ];

    protected $casts = [
        'tvl_daily_change_percentage' => 'float',
        'tvl_weekly_change_percentage' => 'float',
        'tvl_monthly_change_percentage' => 'float',
        'active_wallets_count' => 'integer',
        'new_wallets_count' => 'integer',
        'returning_wallets_count' => 'integer',
        'active_pools_count' => 'integer',
        'total_transactions_count' => 'integer',
        'capital_efficiency_ratio' => 'float',
        'timestamp' => 'datetime',
    ];
}
