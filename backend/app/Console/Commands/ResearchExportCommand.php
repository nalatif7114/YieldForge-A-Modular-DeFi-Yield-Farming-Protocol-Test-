<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Research\ResearchExportService;
use Illuminate\Console\Command;

class ResearchExportCommand extends Command
{
    protected $signature = 'research:export {type=wallet_behavior} {--format=json}';

    protected $description = 'Export research datasets to CSV or JSON file format';

    public function handle(ResearchExportService $exportService): int
    {
        $type = (string) $this->argument('type');
        $format = (string) $this->option('format');

        $this->info("Exporting dataset [{$type}] in {$format} format...");

        $export = $exportService->exportDataset($type, $format);

        $this->info("Export successful:");
        $this->line(" - Filename: {$export['filename']}");
        $this->line(" - Mime-Type: {$export['mime_type']}");
        $this->line(" - Row Count: {$export['row_count']}");

        return Command::SUCCESS;
    }
}
