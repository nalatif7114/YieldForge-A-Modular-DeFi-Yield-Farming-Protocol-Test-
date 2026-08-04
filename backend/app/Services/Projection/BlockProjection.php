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
        /** @var IndexedBlock $block */
        $block = IndexedBlock::firstOrCreate(
            ['block_number' => $event->blockNumber],
            [
                'chain_id' => (int) config('blockchain.chain_id', 11155111),
                'network' => (string) config('blockchain.network_name', 'sepolia'),
                'events_count' => 0,
                'status' => 'processed',
                'timestamp' => $event->timestamp ?? now(),
            ]
        );

        $block->increment('events_count');
    }

    public function reset(): void
    {
        IndexedBlock::query()->truncate();
    }
}
