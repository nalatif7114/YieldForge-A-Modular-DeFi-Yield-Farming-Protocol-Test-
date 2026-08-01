<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $table = 'transaction_history';

    protected $fillable = [
        'transaction_hash',
        'wallet',
        'event_name',
        'amount_raw',
        'amount_formatted',
        'block_number',
        'timestamp',
    ];

    protected $casts = [
        'block_number' => 'integer',
        'timestamp' => 'datetime',
    ];
}
