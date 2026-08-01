<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainEvent extends Model
{
    protected $fillable = [
        'chain_id',
        'network',
        'block_number',
        'block_hash',
        'transaction_hash',
        'transaction_index',
        'log_index',
        'contract_address',
        'event_name',
        'event_version',
        'contract_version',
        'payload',
        'removed',
        'timestamp',
    ];

    protected $casts = [
        'chain_id' => 'integer',
        'block_number' => 'integer',
        'transaction_index' => 'integer',
        'log_index' => 'integer',
        'payload' => 'array',
        'removed' => 'boolean',
        'timestamp' => 'datetime',
    ];
}
