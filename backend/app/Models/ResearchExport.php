<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchExport extends Model
{
    protected $fillable = [
        'dataset_name',
        'format',
        'row_count',
        'file_name',
    ];

    protected $casts = [
        'row_count' => 'integer',
    ];
}
