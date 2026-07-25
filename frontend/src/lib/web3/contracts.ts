import tokenArtifact from "@/contracts/YieldForgeToken.json";
import { formatUnits, parseUnits, Address } from "viem";

// Fallback to deployed contract address artifact if env is empty
export const TOKEN_ADDRESS = (process.env.NEXT_PUBLIC_TOKEN_ADDRESS || tokenArtifact.address) as Address;

export const TOKEN_ABI = tokenArtifact.abi;

/**
 * Reusable Viem contract configuration object for YieldForgeToken
 */
export const YFT_CONTRACT_CONFIG = {
  address: TOKEN_ADDRESS,
  abi: TOKEN_ABI,
} as const;

/**
 * Formats a raw bigint token amount (18 decimals) into human-readable string.
 * @param amount Raw bigint in wei / 18 decimals
 * @param decimals Number of decimal places to format (default: 2)
 */
export function formatTokenAmount(amount: bigint, decimals: number = 2): string {
  const formatted = formatUnits(amount, 18);
  const num = parseFloat(formatted);
  return num.toLocaleString(undefined, {
    minimumFractionDigits: 0,
    maximumFractionDigits: decimals,
  });
}

/**
 * Parses a user input string (e.g. "100.5") into raw bigint with 18 decimals.
 */
export function parseTokenAmount(amountStr: string): bigint {
  if (!amountStr || isNaN(Number(amountStr))) return 0n;
  return parseUnits(amountStr, 18);
}
