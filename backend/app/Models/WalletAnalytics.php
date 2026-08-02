<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletAnalytics extends Model
{
    protected $table = 'wallet_analytics';

    protected $fillable = [
        'wallet',
        'analytics_version',
        'tvl_raw',
        'tvl_formatted',
        'rewards_raw',
        'rewards_formatted',
        'pending_rewards_raw',
        'pending_rewards_formatted',
        'roi_percentage',
        'apy_percentage',
        'compounded_yield_formatted',
        'pool_allocation',
        'diversification_score',
        'concentration_risk',
        'largest_pool_exposure',
        'impermanent_risk_estimate',
        'reward_dependency_ratio',
        'last_active_at',
        'timestamp',
    ];

    protected $casts = [
        'roi_percentage' => 'float',
        'apy_percentage' => 'float',
        'pool_allocation' => 'array',
        'diversification_score' => 'float',
        'concentration_risk' => 'float',
        'impermanent_risk_estimate' => 'float',
        'reward_dependency_ratio' => 'float',
        'last_active_at' => 'datetime',
        'timestamp' => 'datetime',
    ];
}
