<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletPosition extends Model
{
    protected $fillable = [
        'wallet',
        'staked_balance_raw',
        'staked_balance_formatted',
        'token_balance_raw',
        'token_balance_formatted',
        'pool_share_percentage',
        'last_updated_block',
    ];

    protected $casts = [
        'pool_share_percentage' => 'float',
        'last_updated_block' => 'integer',
    ];
}
