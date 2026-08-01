<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Services\Indexer\Contracts\ProjectionInterface;
use App\Services\Indexer\Contracts\ProjectionRegistryInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;
use Illuminate\Log\LogManager;
use Throwable;

class ProjectionRegistry implements ProjectionRegistryInterface
{
    /**
     * @var array<int, ProjectionInterface>
     */
    private array $projections = [];

    public function __construct(
        private readonly LogManager $log
    ) {}

    public function register(ProjectionInterface $projection): void
    {
        $this->projections[] = $projection;
    }

    public function dispatch(AbstractDomainEvent $event): void
    {
        foreach ($this->projections as $projection) {
            if ($projection->supports($event)) {
                try {
                    $projection->handle($event);
                } catch (Throwable $e) {
                    $this->log->channel('indexer')->error(
                        "Projection [{$projection->getProjectionName()}] failed for event [{$event->eventName}]: {$e->getMessage()}",
                        [
                            'event' => $event->toArray(),
                            'exception' => $e->getMessage(),
                        ]
                    );
                }
            }
        }
    }

    public function getProjections(): array
    {
        return $this->projections;
    }

    public function resetAll(): void
    {
        foreach ($this->projections as $projection) {
            $projection->reset();
        }
    }
}
