# YieldForge Comprehensive System Diagrams Collection

This document aggregates all system architecture, component, data flow, sequence, ERD, and operational workflow diagrams for the YieldForge protocol.

---

## 1. System Layer Architecture Diagram

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

## 2. Blockchain Ingestion Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    participant Indexer as BlockchainIndexer
    participant EventSvc as EventService
    participant Codec as EthereumCodec
    participant RPC as RpcClient
    participant EthNode as Sepolia RPC Node

    Indexer->>EventSvc: getEvents(fromBlock=11415600, toBlock=11415700)
    EventSvc->>Codec: getTopicHashes()
    Codec-->>EventSvc: Return [0x9e71bc..., 0x708450..., 0xe24465...]
    EventSvc->>RPC: send('eth_getLogs', [payload])
    RPC->>EthNode: POST / HTTP/1.1 {"jsonrpc":"2.0","method":"eth_getLogs",...}
    EthNode-->>RPC: 200 OK {"jsonrpc":"2.0","result":[raw_logs...]}
    RPC-->>EventSvc: Raw Log Array
    loop For Each Raw Log
        EventSvc->>Codec: decodeLog(rawLog)
        Codec-->>EventSvc: Decoded Event DTO (user, amount, poolId)
    end
    EventSvc-->>Indexer: Decoded Event List
```

---

## 3. Event Sourcing Ingestion & Projection Flowchart

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

---

## 4. SIWE Authentication Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    participant User as Web3 User / Wallet
    participant API as SiweAuthController
    participant SiweSvc as SiweAuthService
    participant JwtSvc as JwtService
    participant DB as Security Database

    User->>API: GET /api/v1/auth/nonce?wallet_address=0x86B6...
    API->>SiweSvc: generateNonce("0x86b6...")
    SiweSvc->>DB: Store record in wallet_nonces (expires in 10 mins)
    DB-->>SiweSvc: Created WalletNonce
    SiweSvc-->>API: Return Nonce string
    API-->>User: {"nonce": "a1b2c3d4...", "statement": "Sign in with Ethereum..."}

    User->>User: Sign EIP-4361 message using Private Key
    User->>API: POST /api/v1/auth/verify {"wallet_address":"0x86B6...", "signature":"0x...", "nonce":"a1b2c3d4..."}
    API->>SiweSvc: verifySignature(wallet, signature, nonce)
    SiweSvc->>DB: Query wallet_nonces where nonce = 'a1b2c3d4...' AND used = false
    SiweSvc->>SiweSvc: Verify EIP-191 Signature against wallet address
    SiweSvc->>DB: Update nonce used = true & fetch/create User record
    SiweSvc-->>API: Return User model
    API->>JwtSvc: issueAccessToken(user)
    JwtSvc-->>API: Return JWT string
    API-->>User: {"access_token": "eyJhbG...", "refresh_token": "def456...", "user": {...}}
```

---

## 5. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    users ||--o{ role_user : has
    roles ||--o{ role_user : belongs_to
    roles ||--o{ permission_role : has
    permissions ||--o{ permission_role : belongs_to
    users ||--o{ api_keys : owns
    users ||--o{ refresh_tokens : maintains
    users ||--o{ audit_logs : generates

    research_datasets ||--o{ dataset_versions : tracks

    blockchain_events {
        bigint id PK
        string transaction_hash
        int log_index
        string contract_address
        string event_name
        string topic_hash
        bigint block_number
        string block_hash
        json payload
        json decoded_payload
        timestamp timestamp
    }

    projection_checkpoints {
        bigint id PK
        string projection_name UK
        bigint last_processed_block
        string checkpoint_version
        timestamp updated_at
    }

    wallet_positions {
        bigint id PK
        string wallet UK
        string staked_balance_raw
        string staked_balance_formatted
        bigint pool_id
        boolean is_active
    }

    pool_snapshots {
        bigint id PK
        bigint pool_id UK
        string total_staked_raw
        string total_staked_formatted
        int stakers_count
        boolean is_active
    }

    wallet_features {
        bigint id PK
        string wallet_address UK
        int wallet_age_days
        string average_stake_formatted
        float staking_frequency
        int holding_duration_days
        float reward_velocity
        float stake_growth_pct
        float unstake_ratio
        int active_days
        float transaction_interval_hours
        int pool_diversity_count
        string feature_version
    }

    users {
        bigint id PK
        string name
        string email UK
        string password
        string wallet_address
        boolean is_active
        timestamp last_login_at
    }

    api_keys {
        bigint id PK
        bigint user_id FK
        string name
        string key_prefix
        string key_hash UK
        json scopes
        json ip_allowlist
        timestamp expires_at
        timestamp last_used_at
    }
```
