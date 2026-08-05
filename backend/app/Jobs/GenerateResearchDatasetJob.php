<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Research\ResearchDatasetEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateResearchDatasetJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $datasetType = 'wallet_behavior',
        public string $version = '1.0.0'
    ) {}

    public function handle(ResearchDatasetEngine $datasetEngine): void
    {
        $datasetEngine->buildDataset($this->datasetType, $this->version);
    }
}
