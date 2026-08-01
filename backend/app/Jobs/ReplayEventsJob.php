<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Indexer\Contracts\ReplayEngineInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReplayEventsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $fromBlock = 0
    ) {}

    public function handle(ReplayEngineInterface $replayEngine): void
    {
        $replayEngine->replay($this->fromBlock);
    }
}
