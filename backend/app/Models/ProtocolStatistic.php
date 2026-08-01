<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtocolStatistic extends Model
{
    protected $fillable = [
        'total_value_locked_raw',
        'total_value_locked_formatted',
        'total_stakers_count',
        'total_events_processed',
        'total_tokens_minted_raw',
        'total_tokens_burned_raw',
        'latest_indexed_block',
    ];

    protected $casts = [
        'total_stakers_count' => 'integer',
        'total_events_processed' => 'integer',
        'latest_indexed_block' => 'integer',
    ];
}
