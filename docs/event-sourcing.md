# YieldForge Event Sourcing & Indexer Engine Documentation (Phase B3)

## Overview

Phase B3 implements a Command Query Responsibility Segregation (CQRS) Event Sourcing engine for YieldForge. The indexer reads on-chain events from Ethereum Sepolia via the B2 Blockchain Adapter, appends un-mutated log entries to the `blockchain_events` immutable event store, and dispatches each event to 5 dedicated read-model projection handlers.

---

## 🗄️ Core Components

### 1. `blockchain_events` (Immutable Event Store)
Every decoded on-chain log is persisted with its `transaction_hash`, `log_index`, `contract_address`, `event_name`, `block_number`, `block_hash`, raw `payload`, and `decoded_payload`.

### 2. `EventDispatcher` (`App\Services\Indexer\EventDispatcher`)
Receives decoded event DTOs and routes each event sequentially to registered projection handlers:
- `BlockProjection`
- `WalletProjection`
- `PoolProjection`
- `RewardProjection`
- `ProtocolProjection`

### 3. Read Model Projections
- **BlockProjection**: Tracks current indexed block number in `block_checkpoints`.
- **WalletProjection**: Maintains `wallet_positions` (staked balance, total rewards, active status per wallet).
- **PoolProjection**: Maintains `pool_snapshots` (pool total staked balance, stakers count, active status).
- **RewardProjection**: Maintains `reward_snapshots` (rewards distributed per wallet and pool).
- **ProtocolProjection**: Maintains global `protocol_statistics` (Total Value Locked, active stakers, event counts).

### 4. `ProjectionCheckpoint` & Replay Engine
- `projection_checkpoints` stores the exact `last_processed_block` and `checkpoint_version` for each projection.
- **Atomic Transactions**: Block range sync is wrapped in `DB::transaction()` guaranteeing that events, projections, and checkpoints advance atomically.
- **Replay Engine**: Invoking `php artisan indexer:replay` truncates read model tables, resets checkpoints to block #0, and re-executes all projection handlers over stored `blockchain_events` deterministically.

---

## 🔄 Flow Diagram: Event Ingestion & Projection Pipeline

```mermaid
flowchart TD
    A[Cron / CLI command php artisan blockchain:sync] --> B[BlockchainIndexer]
    B --> C[Fetch RPC Block Head & Projection Checkpoint]
    C --> D{Block Head > Checkpoint?}
    D -- No --> E[End Sync Loop]
    D -- Yes --> F[Fetch eth_getLogs in Range fromBlock..toBlock]
    F --> G[Decode Logs via EthereumCodec]
    G --> H[Begin DB Transaction]
    H --> I[Insert into blockchain_events table]
    I --> J[EventDispatcher dispatches event to Projections]
    J --> K1[WalletProjection: Update wallet_positions]
    J --> K2[PoolProjection: Update pool_snapshots]
    J --> K3[RewardProjection: Update reward_snapshots]
    J --> K4[ProtocolProjection: Update protocol_statistics]
    J --> K5[BlockProjection: Update block_checkpoints]
    K1 & K2 & K3 & K4 & K5 --> L[Update projection_checkpoints to toBlock]
    L --> M[Commit DB Transaction]
```
