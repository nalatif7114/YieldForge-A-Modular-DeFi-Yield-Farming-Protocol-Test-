<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureSet extends Model
{
    protected $fillable = [
        'name',
        'version',
        'feature_count',
        'metadata',
    ];

    protected $casts = [
        'feature_count' => 'integer',
        'metadata' => 'array',
    ];
}
