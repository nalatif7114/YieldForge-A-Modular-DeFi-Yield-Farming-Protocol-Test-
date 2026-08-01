<?php

declare(strict_types=1);

namespace App\Services\Projection;

use App\Models\RewardSnapshot;
use App\Services\Blockchain\Support\EthereumCodec;
use App\Services\Indexer\Contracts\ProjectionInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;
use App\Services\Indexer\DomainEvents\TokensBurnedDomainEvent;
use App\Services\Indexer\DomainEvents\TokensMintedDomainEvent;
use App\Services\Indexer\DomainEvents\TransferDomainEvent;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class RewardProjection implements ProjectionInterface
{
    public function __construct(
        private readonly EthereumCodec $codec,
        private readonly ConfigRepository $config
    ) {}

    public function getProjectionName(): string
    {
        return 'RewardProjection';
    }

    public function supports(AbstractDomainEvent $event): bool
    {
        return $event instanceof TransferDomainEvent
            || $event instanceof TokensMintedDomainEvent
            || $event instanceof TokensBurnedDomainEvent;
    }

    public function handle(AbstractDomainEvent $event): void
    {
        $tokenAddress = (string) $this->config->get('blockchain.contracts.token.address');

        if ($event instanceof TransferDomainEvent) {
            $to = strtolower($event->to);
            if ($to !== '' && $to !== '0x0000000000000000000000000000000000000000') {
                $snapshot = RewardSnapshot::firstOrCreate(
                    ['wallet' => $to, 'token_address' => $tokenAddress],
                    [
                        'balance_raw' => '0',
                        'balance_formatted' => '0',
                        'pending_rewards_raw' => '0',
                        'pending_rewards_formatted' => '0',
                        'block_number' => $event->blockNumber,
                    ]
                );

                $newRaw = (string) bcadd($snapshot->balance_raw, $event->valueRaw);
                $snapshot->update([
                    'balance_raw' => $newRaw,
                    'balance_formatted' => $this->codec->formatUnits($newRaw, 18),
                    'block_number' => max($snapshot->block_number, $event->blockNumber),
                ]);
            }
        } elseif ($event instanceof TokensMintedDomainEvent) {
            $to = strtolower($event->to);
            if ($to !== '' && $to !== '0x0000000000000000000000000000000000000000') {
                $snapshot = RewardSnapshot::firstOrCreate(
                    ['wallet' => $to, 'token_address' => $tokenAddress],
                    [
                        'balance_raw' => '0',
                        'balance_formatted' => '0',
                        'pending_rewards_raw' => '0',
                        'pending_rewards_formatted' => '0',
                        'block_number' => $event->blockNumber,
                    ]
                );

                $newRaw = (string) bcadd($snapshot->balance_raw, $event->amountRaw);
                $snapshot->update([
                    'balance_raw' => $newRaw,
                    'balance_formatted' => $this->codec->formatUnits($newRaw, 18),
                    'block_number' => max($snapshot->block_number, $event->blockNumber),
                ]);
            }
        } elseif ($event instanceof TokensBurnedDomainEvent) {
            $from = strtolower($event->from);
            if ($from !== '' && $from !== '0x0000000000000000000000000000000000000000') {
                $snapshot = RewardSnapshot::where('wallet', $from)->first();
                if ($snapshot) {
                    $newRaw = (string) bcsub($snapshot->balance_raw, $event->amountRaw);
                    if (bccomp($newRaw, '0') < 0) {
                        $newRaw = '0';
                    }
                    $snapshot->update([
                        'balance_raw' => $newRaw,
                        'balance_formatted' => $this->codec->formatUnits($newRaw, 18),
                        'block_number' => max($snapshot->block_number, $event->blockNumber),
                    ]);
                }
            }
        }
    }

    public function reset(): void
    {
        RewardSnapshot::query()->truncate();
    }
}
