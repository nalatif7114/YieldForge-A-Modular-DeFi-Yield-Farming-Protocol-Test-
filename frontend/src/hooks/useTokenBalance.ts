"use client";

import { useState, useEffect, useCallback } from "react";
import { useContract } from "./useContract";
import { formatTokenAmount } from "@/lib/web3/contracts";
import { Address } from "viem";

export interface UseTokenBalanceReturn {
  rawBalance: bigint;
  formattedBalance: string;
  totalSupply: bigint;
  formattedTotalSupply: string;
  isLoading: boolean;
  error: string | null;
  refetch: () => Promise<void>;
}

export function useTokenBalance(accountAddress: string | null): UseTokenBalanceReturn {
  const [rawBalance, setRawBalance] = useState<bigint>(0n);
  const [totalSupply, setTotalSupply] = useState<bigint>(0n);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const { getBalance, getTotalSupply } = useContract();

  const fetchBalances = useCallback(async () => {
    setIsLoading(true);
    setError(null);

    try {
      // Fetch Total Supply
      const supply = await getTotalSupply();
      setTotalSupply(supply);

      // Fetch User Balance if account address exists
      if (accountAddress) {
        const bal = await getBalance(accountAddress as Address);
        setRawBalance(bal);
      } else {
        setRawBalance(0n);
      }
    } catch (err: any) {
      console.error("Error fetching token balance:", err);
      setError(err.message || "Failed to fetch YFT balance");
    } finally {
      setIsLoading(false);
    }
  }, [accountAddress, getBalance, getTotalSupply]);

  useEffect(() => {
    fetchBalances();
  }, [fetchBalances]);

  return {
    rawBalance,
    formattedBalance: formatTokenAmount(rawBalance),
    totalSupply,
    formattedTotalSupply: formatTokenAmount(totalSupply),
    isLoading,
    error,
    refetch: fetchBalances,
  };
}
