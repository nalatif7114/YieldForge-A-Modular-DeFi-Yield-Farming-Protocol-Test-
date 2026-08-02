<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\AggregateDailyStatisticsJob;
use App\Jobs\AggregateHourlyStatisticsJob;
use App\Services\Analytics\Contracts\MetricsAggregatorInterface;
use Illuminate\Console\Command;

class AnalyticsAggregateCommand extends Command
{
    protected $signature = 'analytics:aggregate {--period=hourly} {--async}';

    protected $description = 'Aggregate time-series analytics statistics (hourly or daily)';

    public function handle(MetricsAggregatorInterface $aggregator): int
    {
        $period = (string) $this->option('period');
        $isAsync = (bool) $this->option('async');

        if ($period === 'daily') {
            if ($isAsync) {
                AggregateDailyStatisticsJob::dispatch();
                $this->info('Dispatched AggregateDailyStatisticsJob.');
                return Command::SUCCESS;
            }

            $aggregator->aggregateDaily();
            $this->info('Daily analytics aggregation completed.');
            return Command::SUCCESS;
        }

        if ($isAsync) {
            AggregateHourlyStatisticsJob::dispatch();
            $this->info('Dispatched AggregateHourlyStatisticsJob.');
            return Command::SUCCESS;
        }

        $aggregator->aggregateHourly();
        $this->info('Hourly analytics aggregation completed.');

        return Command::SUCCESS;
    }
}
