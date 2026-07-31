<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Services\Blockchain\Contracts\AbiLoaderInterface;
use App\Services\Blockchain\Exceptions\BlockchainException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class AbiLoader implements AbiLoaderInterface
{
    /**
     * Cache loaded ABIs in memory.
     *
     * @var array<string, array<int, array<string, mixed>>>
     */
    private array $loadedAbis = [];

    public function __construct(
        private readonly ConfigRepository $config
    ) {}

    /**
     * Get loaded ABI array for a given contract name.
     *
     * @param string $contractName
     * @return array<int, array<string, mixed>>
     * @throws BlockchainException
     */
    public function getAbi(string $contractName): array
    {
        if (isset($this->loadedAbis[$contractName])) {
            return $this->loadedAbis[$contractName];
        }

        $abiPath = (string) $this->config->get('blockchain.abi_path');
        $filePath = "{$abiPath}/{$contractName}.json";

        if (!file_exists($filePath)) {
            throw new BlockchainException("ABI file not found for contract [{$contractName}] at path: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new BlockchainException("Failed to read ABI file for [{$contractName}]");
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new BlockchainException("Invalid JSON content in ABI file for [{$contractName}]");
        }

        // Handle case where ABI is inside { "abi": [...] } wrapper object
        if (isset($data['abi']) && is_array($data['abi'])) {
            $data = $data['abi'];
        }

        $this->loadedAbis[$contractName] = $data;

        return $data;
    }

    /**
     * Get ABI entry for a specific function name.
     *
     * @param string $contractName
     * @param string $functionName
     * @return array<string, mixed>|null
     */
    public function getFunctionAbi(string $contractName, string $functionName): ?array
    {
        $abi = $this->getAbi($contractName);

        foreach ($abi as $item) {
            if (
                isset($item['type'], $item['name']) &&
                $item['type'] === 'function' &&
                $item['name'] === $functionName
            ) {
                return $item;
            }
        }

        return null;
    }
}
