<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolSnapshot extends Model
{
    protected $fillable = [
        'pool_id',
        'contract_address',
        'staking_token_address',
        'staking_token_name',
        'staking_token_symbol',
        'staking_token_decimals',
        'total_staked_raw',
        'total_staked_formatted',
        'is_paused',
        'block_number',
    ];

    protected $casts = [
        'staking_token_decimals' => 'integer',
        'is_paused' => 'boolean',
        'block_number' => 'integer',
    ];
}
