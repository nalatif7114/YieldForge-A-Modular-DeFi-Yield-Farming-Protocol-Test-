<?php

declare(strict_types=1);

namespace App\Services\Projection;

use App\Models\IndexedBlock;
use App\Services\Indexer\Contracts\ProjectionInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;

class BlockProjection implements ProjectionInterface
{
    public function getProjectionName(): string
    {
        return 'BlockProjection';
    }

    public function supports(AbstractDomainEvent $event): bool
    {
        return true; // Supports tracking all events into block stats
    }

    public function handle(AbstractDomainEvent $event): void
    {
        IndexedBlock::updateOrCreate(
            ['block_number' => $event->blockNumber],
            [
                'events_count' => \Illuminate\Database\Eloquent\Casts\Attribute::make(), // Handled via increment
                'timestamp' => $event->timestamp ?? now(),
                'status' => 'processed',
            ]
        );
    }

    public function reset(): void
    {
        IndexedBlock::query()->truncate();
    }
}
