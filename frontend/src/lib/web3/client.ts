import { createPublicClient, createWalletClient, custom, http, Chain } from "viem";
import { hardhat, sepolia } from "viem/chains";

// Environment variables
export const CHAIN_ID = Number(process.env.NEXT_PUBLIC_CHAIN_ID || 31337);
export const RPC_URL = process.env.NEXT_PUBLIC_RPC_URL || "http://127.0.0.1:8545";

/**
 * Selects Viem Chain configuration based on target CHAIN_ID environment variable.
 */
export function getTargetChain(): Chain {
  if (CHAIN_ID === 11155111) {
    return sepolia;
  }
  // Default to local Hardhat chain (31337)
  return hardhat;
}

export const targetChain = getTargetChain();

/**
 * Viem PublicClient instance for reading read-only smart contract state.
 * Uses HTTP transport targeting configured RPC URL.
 */
export const publicClient = createPublicClient({
  chain: targetChain,
  transport: http(RPC_URL),
});

/**
 * Creates a Viem WalletClient attached to window.ethereum for signing transactions.
 * Returns null if no window.ethereum is present.
 */
export function createBrowserWalletClient() {
  if (typeof window === "undefined" || !window.ethereum) {
    return null;
  }

  return createWalletClient({
    chain: targetChain,
    transport: custom(window.ethereum),
  });
}
