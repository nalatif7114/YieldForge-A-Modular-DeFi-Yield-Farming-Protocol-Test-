<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardSnapshot extends Model
{
    protected $fillable = [
        'wallet',
        'token_address',
        'balance_raw',
        'balance_formatted',
        'pending_rewards_raw',
        'pending_rewards_formatted',
        'block_number',
    ];

    protected $casts = [
        'block_number' => 'integer',
    ];
}
