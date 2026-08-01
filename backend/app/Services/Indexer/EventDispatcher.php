<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\BlockchainEvent;
use App\Services\Indexer\Contracts\EventDispatcherInterface;
use App\Services\Indexer\Contracts\ProjectionRegistryInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;
use App\Services\Indexer\DomainEvents\DomainEventFactory;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly ProjectionRegistryInterface $registry,
        private readonly DomainEventFactory $factory
    ) {}

    public function dispatch(BlockchainEvent|AbstractDomainEvent $event): void
    {
        $domainEvent = $event instanceof AbstractDomainEvent
            ? $event
            : $this->factory->fromModel($event);

        $this->registry->dispatch($domainEvent);
    }

    public function dispatchBatch(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
