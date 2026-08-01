<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

use App\Services\Indexer\DomainEvents\AbstractDomainEvent;

interface ProjectionRegistryInterface
{
    public function register(ProjectionInterface $projection): void;

    /**
     * Dispatch domain event to all supporting registered projections.
     *
     * @param AbstractDomainEvent $event
     */
    public function dispatch(AbstractDomainEvent $event): void;

    /**
     * @return array<int, ProjectionInterface>
     */
    public function getProjections(): array;

    public function resetAll(): void;
}
