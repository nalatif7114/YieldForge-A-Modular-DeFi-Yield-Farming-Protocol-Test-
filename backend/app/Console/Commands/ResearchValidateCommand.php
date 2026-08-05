<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Research\DataQualityEngine;
use Illuminate\Console\Command;

class ResearchValidateCommand extends Command
{
    protected $signature = 'research:validate {--dataset=all}';

    protected $description = 'Validate data quality, missing values, duplicate events, and outliers for research datasets';

    public function handle(DataQualityEngine $qualityEngine): int
    {
        $dataset = (string) $this->option('dataset');
        $this->info("Validating YieldForge Research Datasets Data Quality...");

        $res = $qualityEngine->validateDataset($dataset);

        $this->line("Quality Score: {$res['quality_score']}/100");
        $this->line("Issues Found: {$res['issues_count']}");
        $this->line("Health Status: {$res['status']}");

        foreach ($res['checks'] as $checkName => $check) {
            $status = $check['passed'] ? '[PASS]' : '[FAIL]';
            $this->line(" {$status} {$checkName}: {$check['message']}");
        }

        if ($res['status'] === 'failed') {
            $this->error('Data Quality Validation Failed.');
            return Command::FAILURE;
        }

        $this->info('Data Quality Validation Passed cleanly.');

        return Command::SUCCESS;
    }
}
