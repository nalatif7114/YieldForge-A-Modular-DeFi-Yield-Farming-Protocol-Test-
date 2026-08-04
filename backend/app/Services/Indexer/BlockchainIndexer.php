<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use App\Services\Indexer\Contracts\EventDispatcherInterface;
use App\Services\Indexer\Contracts\ReorgDetectorInterface;
use App\Services\Indexer\Contracts\SyncManagerInterface;
use App\Services\Indexer\DTO\IndexerContext;
use App\Services\Indexer\DTO\SyncResultDTO;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Log\LogManager;
use Throwable;

class BlockchainIndexer implements BlockchainIndexerInterface
{
    private IndexerContext $defaultContext;

    public function __construct(
        private readonly SyncManagerInterface $syncManager,
        private readonly LogProcessor $logProcessor,
        private readonly ReorgDetectorInterface $reorgDetector,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Contracts\ProjectionRegistryInterface $projectionRegistry,
        private readonly BlockCursor $blockCursor,
        private readonly IndexerMetricsService $metricsService,
        private readonly ConfigRepository $config,
        private readonly LogManager $log
    ) {
        $this->defaultContext = new IndexerContext(
            chainId: (int) $this->config->get('blockchain.chain_id', 11155111),
            network: (string) $this->config->get('blockchain.network_name', 'sepolia'),
            rpcEndpoint: (string) $this->config->get('blockchain.rpc_url')
        );
    }

    public function getContext(): IndexerContext
    {
        return $this->defaultContext;
    }

    public function syncLatest(?IndexerContext $context = null): SyncResultDTO
    {
        $context ??= $this->defaultContext;
        $range = $this->syncManager->determineSyncRange($context);

        if ($range['from'] <= 0 || $range['to'] < $range['from']) {
            return new SyncResultDTO(
                blocksProcessed: 0,
                eventsIndexed: 0,
                fromBlock: 0,
                toBlock: 0,
                hasReorg: false,
                durationMs: 0.0
            );
        }

        return $this->syncRange($range['from'], $range['to'], $context);
    }

    public function syncRange(int $fromBlock, int $toBlock, ?IndexerContext $context = null): SyncResultDTO
    {
        $context ??= $this->defaultContext;
        $startTime = microtime(true);
        $hasReorg = false;

        try {
            // Check for reorg at starting block
            $divergence = $this->reorgDetector->detectReorg($context, $fromBlock, null);
            if ($divergence !== null) {
                $hasReorg = true;
                $this->reorgDetector->rollbackTo($context, $divergence);
                $fromBlock = $divergence + 1;
            }

            $savedEvents = [];
            $eventsCount = 0;

            \Illuminate\Support\Facades\DB::transaction(function () use ($context, $fromBlock, $toBlock, &$savedEvents, &$eventsCount): void {
                // Fetch and save raw events
                $savedEvents = $this->logProcessor->process($context, $fromBlock, $toBlock);

                // Dispatch domain events to projections
                $this->eventDispatcher->dispatchBatch($savedEvents);

                // Update block cursor for processed range
                $eventsCount = count($savedEvents);
                for ($b = $fromBlock; $b <= $toBlock; $b++) {
                    $this->blockCursor->updateCursor($context, $b, null, null, $eventsCount);
                }

                // Update projection checkpoints for all registered projections
                foreach ($this->projectionRegistry->getProjections() as $projection) {
                    $this->blockCursor->updateCheckpoint(
                        projectionName: $projection->getProjectionName(),
                        blockNumber: $toBlock,
                        version: (string) $this->config->get('blockchain.projection_version', '1.0.0')
                    );
                }
            });

            $blocksProcessed = max(1, ($toBlock - $fromBlock) + 1);

            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $this->metricsService->recordSync($blocksProcessed, $eventsCount, $durationMs);

            $this->log->channel('indexer')->info("Synced blocks {$fromBlock}-{$toBlock} ({$eventsCount} events) in {$durationMs}ms");

            return new SyncResultDTO(
                blocksProcessed: $blocksProcessed,
                eventsIndexed: $eventsCount,
                fromBlock: $fromBlock,
                toBlock: $toBlock,
                hasReorg: $hasReorg,
                durationMs: $durationMs
            );
        } catch (Throwable $e) {
            $this->log->channel('indexer')->error("SyncRange error for {$fromBlock}-{$toBlock}: {$e->getMessage()}");

            return new SyncResultDTO(
                blocksProcessed: 0,
                eventsIndexed: 0,
                fromBlock: $fromBlock,
                toBlock: $toBlock,
                hasReorg: false,
                durationMs: round((microtime(true) - $startTime) * 1000, 2)
            );
        }
    }
}
