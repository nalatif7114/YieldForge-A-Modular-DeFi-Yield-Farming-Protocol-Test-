# YieldForge Testing Strategy & Test Suite Specification

## Overview

YieldForge implements a comprehensive, automated unit and feature test suite ensuring 100% verification across all protocol backend layers (B1 to B7). The test suite executes in isolated SQLite in-memory databases with automatic database transaction refresh.

---

## 📊 Test Suite Summary

```bash
$ php vendor/phpunit/phpunit/phpunit
{"tool":"phpunit","result":"passed","tests":104,"passed":104,"assertions":553,"duration_ms":5000}
```

- **Total Unit & Feature Tests**: **104 tests**
- **Total Assertions**: **553 assertions**
- **Test Status**: **100% Passed (0 failures, 0 errors)**

---

## 🧪 Test Suite Organization

### 1. Unit Tests (`tests/Unit/`)
- `Unit/Security/JwtServiceTest.php`: Tests access token issuance, HS256 signature verification, and expiration validation.
- `Unit/Security/SiweAuthServiceTest.php`: Tests SIWE nonce generation, EIP-191 signature validation, and nonce replay prevention.
- `Unit/Security/RbacServiceTest.php`: Tests user role assignment, scope evaluation, and admin permission overrides.
- `Unit/Security/ApiKeyServiceTest.php`: Tests API key creation (`yf_live_...`), SHA-256 key hashing, expiration, and IP allowlist enforcement.
- `Unit/Security/RequestSignatureServiceTest.php`: Tests HMAC-SHA256 request signature, timestamp freshness (300s window), and nonce replay.
- `Unit/Security/AuditLoggerServiceTest.php`: Tests audit log persistence, security event logging, and login attempt tracking.
- `Unit/Security/SecurityCommandsTest.php`: Tests `security:audit`, `security:cleanup`, and `security:apikey:rotate` CLI commands.
- `Unit/Research/FeatureCalculatorTest.php`: Tests 10 ML research feature calculations (`wallet_age_days`, `average_stake`, `staking_frequency`, etc.).
- `Unit/Research/DataQualityEngineTest.php`: Tests 5-point data quality validation suite (missing values, duplicate events, timestamps, outliers, completeness).
- `Unit/Research/FeatureStoreServiceTest.php`: Tests versioned feature set registration and feature vector computation.
- `Unit/Research/BenchmarkServiceTest.php`: Tests protocol windowed metric benchmarks (24h, 7d, 30d, 90d).
- `Unit/Research/ResearchCommandsTest.php`: Tests `research:build`, `research:validate`, and `research:export` CLI commands.
- `Unit/Research/ResearchTimeSeriesBuilderTest.php`: Tests hourly and daily research time series aggregations.

### 2. Feature Tests (`tests/Feature/`)
- `Feature/Security/SecurityAuthEndpointsTest.php`: Tests `/api/v1/auth/login`, `/me`, `/refresh`, and `/logout` API flows.
- `Feature/Security/SiweAuthEndpointsTest.php`: Tests SIWE `/api/v1/auth/nonce` and `/verify` API flows.
- `Feature/Security/SecurityMonitoringEndpointsTest.php`: Tests `/api/v1/security/dashboard`, `/audit`, `/rate-limit`, `/sessions` endpoints.
- `Feature/Security/ApiKeyEndpointsTest.php`: Tests API key creation, listing, and revocation endpoints.
- `Feature/Security/SecurityMiddlewareTest.php`: Tests `JwtAuthMiddleware`, `RbacMiddleware`, `RequestSignatureMiddleware`, `ReplayProtectionMiddleware`, and `AdaptiveRateLimiterMiddleware`.
- `Feature/Research/ResearchEndpointsTest.php`: Tests `/api/v1/research/dashboard`, `/wallets`, `/pools`, `/features`, `/events`, `/statistics` endpoints.
- `Feature/Research/ResearchExportTest.php`: Tests `/api/v1/research/export/{type}` JSON and CSV export endpoints.

---

## 🏃 Running Tests

```bash
# Run complete test suite
php artisan test

# Run tests via PHPUnit directly
php vendor/phpunit/phpunit/phpunit

# Run specific test class
php artisan test --filter=JwtServiceTest

# Run specific test method
php artisan test --filter=test_issue_access_token_and_validate_payload
```
