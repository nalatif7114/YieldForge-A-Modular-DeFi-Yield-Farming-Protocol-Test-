<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Monitoring\AlertEngineService;
use Illuminate\Console\Command;

class MonitoringEvaluateCommand extends Command
{
    protected $signature = 'monitoring:evaluate';

    protected $description = 'Evaluate operational monitoring rules and persist active/resolved alerts';

    public function handle(AlertEngineService $alertEngine): int
    {
        $this->info('Evaluating monitoring alert rules...');

        $alerts = $alertEngine->evaluateRules();
        $count = count($alerts);

        $this->info("Evaluation complete: {$count} active alert(s) detected.");
        foreach ($alerts as $a) {
            $this->line(" - [{$a->severity}] {$a->ruleName}: {$a->message}");
        }

        return Command::SUCCESS;
    }
}
