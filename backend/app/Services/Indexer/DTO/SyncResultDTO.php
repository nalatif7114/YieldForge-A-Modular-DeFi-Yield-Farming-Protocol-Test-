<?php

declare(strict_types=1);

namespace App\Services\Indexer\DTO;

readonly class SyncResultDTO
{
    public function __construct(
        public int $blocksProcessed,
        public int $eventsIndexed,
        public int $fromBlock,
        public int $toBlock,
        public bool $hasReorg,
        public float $durationMs
    ) {}

    public function toArray(): array
    {
        return [
            'blocks_processed' => $this->blocksProcessed,
            'events_indexed' => $this->eventsIndexed,
            'from_block' => $this->fromBlock,
            'to_block' => $this->toBlock,
            'has_reorg' => $this->hasReorg,
            'duration_ms' => $this->durationMs,
        ];
    }
}
