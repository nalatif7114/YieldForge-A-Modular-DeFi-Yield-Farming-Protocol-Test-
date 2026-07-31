<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Enums;

enum ContractType: string
{
    case ERC20 = 'erc20';
    case STAKING = 'staking';
}
