# YieldForge: Decentralized 3D Yield Farming Platform (DeFi Learning Simulation)

YieldForge is an educational DeFi simulation platform built on Ethereum Sepolia Testnet. It demonstrates smart contract security, ERC-20 tokenomics, Web3 wallet integration, a Laravel REST API backend, and an interactive 3D futuristic farm built with React Three Fiber.

---

## Project Structure

```
YieldForge/
├── contracts/          # Hardhat & Solidity Smart Contracts (v2.29.0)
│   ├── contracts/
│   │   └── YieldForgeToken.sol   # YFT ERC-20 Token with Pausable & Custom Errors
│   ├── scripts/
│   │   └── deploy.ts             # Deployment & ABI Export Script
│   ├── test/
│   │   └── YieldForgeToken.test.ts # Hardhat Unit Tests (16 passing)
│   └── deployments/              # Exported network metadata
├── backend/            # Laravel 13 REST API & Database
└── frontend/           # Next.js 16 Client with React Three Fiber & Web3
```

---

## Deployment & Testing Instructions

### 1. Smart Contracts (`contracts/`)

#### Run Unit Tests
```bash
cd contracts
npx hardhat test
```

#### Deploy to Local Hardhat Network
```bash
cd contracts
npx hardhat run scripts/deploy.ts --network hardhat
```

#### Deploy to Ethereum Sepolia Testnet
1. Copy `.env.example` to `.env` in `contracts/`:
   ```bash
   cp .env.example .env
   ```
2. Set your `SEPOLIA_RPC_URL` and testnet account `PRIVATE_KEY`.
3. Execute the deployment:
   ```bash
   npx hardhat run scripts/deploy.ts --network sepolia
   ```

---

## Contract Verification Details

- **Token Name**: YieldForge Token
- **Symbol**: YFT
- **Decimals**: 18
- **Local Deployed Address**: `0x5FbDB2315678afecb367f032d93F642f64180aa3`
