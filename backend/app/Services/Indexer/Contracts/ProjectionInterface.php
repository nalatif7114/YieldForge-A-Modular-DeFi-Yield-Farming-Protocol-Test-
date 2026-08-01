<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

use App\Services\Indexer\DomainEvents\AbstractDomainEvent;

interface ProjectionInterface
{
    public function getProjectionName(): string;

    public function supports(AbstractDomainEvent $event): bool;

    public function handle(AbstractDomainEvent $event): void;

    public function reset(): void;
}
