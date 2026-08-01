<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectionCheckpoint extends Model
{
    protected $fillable = [
        'projection_name',
        'last_processed_block',
        'last_transaction_index',
        'last_log_index',
        'projection_version',
    ];

    protected $casts = [
        'last_processed_block' => 'integer',
        'last_transaction_index' => 'integer',
        'last_log_index' => 'integer',
    ];
}
