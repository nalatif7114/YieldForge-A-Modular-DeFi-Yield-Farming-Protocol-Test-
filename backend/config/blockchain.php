<?php

declare(strict_types=1);

return [
    'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'https://ethereum-sepolia-rpc.publicnode.com'),
    'chain_id' => (int) env('BLOCKCHAIN_CHAIN_ID', 11155111),
    'network_name' => env('BLOCKCHAIN_NETWORK_NAME', 'sepolia'),
    'timeout' => (int) env('BLOCKCHAIN_RPC_TIMEOUT', 10),
    'retries' => (int) env('BLOCKCHAIN_RPC_RETRIES', 3),
    'retry_delay_ms' => (int) env('BLOCKCHAIN_RPC_RETRY_DELAY_MS', 100),
    'abi_path' => storage_path('blockchain/abis'),

    'sync_batch_size' => (int) env('BLOCKCHAIN_SYNC_BATCH_SIZE', 100),
    'confirmations' => (int) env('BLOCKCHAIN_CONFIRMATIONS', 1),
    'poll_interval' => (int) env('BLOCKCHAIN_POLL_INTERVAL', 10),
    'replay_batch_size' => (int) env('BLOCKCHAIN_REPLAY_BATCH_SIZE', 500),
    'projection_version' => env('BLOCKCHAIN_PROJECTION_VERSION', '1.0.0'),
    'indexer_timeout' => (int) env('BLOCKCHAIN_INDEXER_TIMEOUT', 30),

    'analytics_snapshot_interval' => (int) env('ANALYTICS_SNAPSHOT_INTERVAL', 300),
    'history_retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 365),
    'aggregation_interval' => (int) env('ANALYTICS_AGGREGATION_INTERVAL', 3600),
    'analytics_version' => env('ANALYTICS_VERSION', '1.0.0'),
    'analytics_cache_duration' => (int) env('ANALYTICS_CACHE_TTL', 300),

    'cache_ttl' => [
        'network' => (int) env('BLOCKCHAIN_CACHE_TTL_NETWORK', 15),
        'pools' => (int) env('BLOCKCHAIN_CACHE_TTL_POOLS', 30),
        'contracts' => (int) env('BLOCKCHAIN_CACHE_TTL_CONTRACTS', 300),
        'stakes' => (int) env('BLOCKCHAIN_CACHE_TTL_STAKES', 15),
        'rewards' => (int) env('BLOCKCHAIN_CACHE_TTL_REWARDS', 15),
        'events' => (int) env('BLOCKCHAIN_CACHE_TTL_EVENTS', 15),
        'stats' => (int) env('BLOCKCHAIN_CACHE_TTL_STATS', 15),
        'metrics' => (int) env('BLOCKCHAIN_CACHE_TTL_METRICS', 5),
    ],

    'contracts' => [
        'token' => [
            'name' => 'YieldForgeToken',
            'type' => 'erc20',
            'address' => env('YIELD_FORGE_TOKEN_ADDRESS', '0x5FbDB2315678afecb367f032d93F642f64180aa3'),
            'abi' => 'YieldForgeToken.json',
        ],
        'staking' => [
            'name' => 'YieldForgeStaking',
            'type' => 'staking',
            'address' => env('YIELD_FORGE_STAKING_ADDRESS', '0xe7f1725E7734CE288F8367e1Bb143E90bb3F0512'),
            'abi' => 'YieldForgeStaking.json',
        ],
    ],
];
