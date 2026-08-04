<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Support;

use App\Services\Blockchain\Exceptions\BlockchainException;

class EthereumCodec
{
    /**
     * Pre-calculated function selectors for YieldForge contracts.
     */
    private const SELECTORS = [
        'name()' => '0x06fdde03',
        'symbol()' => '0x95d89b41',
        'decimals()' => '0x313ce567',
        'totalSupply()' => '0x18160ddd',
        'balanceOf(address)' => '0x70a08231',
        'allowance(address,address)' => '0xdd62ed3e',
        'owner()' => '0x8da5cb5b',
        'paused()' => '0x5c975abb',
        'totalStaked()' => '0x04461019',
        'stakingToken()' => '0x7284e416',
    ];

    /**
     * Pre-calculated event topic hashes.
     */
    private const EVENT_TOPICS = [
        'Staked' => '0x9e71bc8eea02a63969f509818f2dafb9254532904319f9dbda79b67bd34a5f3d',
        'Withdrawn' => '0x7084f5476618d8e60b11ef0d7d3f06914655adb8793e28ff7f018d4c76d505d5',
        'Transfer' => '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef',
        'TokensMinted' => '0x3f2c9d57c068687834f0de942a9babb9e5acab57d516d3480a3c16ee165a4273',
        'TokensBurned' => '0xfd38818f5291bf0bb3a2a48aadc06ba8757865d1dabd804585338aab3009dcb6',
        'Paused' => '0x62e78cea01bee320cd4e420270b5ea74000d11b0c9f74754ebdbfc544b05a258',
        'Unpaused' => '0x5db9ee0a495bf2e6ff9c91a7834c1ba4fdd244a5e8aa4e537bd38aeae4b073aa',
    ];

    /**
     * Encode a function call into hex payload (`0x` + 4-byte selector + padded parameters).
     *
     * @param string $signature e.g. "balanceOf(address)"
     * @param array<int, mixed> $args
     * @return string
     */
    public function encodeCall(string $signature, array $args = []): string
    {
        $selector = self::SELECTORS[$signature] ?? null;

        if ($selector === null) {
            throw new BlockchainException("Unsupported function signature: {$signature}");
        }

        $payload = $selector;

        foreach ($args as $arg) {
            if (is_string($arg) && str_starts_with(strtolower($arg), '0x')) {
                // Address: pad left to 32 bytes (64 hex characters)
                $payload .= str_pad(strtolower(substr($arg, 2)), 64, '0', STR_PAD_LEFT);
            } elseif (is_numeric($arg)) {
                // Number / uint: convert to hex and pad left
                $hexVal = dechex((int) $arg);
                $payload .= str_pad($hexVal, 64, '0', STR_PAD_LEFT);
            }
        }

        return $payload;
    }

    /**
     * Decode a single uint256 / uint output.
     *
     * @param string $hex
     * @return string
     */
    public function decodeUint256(string $hex): string
    {
        $clean = ltrim(str_replace('0x', '', $hex), '0');

        if ($clean === '') {
            return '0';
        }

        if (function_exists('gmp_init')) {
            return gmp_strval(gmp_init($clean, 16));
        }

        if (function_exists('bcadd')) {
            $dec = '0';
            $len = strlen($clean);
            for ($i = 0; $i < $len; $i++) {
                $hexChar = $clean[$i];
                $val = hexdec($hexChar);
                $dec = bcadd(bcmul($dec, '16'), (string) $val);
            }
            return $dec;
        }

        return sprintf('%.0f', hexdec($clean));
    }

    /**
     * Decode an Ethereum address output (32-byte word to 20-byte hex address).
     *
     * @param string $hex
     * @return string
     */
    public function decodeAddress(string $hex): string
    {
        $clean = str_replace('0x', '', $hex);

        if (strlen($clean) < 40) {
            return '0x0000000000000000000000000000000000000000';
        }

        return '0x' . substr($clean, -40);
    }

    /**
     * Decode a boolean output.
     *
     * @param string $hex
     * @return bool
     */
    public function decodeBool(string $hex): bool
    {
        $clean = ltrim(str_replace('0x', '', $hex), '0');

        return $clean !== '' && $clean !== '0';
    }

    /**
     * Decode dynamic string output (e.g. token name or symbol).
     *
     * @param string $hex
     * @return string
     */
    public function decodeString(string $hex): string
    {
        $clean = str_replace('0x', '', $hex);

        if (strlen($clean) < 128) {
            return '';
        }

        // Offset 32-64 bytes contains length
        $lengthHex = substr($clean, 64, 64);
        $length = (int) hexdec($lengthHex);

        if ($length === 0) {
            return '';
        }

        // Value starts at byte 64 (128 hex chars)
        $stringHex = substr($clean, 128, $length * 2);

        return hex2bin($stringHex) ?: '';
    }

    /**
     * Get event topic hash by event name.
     *
     * @param string $eventName
     * @return string|null
     */
    public function getEventTopic(string $eventName): ?string
    {
        return self::EVENT_TOPICS[$eventName] ?? null;
    }

    /**
     * Reverse lookup event name from topic0 hash.
     *
     * @param string $topic0
     * @return string
     */
    public function resolveEventName(string $topic0): string
    {
        $topic0Lower = strtolower($topic0);

        foreach (self::EVENT_TOPICS as $name => $hash) {
            if (strtolower($hash) === $topic0Lower) {
                return $name;
            }
        }

        return 'UnknownEvent';
    }

    /**
     * Format raw uint256 value into human-readable string with decimals.
     *
     * @param string|int $rawAmount
     * @param int $decimals
     * @return string
     */
    public function formatUnits(string|int $rawAmount, int $decimals = 18): string
    {
        $rawStr = (string) $rawAmount;
        $dec = (int) $decimals;

        if ($rawStr === '0' || $rawStr === '' || $rawStr === '0.0') {
            return '0';
        }

        if (str_contains($rawStr, 'E') || str_contains($rawStr, 'e')) {
            $rawStr = sprintf('%.0f', (float) $rawStr);
        }

        $rawLen = strlen($rawStr);

        if ($rawLen <= $dec) {
            $padLength = (int) ($dec + 1);
            $padded = str_pad($rawStr, $padLength, '0', STR_PAD_LEFT);
            $integerPart = substr($padded, 0, strlen($padded) - $dec);
            $fractionPart = rtrim(substr($padded, -$dec), '0');

            return $fractionPart !== '' ? "{$integerPart}.{$fractionPart}" : $integerPart;
        }

        $integerPart = substr($rawStr, 0, $rawLen - $dec);
        $fractionPart = rtrim(substr($rawStr, -$dec), '0');

        return $fractionPart !== '' ? "{$integerPart}.{$fractionPart}" : $integerPart;
    }
}
