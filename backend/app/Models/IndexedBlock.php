<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndexedBlock extends Model
{
    protected $fillable = [
        'chain_id',
        'network',
        'block_number',
        'block_hash',
        'parent_hash',
        'timestamp',
        'status',
        'events_count',
    ];

    protected $casts = [
        'chain_id' => 'integer',
        'block_number' => 'integer',
        'events_count' => 'integer',
        'timestamp' => 'datetime',
    ];
}
