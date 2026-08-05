<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Research\ResearchExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExportDatasetJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $datasetType = 'wallet_behavior',
        public string $format = 'json'
    ) {}

    public function handle(ResearchExportService $exportService): void
    {
        $exportService->exportDataset($this->datasetType, $this->format);
    }
}
