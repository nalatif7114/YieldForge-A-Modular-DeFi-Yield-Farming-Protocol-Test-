<?php

declare(strict_types=1);

namespace App\Services\Indexer\DTO;

readonly class IndexerStateDTO
{
    public function __construct(
        public string $network,
        public int $latestBlock,
        public int $indexedBlock,
        public int $lag,
        public int $events,
        public string $status,
        public string $lastSync,
        public string $projectionVersion = '1.0.0'
    ) {}

    public function toArray(): array
    {
        return [
            'network' => $this->network,
            'latest_block' => $this->latestBlock,
            'indexed_block' => $this->indexedBlock,
            'lag' => $this->lag,
            'events' => $this->events,
            'status' => $this->status,
            'last_sync' => $this->lastSync,
            'projection_version' => $this->projectionVersion,
        ];
    }
}
