<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Monitoring\AlertEngineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EvaluateAlertRulesJob implements ShouldQueue
{
    use Queueable;

    public function handle(AlertEngineService $alertEngine): void
    {
        $alertEngine->evaluateRules();
    }
}
