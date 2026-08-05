<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetVersion extends Model
{
    protected $fillable = [
        'dataset_id',
        'version',
        'checksum',
        'file_path',
        'row_count',
    ];

    protected $casts = [
        'row_count' => 'integer',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(ResearchDataset::class, 'dataset_id');
    }
}
