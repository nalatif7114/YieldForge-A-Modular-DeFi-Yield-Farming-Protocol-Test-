<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Indexer\Contracts\SyncManagerInterface;
use App\Services\Indexer\DTO\IndexerContext;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class SyncManager implements SyncManagerInterface
{
    public function __construct(
        private readonly NetworkServiceInterface $networkService,
        private readonly BlockCursor $blockCursor,
        private readonly ConfigRepository $config
    ) {}

    public function determineSyncRange(IndexerContext $context): array
    {
        try {
            $networkInfo = $this->networkService->getNetworkInfo();
            $onChainLatest = $networkInfo->blockNumber;

            if ($onChainLatest <= 0) {
                return ['from' => 0, 'to' => 0];
            }

            $indexedLatest = $this->blockCursor->getLatestIndexedBlock($context);
            $batchSize = (int) $this->config->get('blockchain.sync_batch_size', 100);

            if ($indexedLatest <= 0) {
                $from = max(1, $onChainLatest - $batchSize);
                $to = $onChainLatest;
                return ['from' => $from, 'to' => $to];
            }

            if ($indexedLatest >= $onChainLatest) {
                return ['from' => $indexedLatest, 'to' => $indexedLatest];
            }

            $from = $indexedLatest + 1;
            $to = min($onChainLatest, $from + $batchSize - 1);

            return ['from' => $from, 'to' => $to];
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error("SyncManager determineSyncRange error: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
            return ['from' => 0, 'to' => 0];
        }
    }

    public function isSyncNeeded(IndexerContext $context): bool
    {
        $range = $this->determineSyncRange($context);

        return $range['to'] > $range['from'];
    }
}
