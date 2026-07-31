<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Enums;

enum ChainId: int
{
    case MAINNET = 1;
    case SEPOLIA = 11155111;
    case HARDHAT = 31337;

    public function name(): string
    {
        return match ($this) {
            self::MAINNET => 'Ethereum Mainnet',
            self::SEPOLIA => 'Sepolia Testnet',
            self::HARDHAT => 'Hardhat Local',
        };
    }
}
