"use client";

import { useCallback } from "react";
import { publicClient, createBrowserWalletClient } from "@/lib/web3/client";
import { YFT_CONTRACT_CONFIG } from "@/lib/web3/contracts";
import { Address } from "viem";

export function useContract() {
  /**
   * Reads YFT balance of a specific address from the contract.
   */
  const getBalance = useCallback(async (accountAddress: Address): Promise<bigint> => {
    try {
      const balance = await publicClient.readContract({
        ...YFT_CONTRACT_CONFIG,
        functionName: "balanceOf",
        args: [accountAddress],
      });
      return balance as bigint;
    } catch (error) {
      console.error("Failed to read balanceOf from YFT contract:", error);
      return 0n;
    }
  }, []);

  /**
   * Reads total supply of YFT tokens from the contract.
   */
  const getTotalSupply = useCallback(async (): Promise<bigint> => {
    try {
      const totalSupply = await publicClient.readContract({
        ...YFT_CONTRACT_CONFIG,
        functionName: "totalSupply",
      });
      return totalSupply as bigint;
    } catch (error) {
      console.error("Failed to read totalSupply from YFT contract:", error);
      return 0n;
    }
  }, []);

  /**
   * Mints new YFT tokens to an account (Owner only).
   */
  const mint = useCallback(
    async (userAccount: Address, toAddress: Address, amountWei: bigint): Promise<`0x${string}`> => {
      const walletClient = createBrowserWalletClient();
      if (!walletClient) {
        throw new Error("No wallet client available. Please connect MetaMask.");
      }

      const { request } = await publicClient.simulateContract({
        ...YFT_CONTRACT_CONFIG,
        functionName: "mint",
        args: [toAddress, amountWei],
        account: userAccount,
      });

      const hash = await walletClient.writeContract(request);
      await publicClient.waitForTransactionReceipt({ hash });
      return hash;
    },
    []
  );

  return {
    getBalance,
    getTotalSupply,
    mint,
  };
}
