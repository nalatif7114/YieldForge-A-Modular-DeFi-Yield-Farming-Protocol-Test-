<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

interface ReplayEngineInterface
{
    /**
     * Rebuild projections from stored BlockchainEvent models starting from given block.
     *
     * @param int $fromBlock
     * @return int Number of events replayed
     */
    public function replay(int $fromBlock = 0): int;
}
