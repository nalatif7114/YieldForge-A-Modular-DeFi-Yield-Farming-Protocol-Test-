<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringAlert extends Model
{
    protected $table = 'monitoring_alerts';

    protected $fillable = [
        'rule_name',
        'severity',
        'component',
        'message',
        'status',
        'context',
        'acknowledged_at',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'array',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
}
