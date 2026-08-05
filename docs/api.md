# YieldForge REST API Reference Specification

## Overview

The YieldForge REST API provides 61 endpoints grouped into 6 core functional modules: Authentication, Blockchain Core, Protocol Analytics, Institutional Monitoring, Enterprise Security, and Data Intelligence & Research.

- **Base URL**: `/api/v1`
- **Content-Type**: `application/json`
- **Authentication**: Bearer Token (`Authorization: Bearer <jwt>`) or API Key (`X-API-Key: yf_live_<key>`).

---

## 🔐 1. Authentication Module

### `POST /api/v1/auth/login`
- **Description**: Authenticates user via email and password.
- **Request Body**:
  ```json
  {
    "email": "admin@yieldforge.io",
    "password": "AdminSecretPassword123!"
  }
  ```
- **Response Example (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "access_token": "eyJhbGciOiJIUzI1Ni...",
      "refresh_token": "a1b2c3d4e5...",
      "token_type": "Bearer",
      "expires_in_seconds": 3600,
      "user": {
        "id": 1,
        "name": "System Administrator",
        "email": "admin@yieldforge.io",
        "roles": ["admin"]
      }
    }
  }
  ```

### `GET /api/v1/auth/nonce`
- **Description**: Generates an EIP-4361 SIWE nonce for wallet authentication.
- **Query Parameters**: `wallet_address` (required string).
- **Response Example (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "nonce": "9f8e7d6c5b4a3f2e",
      "wallet_address": "0x86b6346984f6f9380a94bc0d2c006044649f2077",
      "statement": "Sign in with Ethereum to YieldForge Protocol Operations Platform."
    }
  }
  ```

### `POST /api/v1/auth/verify`
- **Description**: Verifies SIWE wallet signature and returns access token.
- **Request Body**:
  ```json
  {
    "wallet_address": "0x86B6346984F6f9380A94bC0d2C006044649f2077",
    "signature": "0x...",
    "nonce": "9f8e7d6c5b4a3f2e"
  }
  ```

### `POST /api/v1/auth/refresh`
- **Description**: Refreshes expired JWT access token using valid refresh token.

### `GET /api/v1/auth/me`
- **Description**: Retrieves current authenticated user profile and roles.

### `POST /api/v1/auth/logout`
- **Description**: Revokes current active refresh tokens.

---

## ⛓️ 2. Blockchain Core Module

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/network` | `GET` | Sepolia network status, chain ID (`11155111`), and RPC connection status |
| `/api/v1/contracts` | `GET` | Deployed YieldForgeToken and YieldForgeStaking contract addresses |
| `/api/v1/pools` | `GET` | Active staking pool list and addresses |
| `/api/v1/stakes/{wallet}` | `GET` | Staking positions for specified wallet address |
| `/api/v1/rewards/{wallet}` | `GET` | Claimable rewards summary for specified wallet address |
| `/api/v1/events` | `GET` | Chronological list of decoded on-chain events |
| `/api/v1/health` | `GET` | Blockchain adapter health and Sepolia RPC connectivity |
| `/api/v1/indexer` | `GET` | Event Indexer status and projection checkpoint heights |
| `/api/v1/indexer/metrics` | `GET` | Indexer throughput and sync performance metrics |
| `/api/v1/stats` | `GET` | Protocol statistics overview |

---

## 📈 3. Protocol Analytics Module (B4)

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/analytics/overview` | `GET` | Protocol overview KPIs (TVL, APY, active stakers) |
| `/api/v1/analytics/tvl` | `GET` | Real-time TVL breakdown across pools |
| `/api/v1/analytics/apy` | `GET` | Dynamic APY calculation per pool |
| `/api/v1/analytics/protocol` | `GET` | Protocol-level historical statistics summary |
| `/api/v1/analytics/pools` | `GET` | All staking pools analytics summary |
| `/api/v1/analytics/pools/{id}` | `GET` | Detailed analytics for pool ID |
| `/api/v1/analytics/wallet/{address}` | `GET` | Wallet performance & position analytics |
| `/api/v1/analytics/rewards` | `GET` | Reward distribution metrics |
| `/api/v1/analytics/history` | `GET` | Historical transaction list |
| `/api/v1/analytics/charts/tvl` | `GET` | TVL time-series chart dataset |
| `/api/v1/analytics/charts/apy` | `GET` | APY time-series chart dataset |
| `/api/v1/analytics/charts/rewards` | `GET` | Rewards time-series chart dataset |
| `/api/v1/analytics/charts/transactions` | `GET` | Transaction throughput chart dataset |
| `/api/v1/analytics/health` | `GET` | Analytics engine time-series freshness health |

---

## 🛠️ 4. Institutional Monitoring Module (B5)

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/monitoring/dashboard` | `GET` | Main operational dashboard metrics & System Health Score |
| `/api/v1/monitoring/health` | `GET` | Telemetry health score breakdown |
| `/api/v1/monitoring/queues` | `GET` | Background job queue status and queue length |
| `/api/v1/monitoring/cache` | `GET` | Cache hit/miss rates, memory usage, driver status |
| `/api/v1/monitoring/indexer/history` | `GET` | Historical indexer block sync logs |
| `/api/v1/monitoring/rpc` | `GET` | Sepolia RPC request latency & call metrics |
| `/api/v1/monitoring/alerts` | `GET` | Active and historical operational alerts |
| `/api/v1/monitoring/alerts/{id}/acknowledge` | `POST` | Acknowledge an alert |
| `/api/v1/monitoring/alerts/rules` | `GET` | Configured monitoring alert threshold rules |
| `/api/v1/monitoring/history` | `GET` | Historical monitoring metrics log |
| `/api/v1/monitoring/export/events` | `GET` | Export event log data stream (CSV / JSON) |
| `/api/v1/monitoring/export/metrics` | `GET` | Export metrics data stream (CSV / JSON) |
| `/api/v1/monitoring/performance` | `GET` | System resource consumption (Memory, CPU) |

---

## 🔒 5. Enterprise Security Module (B6)

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/security/dashboard` | `GET` | Security telemetry, active sessions, failed logins |
| `/api/v1/security/audit` | `GET` | Audit log activity trail |
| `/api/v1/security/rate-limit` | `GET` | Adaptive rate limiter configuration & headers |
| `/api/v1/security/sessions` | `GET` | Active user refresh token sessions |
| `/api/v1/security/api-keys` | `GET` | List active API keys |
| `/api/v1/security/api-keys` | `POST` | Generate new API key with custom scopes |
| `/api/v1/security/api-keys/{id}` | `DELETE` | Revoke specified API key |

---

## 🧪 6. Data Intelligence & Research Module (B7)

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/research/dashboard` | `GET` | Research platform health, dataset quality score, feature sets |
| `/api/v1/research/wallets` | `GET` | Wallets list with calculated ML feature vectors |
| `/api/v1/research/pools` | `GET` | Staking pool activity & utilization features |
| `/api/v1/research/features` | `GET` | Versioned feature sets and sample vectors |
| `/api/v1/research/events` | `GET` | Research event store dataset |
| `/api/v1/research/statistics` | `GET` | Time-series aggregates & 24h/7d/30d/90d benchmarks |
| `/api/v1/research/export/{type}` | `GET` | Export dataset stream (`wallet_behavior`, `pool_activity`, etc.) in JSON or CSV |
