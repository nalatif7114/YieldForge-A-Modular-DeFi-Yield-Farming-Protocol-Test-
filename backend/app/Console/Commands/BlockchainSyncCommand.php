<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SyncHistoricalBlocksJob;
use App\Jobs\SyncLatestBlockJob;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Indexer\BlockCursor;
use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use App\Services\Indexer\Contracts\SyncManagerInterface;
use App\Services\Indexer\DTO\IndexerContext;
use Illuminate\Console\Command;

class BlockchainSyncCommand extends Command
{
    protected $signature = 'blockchain:sync {--from=} {--to=} {--async}';

    protected $description = 'Synchronize smart contract events from blockchain into indexer store';

    public function handle(
        BlockchainIndexerInterface $indexer,
        SyncManagerInterface $syncManager,
        NetworkServiceInterface $networkService,
        BlockCursor $blockCursor
    ): int {
        $fromOption = $this->option('from') !== null ? (int) $this->option('from') : null;
        $toOption = $this->option('to') !== null ? (int) $this->option('to') : null;
        $isAsync = (bool) $this->option('async');

        if ($isAsync) {
            if ($fromOption !== null && $toOption !== null) {
                SyncHistoricalBlocksJob::dispatch($fromOption, $toOption);
                $this->info("Dispatched SyncHistoricalBlocksJob for range {$fromOption}-{$toOption}.");
            } else {
                SyncLatestBlockJob::dispatch();
                $this->info('Dispatched SyncLatestBlockJob.');
            }
            return Command::SUCCESS;
        }

        $context = $indexer->getContext();
        $networkInfo = $networkService->getNetworkInfo();
        $latestRpcBlock = $networkInfo->blockNumber;
        $cursorBlock = $blockCursor->getLatestIndexedBlock($context);

        if ($fromOption !== null && $toOption !== null) {
            $from = $fromOption;
            $to = $toOption;
        } else {
            $range = $syncManager->determineSyncRange($context);
            $from = $range['from'];
            $to = $range['to'];
        }

        $tokenAddress = (string) config('blockchain.contracts.token.address');
        $stakingAddress = (string) config('blockchain.contracts.staking.address');
        $addresses = array_values(array_filter([$tokenAddress, $stakingAddress]));

        $this->info("--- YieldForge Indexer Diagnostics ---");
        $this->line("Latest RPC block: {$latestRpcBlock}");
        $this->line("Cursor block: {$cursorBlock}");
        $this->line("From block: {$from}");
        $this->line("To block: {$to}");
        $this->line("Contract addresses: " . implode(', ', $addresses));
        $this->line("Topics: All registered event topics (Staked, Withdrawn, Transfer, TokensMinted, TokensBurned, Paused, Unpaused)");

        if ($from <= 0 || $to < $from) {
            $this->warn("Sync skipped: No new blocks to sync (Cursor {$cursorBlock} >= Latest RPC block {$latestRpcBlock}).");
            $this->line("Logs returned: 0");
            $this->line("Logs inserted: 0");
            $this->line("Logs skipped: 0");
            $this->line("Reason skipped: Indexed latest block is up to date with on-chain height.");
            return Command::SUCCESS;
        }

        $this->info("Syncing range {$from}-{$to}...");
        $result = $indexer->syncRange($from, $to, $context);

        $this->info("Sync completed: {$result->blocksProcessed} blocks, {$result->eventsIndexed} events in {$result->durationMs}ms.");
        $this->line("Logs returned: {$result->eventsIndexed}");
        $this->line("Logs inserted: {$result->eventsIndexed}");
        $this->line("Logs skipped: 0");

        return Command::SUCCESS;
    }
}
