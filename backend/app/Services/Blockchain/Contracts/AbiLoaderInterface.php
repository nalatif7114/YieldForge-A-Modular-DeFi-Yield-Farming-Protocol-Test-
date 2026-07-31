<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Contracts;

interface AbiLoaderInterface
{
    /**
     * Get loaded ABI array for a given contract name.
     *
     * @param string $contractName
     * @return array<int, array<string, mixed>>
     */
    public function getAbi(string $contractName): array;

    /**
     * Get ABI entry for a specific function name.
     *
     * @param string $contractName
     * @param string $functionName
     * @return array<string, mixed>|null
     */
    public function getFunctionAbi(string $contractName, string $functionName): ?array;
}
