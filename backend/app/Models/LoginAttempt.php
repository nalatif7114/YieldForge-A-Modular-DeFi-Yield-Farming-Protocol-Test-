<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'identity',
        'ip_address',
        'successful',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];
}
