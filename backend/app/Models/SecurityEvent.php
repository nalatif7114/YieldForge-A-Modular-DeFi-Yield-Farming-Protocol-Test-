<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = [
        'event_type',
        'severity',
        'ip_address',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}
