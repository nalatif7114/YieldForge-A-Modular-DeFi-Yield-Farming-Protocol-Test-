<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

use App\Models\BlockchainEvent;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;

interface EventDispatcherInterface
{
    public function dispatch(BlockchainEvent|AbstractDomainEvent $event): void;

    /**
     * @param array<int, BlockchainEvent|AbstractDomainEvent> $events
     */
    public function dispatchBatch(array $events): void;
}
