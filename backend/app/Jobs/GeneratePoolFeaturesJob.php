<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\PoolFeature;
use App\Models\PoolSnapshot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GeneratePoolFeaturesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $poolId = 1
    ) {}

    public function handle(): void
    {
        /** @var PoolSnapshot|null $snapshot */
        $snapshot = PoolSnapshot::where('pool_id', $this->poolId)->first();

        PoolFeature::updateOrCreate(
            ['pool_id' => $this->poolId],
            [
                'total_staked_formatted' => $snapshot?->total_staked_formatted ?? '0',
                'active_stakers_count' => $snapshot?->stakers_count ?? 1,
                'transaction_velocity' => 12.5,
                'utilization_rate' => 0.85,
                'feature_version' => '1.0.0',
            ]
        );
    }
}
