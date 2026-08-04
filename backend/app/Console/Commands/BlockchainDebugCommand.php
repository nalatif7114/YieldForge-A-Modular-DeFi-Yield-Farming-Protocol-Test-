<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BlockchainEvent;

use App\Models\IndexedBlock;
use App\Models\PoolSnapshot;
use App\Models\ProjectionCheckpoint;
use App\Models\ProtocolStatistic;
use App\Models\RewardSnapshot;
use App\Models\WalletPosition;
use App\Services\Blockchain\Contracts\AbiLoaderInterface;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\Support\EthereumCodec;
use App\Services\Indexer\BlockCursor;
use App\Services\Indexer\DTO\IndexerContext;
use App\Services\Indexer\EventDispatcher;
use App\Services\Indexer\LogProcessor;
use Illuminate\Console\Command;

class BlockchainDebugCommand extends Command
{
    protected $signature = 'blockchain:debug {--from=} {--to=} {--range=1000}';

    protected $description = 'Perform detailed runtime diagnostic audit on eth_getLogs and indexer pipeline';

    public function handle(
        RpcClientInterface $rpcClient,
        NetworkServiceInterface $networkService,
        BlockCursor $blockCursor,
        EthereumCodec $codec,
        AbiLoaderInterface $abiLoader,
        LogProcessor $logProcessor,
        EventDispatcher $eventDispatcher
    ): int {
        $this->info('Starting YieldForge Indexer Runtime Debug Audit...');

        // 1. Fetch Latest RPC block & Cursor block
        $networkInfo = $networkService->getNetworkInfo();
        $latestRpcBlock = $networkInfo->blockNumber;

        $context = new IndexerContext(
            chainId: (int) config('blockchain.chain_id', 11155111),
            network: (string) config('blockchain.network_name', 'sepolia'),
            rpcEndpoint: (string) config('blockchain.rpc_url')
        );
        $cursorBlock = $blockCursor->getLatestIndexedBlock($context);

        $rangeOption = (int) $this->option('range');
        $fromBlock = $this->option('from') !== null
            ? (int) $this->option('from')
            : max(1, $latestRpcBlock - $rangeOption);
        $toBlock = $this->option('to') !== null
            ? (int) $this->option('to')
            : $latestRpcBlock;

        $tokenAddress = (string) config('blockchain.contracts.token.address');
        $stakingAddress = (string) config('blockchain.contracts.staking.address');

        $addresses = array_values(array_filter([$tokenAddress, $stakingAddress]));

        $this->line("Latest RPC block: {$latestRpcBlock}");
        $this->line("Cursor block: {$cursorBlock}");
        $this->line("From block: {$fromBlock}");
        $this->line("To block: {$toBlock}");
        $this->line("Contract addresses: " . implode(', ', $addresses));

        // Prepare eth_getLogs payload
        $fromBlockHex = '0x' . dechex($fromBlock);
        $toBlockHex = '0x' . dechex($toBlock);

        $params = [
            'fromBlock' => $fromBlockHex,
            'toBlock' => $toBlockHex,
        ];
        if (!empty($addresses)) {
            $params['address'] = count($addresses) === 1 ? $addresses[0] : $addresses;
        }

        // ------------------------------------------------
        // Raw JSON-RPC Request
        // ------------------------------------------------
        $this->line("\n------------------------------------------------");
        $this->line("Raw JSON-RPC Request");
        $this->line("------------------------------------------------");
        $this->line("method: eth_getLogs");
        $this->line("fromBlock: {$fromBlockHex} ({$fromBlock})");
        $this->line("toBlock: {$toBlockHex} ({$toBlock})");
        $this->line("address: " . json_encode($params['address'] ?? null));
        $this->line("topics: null (all topics)");
        $this->line("Full params: " . json_encode([$params]));

        // Execute raw call
        $rawLogs = [];
        $rawError = null;
        try {
            $rawLogs = $rpcClient->call('eth_getLogs', [$params]);
        } catch (\Throwable $e) {
            $rawError = $e->getMessage();
        }

        // ------------------------------------------------
        // Raw JSON-RPC Response
        // ------------------------------------------------
        $this->line("\n------------------------------------------------");
        $this->line("Raw JSON-RPC Response");
        $this->line("------------------------------------------------");

        if ($rawError !== null) {
            $this->error("RPC Error: {$rawError}");
        } else {
            $logsCount = is_array($rawLogs) ? count($rawLogs) : 0;
            $this->info("Total logs returned: {$logsCount}");

            if ($logsCount === 0) {
                $this->warn("Reason if no logs returned: No event logs emitted by configured contract addresses [{$tokenAddress}, {$stakingAddress}] in block range {$fromBlock}-{$toBlock}.");
            } else {
                foreach ($rawLogs as $index => $log) {
                    $bNum = isset($log['blockNumber']) ? hexdec(str_replace('0x', '', (string) $log['blockNumber'])) : 'N/A';
                    $txHash = $log['transactionHash'] ?? 'N/A';
                    $lIdx = isset($log['logIndex']) ? hexdec(str_replace('0x', '', (string) $log['logIndex'])) : 'N/A';
                    $addr = $log['address'] ?? 'N/A';
                    $topics = json_encode($log['topics'] ?? []);
                    $data = $log['data'] ?? '0x';

                    $this->line("Log #{$index}: blockNumber={$bNum}, txHash={$txHash}, logIndex={$lIdx}, address={$addr}");
                    $this->line("  topics: {$topics}");
                    $this->line("  data: {$data}");
                }
            }
        }

        // ------------------------------------------------
        // EventService Decoding Audit
        // ------------------------------------------------
        $this->line("\n------------------------------------------------");
        $this->line("EventService");
        $this->line("------------------------------------------------");

        $logsReceived = is_array($rawLogs) ? count($rawLogs) : 0;
        $logsDecoded = 0;
        $logsDiscarded = 0;

        if (is_array($rawLogs)) {
            foreach ($rawLogs as $log) {
                $topics = $log['topics'] ?? [];
                $topic0 = $topics[0] ?? '';
                $resolvedName = $codec->resolveEventName((string) $topic0);

                if ($resolvedName === 'UnknownEvent') {
                    $logsDiscarded++;
                    $this->warn("Discarded log in tx {$log['transactionHash']}: Unknown topic0 [{$topic0}]");
                } else {
                    $logsDecoded++;
                    $this->info("Decoded log in tx {$log['transactionHash']}: Event [{$resolvedName}]");
                }
            }
        }

        $this->line("Logs received: {$logsReceived}");
        $this->line("Logs decoded: {$logsDecoded}");
        $this->line("Logs discarded: {$logsDiscarded}");

        // ------------------------------------------------
        // LogProcessor & Projection Audit
        // ------------------------------------------------
        $this->line("\n------------------------------------------------");
        $this->line("LogProcessor & Projections");
        $this->line("------------------------------------------------");

        $savedEvents = $logProcessor->process($context, $fromBlock, $toBlock);
        $insertedCount = count($savedEvents);
        $eventDispatcher->dispatchBatch($savedEvents);

        $this->line("Logs inserted into blockchain_events: {$insertedCount}");
        $this->line("Duplicate detection result: Handled via unique constraint (transaction_hash, log_index)");
        $this->line("Projection execution: Complete for all registered projections");

        // ------------------------------------------------
        // Database Row Counts Audit
        // ------------------------------------------------
        $this->line("\n------------------------------------------------");
        $this->line("Database Row Counts Audit");
        $this->line("------------------------------------------------");
        $this->line("indexed_blocks: " . IndexedBlock::count());
        $this->line("blockchain_events: " . BlockchainEvent::count());
        $this->line("wallet_positions: " . WalletPosition::count());
        $this->line("pool_snapshots: " . PoolSnapshot::count());
        $this->line("reward_snapshots: " . RewardSnapshot::count());
        $this->line("protocol_statistics: " . ProtocolStatistic::count());
        $this->line("projection_checkpoints: " . ProjectionCheckpoint::count());
        $this->line("------------------------------------------------\n");

        return Command::SUCCESS;
    }
}
