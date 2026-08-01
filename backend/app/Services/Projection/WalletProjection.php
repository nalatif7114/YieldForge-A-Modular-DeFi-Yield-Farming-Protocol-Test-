<?php

declare(strict_types=1);

namespace App\Services\Projection;

use App\Models\TransactionHistory;
use App\Models\WalletPosition;
use App\Services\Blockchain\Support\EthereumCodec;
use App\Services\Indexer\Contracts\ProjectionInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;
use App\Services\Indexer\DomainEvents\StakedDomainEvent;
use App\Services\Indexer\DomainEvents\TransferDomainEvent;
use App\Services\Indexer\DomainEvents\WithdrawnDomainEvent;

class WalletProjection implements ProjectionInterface
{
    public function __construct(
        private readonly EthereumCodec $codec
    ) {}

    public function getProjectionName(): string
    {
        return 'WalletProjection';
    }

    public function supports(AbstractDomainEvent $event): bool
    {
        return $event instanceof StakedDomainEvent
            || $event instanceof WithdrawnDomainEvent
            || $event instanceof TransferDomainEvent;
    }

    public function handle(AbstractDomainEvent $event): void
    {
        if ($event instanceof StakedDomainEvent) {
            $this->handleStaked($event);
        } elseif ($event instanceof WithdrawnDomainEvent) {
            $this->handleWithdrawn($event);
        } elseif ($event instanceof TransferDomainEvent) {
            $this->handleTransfer($event);
        }
    }

    private function handleStaked(StakedDomainEvent $event): void
    {
        $wallet = strtolower($event->user);
        if ($wallet === '' || $wallet === '0x0000000000000000000000000000000000000000') {
            return;
        }

        /** @var WalletPosition $position */
        $position = WalletPosition::firstOrCreate(
            ['wallet' => $wallet],
            [
                'staked_balance_raw' => '0',
                'staked_balance_formatted' => '0',
                'token_balance_raw' => '0',
                'token_balance_formatted' => '0',
                'pool_share_percentage' => 0.0,
                'last_updated_block' => $event->blockNumber,
            ]
        );

        $currentRaw = $position->staked_balance_raw;
        $addRaw = $event->amountRaw;

        $newRaw = (string) bcadd($currentRaw, $addRaw);
        $newFormatted = $this->codec->formatUnits($newRaw, 18);

        $position->update([
            'staked_balance_raw' => $newRaw,
            'staked_balance_formatted' => $newFormatted,
            'last_updated_block' => max($position->last_updated_block, $event->blockNumber),
        ]);

        TransactionHistory::firstOrCreate(
            [
                'transaction_hash' => $event->transactionHash,
                'wallet' => $wallet,
                'event_name' => 'Staked',
            ],
            [
                'amount_raw' => $event->amountRaw,
                'amount_formatted' => $event->amountFormatted,
                'block_number' => $event->blockNumber,
                'timestamp' => $event->timestamp ?? now(),
            ]
        );
    }

    private function handleWithdrawn(WithdrawnDomainEvent $event): void
    {
        $wallet = strtolower($event->user);
        if ($wallet === '' || $wallet === '0x0000000000000000000000000000000000000000') {
            return;
        }

        /** @var WalletPosition|null $position */
        $position = WalletPosition::where('wallet', $wallet)->first();
        if (!$position) {
            return;
        }

        $currentRaw = $position->staked_balance_raw;
        $subRaw = $event->amountRaw;

        $newRaw = (string) bcsub($currentRaw, $subRaw);
        if (bccomp($newRaw, '0') < 0) {
            $newRaw = '0';
        }

        $newFormatted = $this->codec->formatUnits($newRaw, 18);

        $position->update([
            'staked_balance_raw' => $newRaw,
            'staked_balance_formatted' => $newFormatted,
            'last_updated_block' => max($position->last_updated_block, $event->blockNumber),
        ]);

        TransactionHistory::firstOrCreate(
            [
                'transaction_hash' => $event->transactionHash,
                'wallet' => $wallet,
                'event_name' => 'Withdrawn',
            ],
            [
                'amount_raw' => $event->amountRaw,
                'amount_formatted' => $event->amountFormatted,
                'block_number' => $event->blockNumber,
                'timestamp' => $event->timestamp ?? now(),
            ]
        );
    }

    private function handleTransfer(TransferDomainEvent $event): void
    {
        $from = strtolower($event->from);
        $to = strtolower($event->to);

        if ($from !== '' && $from !== '0x0000000000000000000000000000000000000000') {
            /** @var WalletPosition $posFrom */
            $posFrom = WalletPosition::firstOrCreate(['wallet' => $from]);
            $newRaw = (string) bcsub($posFrom->token_balance_raw, $event->valueRaw);
            if (bccomp($newRaw, '0') < 0) {
                $newRaw = '0';
            }
            $posFrom->update([
                'token_balance_raw' => $newRaw,
                'token_balance_formatted' => $this->codec->formatUnits($newRaw, 18),
                'last_updated_block' => max($posFrom->last_updated_block, $event->blockNumber),
            ]);
        }

        if ($to !== '' && $to !== '0x0000000000000000000000000000000000000000') {
            /** @var WalletPosition $posTo */
            $posTo = WalletPosition::firstOrCreate(['wallet' => $to]);
            $newRaw = (string) bcadd($posTo->token_balance_raw, $event->valueRaw);
            $posTo->update([
                'token_balance_raw' => $newRaw,
                'token_balance_formatted' => $this->codec->formatUnits($newRaw, 18),
                'last_updated_block' => max($posTo->last_updated_block, $event->blockNumber),
            ]);
        }
    }

    public function reset(): void
    {
        WalletPosition::query()->truncate();
        TransactionHistory::query()->truncate();
    }
}
