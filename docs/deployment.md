# YieldForge Deployment & Environment Configuration Guide

## Overview

This guide details instructions for setting up, configuring, running, and deploying the YieldForge backend across local development, Sepolia testnet, and production environments.

---

## ⚙️ Environment Variables Reference (`.env`)

```ini
APP_NAME=YieldForge
APP_ENV=local
APP_KEY=base64:KM+wL8ee9l4OxMu3tR0pmj92aKOsOKSDnVJo8VD4bM4=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Cache & Queue Configuration
CACHE_STORE=database
QUEUE_CONNECTION=database

# Blockchain Sepolia Network Configuration
BLOCKCHAIN_RPC_URL=https://ethereum-sepolia-rpc.publicnode.com
BLOCKCHAIN_CHAIN_ID=11155111
BLOCKCHAIN_NETWORK_NAME=sepolia

# Smart Contract Addresses (Sepolia Deployment)
YIELD_FORGE_TOKEN_ADDRESS=0x527336e72F31840aF45aB3BDDfd7d3958BF5758f
YIELD_FORGE_STAKING_ADDRESS=0xfeA5aaB6C60C48c080AAeFC7eAD22610004E1A5F
```

---

## 🚀 Local Development Quick Start

### 1. Prerequisites
- PHP >= 8.2 with SQLite and OpenSSL extensions enabled.
- Composer >= 2.5
- Node.js >= 18.0 & npm (for frontend)

### 2. Backend Setup
```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Initialize environment configuration
cp .env.example .env

# Generate Application Encryption Key
php artisan key:generate

# Run Database Migrations & Seed Default Security Roles
php artisan migrate --force
php artisan db:seed --class=SecuritySeeder
```

### 3. Start Local Development Server
```bash
# Launch Laravel Development Server
php artisan serve --port=8000

# Launch Background Queue Worker (in separate terminal)
php artisan queue:work

# Launch Scheduled Indexer Sync & Cleanup (in separate terminal)
php artisan schedule:work
```

---

## ⛓️ Blockchain Indexing & Sync Commands

```bash
# Trigger real-time block sync from Sepolia testnet RPC
php artisan blockchain:sync

# Replay event store projections from block #0
php artisan indexer:replay

# Perform automated event store integrity verification
php artisan blockchain:verify
```

---

## 🧪 Research & Security Operations Commands

```bash
# Run Security Health Audit Report
php artisan security:audit

# Purge expired refresh tokens, nonces, and old audit logs
php artisan security:cleanup --days=90

# Rotate API Key secret
php artisan security:apikey:rotate {id}

# Build ML Research Datasets & Feature Vectors
php artisan research:build

# Validate Data Quality Across Research Datasets
php artisan research:validate

# Export Research Datasets to JSON or CSV
php artisan research:export wallet_behavior --format=json
```

---

## 🏭 Production Recommendations

1. **Database & Cache**: Use PostgreSQL for `DB_CONNECTION` and Redis for `CACHE_STORE`, `SESSION_DRIVER`, and `QUEUE_CONNECTION`.
2. **Process Manager**: Manage `php artisan queue:work` processes using Supervisor.
3. **SSL/TLS**: Terminate TLS using Nginx or Cloudflare in front of the API Gateway.
4. **Rate Limiting**: Enable Redis-backed sliding window rate limiting.
