# YieldForge Protocol Analytics Engine Documentation (Phase B4)

## Overview

The B4 Analytics Engine processes read models built by Phase B3, generating real-time protocol KPIs, APY metrics, Total Value Locked (TVL) calculations, transaction history lists, and time-series snapshots for charting and web frontend consumption.

---

## 📊 Analytics Services & Aggregators

### 1. `AnalyticsService` (`App\Services\Analytics\AnalyticsService`)
Calculates protocol-wide KPIs:
- **Total Value Locked (TVL)**: Sum of all staked YF token balances across active pools.
- **Estimated APY**: Dynamic reward rate calculation based on reward distribution velocity and total staked pool TVL.
- **Protocol Statistics**: Active staker count, pool count, total transactions count.

### 2. `SnapshotBuilder` (`App\Services\Analytics\SnapshotBuilder`)
- Aggregates time-series statistics into `hourly_statistics` and `daily_statistics`.
- Captures TVL snapshot, APY snapshot, total rewards distributed, volume, and active user metrics per interval.

### 3. `ChartAnalyticsController` (`App\Http\Controllers\Api\V1\ChartAnalyticsController`)
- Formats time-series datasets into JSON arrays optimized for frontend charting libraries (Recharts / Chart.js):
  - `/api/v1/analytics/charts/tvl`: Historical TVL time-series.
  - `/api/v1/analytics/charts/apy`: Dynamic APY time-series.
  - `/api/v1/analytics/charts/rewards`: Cumulative rewards distributed time-series.
  - `/api/v1/analytics/charts/transactions`: Transaction volume & frequency count time-series.

---

## 🌐 Complete Analytics REST API Reference

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/analytics/overview` | Protocol overview KPIs (TVL, APY, Stakers, Pools) | None |
| `GET` | `/api/v1/analytics/tvl` | Real-time TVL breakdown per pool | None |
| `GET` | `/api/v1/analytics/apy` | Real-time APY calculation per pool | None |
| `GET` | `/api/v1/analytics/protocol` | Global protocol-level statistics summary | None |
| `GET` | `/api/v1/analytics/pools` | List of all active staking pool analytics | None |
| `GET` | `/api/v1/analytics/pools/{id}` | Detailed analytics for specific pool ID | None |
| `GET` | `/api/v1/analytics/wallet/{address}` | Wallet staking performance, TVL, and position analytics | None |
| `GET` | `/api/v1/analytics/rewards` | Protocol-wide reward distribution statistics | None |
| `GET` | `/api/v1/analytics/history` | Historical transaction list with pagination | None |
| `GET` | `/api/v1/analytics/charts/tvl` | Historical TVL time-series dataset for charts | None |
| `GET` | `/api/v1/analytics/charts/apy` | Historical APY time-series dataset for charts | None |
| `GET` | `/api/v1/analytics/charts/rewards` | Historical reward distribution time-series dataset | None |
| `GET` | `/api/v1/analytics/charts/transactions` | Historical transaction frequency & volume chart dataset | None |
| `GET` | `/api/v1/analytics/health` | Analytics Engine status & time-series snapshot freshness | None |
