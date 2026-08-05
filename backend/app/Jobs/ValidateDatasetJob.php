<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Research\DataQualityEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ValidateDatasetJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $datasetName = 'default'
    ) {}

    public function handle(DataQualityEngine $qualityEngine): void
    {
        $qualityEngine->validateDataset($this->datasetName);
    }
}
