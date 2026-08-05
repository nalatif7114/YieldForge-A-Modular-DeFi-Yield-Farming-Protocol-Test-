# YieldForge Architecture Documentation

## Overview & System Philosophy

YieldForge is an enterprise-grade, event-driven DeFi Yield Farming & Staking Protocol backend built on top of Ethereum Sepolia testnet. The system enforces strict architectural decoupling across discrete pipeline layers (Phases B1 to B7), guaranteeing that blockchain read operations, indexing, projection read-model building, analytics calculations, operational monitoring, and data intelligence remain decoupled and non-interfering.

```mermaid
graph TD
    subgraph "Layer 1: Blockchain Infrastructure (B1 & B2)"
        Sepolia["Ethereum Sepolia Testnet"]
        Contracts["YieldForge Token & Staking Contracts"]
        RpcClient["JSON-RPC Adapter Client"]
        Codec["Ethereum Event Decoder & Keccak-256 Hashes"]
    end

    subgraph "Layer 2: Event Sourcing & Indexer Engine (B3)"
        EventService["Event Indexer Service"]
        EventStore[("Blockchain Event Store")]
        Dispatcher["Event Dispatcher"]
        Projections["Read Model Projections (Wallet, Pool, Reward, Protocol, Block)"]
        Checkpoints[("Projection Checkpoints Store")]
    end

    subgraph "Layer 3: Protocol Analytics Engine (B4)"
        AnalyticsService["Analytics Service Engine"]
        SnapshotBuilder["Snapshot & Time-Series Engine"]
        ReadModels[("Read Model Datasets")]
    end

    subgraph "Layer 4: Institutional Monitoring & Alerting (B5)"
        HealthEngine["Health Score & Telemetry Calculator"]
        AlertEngine["Alert Rules & Notification Engine"]
        ExportEngine["Operational CSV/JSON Exporter"]
    end

    subgraph "Layer 5: Enterprise Security & Gateway (B6)"
        JwtService["JWT & SIWE Auth Engine"]
        Gateway["Adaptive Rate Limiter & Request Signer"]
        RBAC["Role-Based Access Controller (RBAC)"]
        AuditLogger["Audit Logger & Security Telemetry"]
    end

    subgraph "Layer 6: Data Intelligence & Research Platform (B7)"
        FeatureStore["Versioned Feature Store"]
        QualityEngine["Data Quality Engine"]
        ResearchEngine["Research Dataset & Export Engine"]
    end

    Sepolia --> Contracts
    Contracts --> RpcClient
    RpcClient --> Codec
    Codec --> EventService
    EventService --> EventStore
    EventStore --> Dispatcher
    Dispatcher --> Projections
    Projections --> Checkpoints
    Projections --> ReadModels
    ReadModels --> AnalyticsService
    ReadModels --> HealthEngine
    ReadModels --> FeatureStore
    AnalyticsService --> SnapshotBuilder
    HealthEngine --> AlertEngine
    HealthEngine --> ExportEngine
    Gateway --> JwtService
    Gateway --> RBAC
    RBAC --> AuditLogger
    FeatureStore --> QualityEngine
    QualityEngine --> ResearchEngine
```

---

## 🏛️ Layered Architectural Breakdown

### 1. Blockchain Layer (Phases B1 & B2)
- **YieldForgeToken** (`ERC20`): Minting, transfer logic, allowances.
- **YieldForgeStaking**: Pool management, stake/withdraw functions, reward calculation.
- **RpcClient & EthereumCodec**: Connects to Sepolia RPC endpoints via JSON-RPC `eth_getLogs` and `eth_blockNumber`, encoding event topics using Keccak-256 hashes (`Staked`, `Withdrawn`, `RewardPaid`, `PoolAdded`).

### 2. Event Sourcing Engine (Phase B3)
- Stores un-mutated log entries in `blockchain_events`.
- Employs CQRS (Command Query Responsibility Segregation) pattern: Write side consumes raw log events; Read side updates isolated projection tables (`wallet_positions`, `pool_snapshots`, `reward_snapshots`, `protocol_statistics`, `block_checkpoints`).
- Atomic database transactions persist event logs and advance `projection_checkpoints` deterministically.

### 3. Protocol Analytics Engine (Phase B4)
- Aggregates historical metrics into time-series data streams (`hourly_statistics`, `daily_statistics`).
- Computes real-time TVL (Total Value Locked), APY (Annual Percentage Yield), transaction throughput, and chart datasets.

### 4. Institutional Monitoring Platform (Phase B5)
- Evaluates system health score (0-100) combining indexer block lag, RPC latency, memory usage, queue length, and failure rates.
- Manages operational alert rules and CSV/JSON event stream exporters.

### 5. Enterprise Security & API Gateway (Phase B6)
- **Authentication**: JWT access tokens (HS256) and EIP-4361 Sign-In With Ethereum (SIWE) nonces with EIP-191 signature validation.
- **API Gateway**: Adaptive sliding-window rate limiting, HMAC-SHA256 request signature validation (`X-Signature`, `X-Timestamp`, `X-Nonce`), replay attack prevention, and API key management with IP allowlists.

### 6. Data Intelligence & Research Platform (Phase B7)
- Computes 10 ML research features (`wallet_age_days`, `average_stake_formatted`, `staking_frequency`, `holding_duration_days`, `reward_velocity`, `stake_growth_pct`, `unstake_ratio`, `active_days`, `transaction_interval_hours`, `pool_diversity_count`).
- Maintains versioned feature sets in `feature_sets` and runs 5-point data quality validation suites.

---

## 🔄 Component Interaction & Data Flow

```mermaid
sequenceDiagram
    autonumber
    participant RPC as Sepolia RPC Endpoint
    participant B2 as B2 Blockchain Adapter
    participant B3 as B3 Event Sourcing Indexer
    participant B4 as B4 Analytics Engine
    participant B5 as B5 Monitoring Platform
    participant B6 as B6 Security Gateway
    participant B7 as B7 Research Platform

    B3->>B2: Request logs (eth_getLogs)
    B2->>RPC: Send JSON-RPC payload
    RPC-->>B2: Return raw log array
    B2->>B2: Decode log payload via Keccak-256 ABI
    B2-->>B3: Pass decoded BlockchainEvent DTO
    B3->>B3: Store in blockchain_events table & update Projections
    B4->>B3: Query read models (wallet_positions, pool_snapshots)
    B4->>B4: Compute TVL, APY, Time-Series aggregations
    B5->>B3: Inspect Projection Checkpoints & Indexer lag
    B5->>B5: Compute System Health Score (0-100) & evaluate Alerts
    B6->>B6: Validate Request JWT / Signature / Rate Limits
    B7->>B3: Fetch historical event logs & position records
    B7->>B7: Calculate ML feature vectors & validate data quality score
```
