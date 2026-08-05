<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchDataset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'version',
        'row_count',
        'quality_score',
        'status',
    ];

    protected $casts = [
        'row_count' => 'integer',
        'quality_score' => 'integer',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(DatasetVersion::class, 'dataset_id');
    }
}
