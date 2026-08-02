<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Services\Analytics\SnapshotBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnapshotBuilderTest extends TestCase
{
    use RefreshDatabase;

    private SnapshotBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->app->make(SnapshotBuilder::class);
    }

    public function test_builds_immutable_analytics_snapshot(): void
    {
        $snapshot = $this->builder->buildSnapshot();

        $this->assertDatabaseHas('analytics_snapshots', [
            'id' => $snapshot->id,
            'snapshot_type' => '5m',
        ]);
        $this->assertDatabaseHas('protocol_analytics', [
            'active_pools_count' => 1,
        ]);
    }
}
