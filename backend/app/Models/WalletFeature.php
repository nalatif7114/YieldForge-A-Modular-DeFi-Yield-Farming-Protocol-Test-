<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletFeature extends Model
{
    protected $fillable = [
        'wallet_address',
        'wallet_age_days',
        'average_stake_formatted',
        'staking_frequency',
        'holding_duration_days',
        'reward_velocity',
        'stake_growth_pct',
        'unstake_ratio',
        'active_days',
        'transaction_interval_hours',
        'pool_diversity_count',
        'feature_version',
    ];

    protected $casts = [
        'wallet_age_days' => 'integer',
        'staking_frequency' => 'float',
        'holding_duration_days' => 'integer',
        'reward_velocity' => 'float',
        'stake_growth_pct' => 'float',
        'unstake_ratio' => 'float',
        'active_days' => 'integer',
        'transaction_interval_hours' => 'float',
        'pool_diversity_count' => 'integer',
    ];
}
