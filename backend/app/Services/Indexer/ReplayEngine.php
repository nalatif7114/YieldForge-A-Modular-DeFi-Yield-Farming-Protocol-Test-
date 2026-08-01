<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\BlockchainEvent;
use App\Services\Indexer\Contracts\EventDispatcherInterface;
use App\Services\Indexer\Contracts\ProjectionRegistryInterface;
use App\Services\Indexer\Contracts\ReplayEngineInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Log\LogManager;

class ReplayEngine implements ReplayEngineInterface
{
    public function __construct(
        private readonly ProjectionRegistryInterface $projectionRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly BlockCursor $blockCursor,
        private readonly ConfigRepository $config,
        private readonly LogManager $log
    ) {}

    public function replay(int $fromBlock = 0): int
    {
        $startTime = microtime(true);
        $this->log->channel('indexer')->info("Starting event replay from block [{$fromBlock}]...");

        if ($fromBlock === 0) {
            $this->projectionRegistry->resetAll();
        }

        $batchSize = (int) $this->config->get('blockchain.replay_batch_size', 500);
        $replayedCount = 0;
        $latestProcessedBlock = $fromBlock;

        BlockchainEvent::query()
            ->where('block_number', '>=', $fromBlock)
            ->where('removed', false)
            ->orderBy('block_number', 'asc')
            ->orderBy('log_index', 'asc')
            ->chunk($batchSize, function ($events) use (&$replayedCount, &$latestProcessedBlock): void {
                /** @var BlockchainEvent $event */
                foreach ($events as $event) {
                    $this->eventDispatcher->dispatch($event);
                    $replayedCount++;
                    $latestProcessedBlock = max($latestProcessedBlock, (int) $event->block_number);
                }
            });

        // Update checkpoints for all registered projections
        foreach ($this->projectionRegistry->getProjections() as $projection) {
            $this->blockCursor->updateCheckpoint(
                projectionName: $projection->getProjectionName(),
                blockNumber: $latestProcessedBlock,
                version: (string) $this->config->get('blockchain.projection_version', '1.0.0')
            );
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);
        $this->log->channel('indexer')->info("Event replay finished: {$replayedCount} events replayed in {$durationMs}ms");

        return $replayedCount;
    }
}
