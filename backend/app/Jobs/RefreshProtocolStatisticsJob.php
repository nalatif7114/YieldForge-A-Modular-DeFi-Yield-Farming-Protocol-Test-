<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Projection\ProtocolProjection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RefreshProtocolStatisticsJob implements ShouldQueue
{
    use Queueable;

    public function handle(ProtocolProjection $projection): void
    {
        $projection->refresh();
    }
}
