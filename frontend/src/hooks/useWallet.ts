"use client";

import { useState, useEffect, useCallback } from "react";
import {
  connectWallet,
  getConnectedAccounts,
  getWalletChainId,
  hasWalletProvider,
  switchNetwork,
} from "@/lib/web3/wallet";
import { CHAIN_ID } from "@/lib/web3/client";
import { useProtocolStore } from "@/store/useProtocolStore";

export interface UseWalletReturn {
  account: string | null;
  chainId: number;
  isConnected: boolean;
  isConnecting: boolean;
  isCorrectNetwork: boolean;
  error: string | null;
  connect: () => Promise<void>;
  disconnect: () => void;
  switchToTargetNetwork: () => Promise<void>;
}

export function useWallet(): UseWalletReturn {
  const [account, setAccount] = useState<string | null>(null);
  const [chainId, setChainId] = useState<number>(0);
  const [isConnecting, setIsConnecting] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const { setWalletConnected } = useProtocolStore();

  const isConnected = Boolean(account);
  const isCorrectNetwork = chainId === CHAIN_ID;

  // Initialize and check current wallet status
  const checkConnection = useCallback(async () => {
    if (!hasWalletProvider()) return;

    try {
      const accounts = await getConnectedAccounts();
      if (accounts.length > 0) {
        setAccount(accounts[0]);
        setWalletConnected(true);
      } else {
        setAccount(null);
        setWalletConnected(false);
      }

      const currentChainId = await getWalletChainId();
      setChainId(currentChainId);
    } catch (err: any) {
      console.error("Error checking wallet connection:", err);
    }
  }, [setWalletConnected]);

  useEffect(() => {
    checkConnection();

    if (hasWalletProvider() && window.ethereum) {
      const handleAccountsChanged = (accounts: string[]) => {
        if (accounts.length > 0) {
          setAccount(accounts[0]);
          setWalletConnected(true);
        } else {
          setAccount(null);
          setWalletConnected(false);
        }
      };

      const handleChainChanged = (hexChainId: string) => {
        setChainId(parseInt(hexChainId, 16));
      };

      window.ethereum.on("accountsChanged", handleAccountsChanged);
      window.ethereum.on("chainChanged", handleChainChanged);

      return () => {
        if (window.ethereum?.removeListener) {
          window.ethereum.removeListener("accountsChanged", handleAccountsChanged);
          window.ethereum.removeListener("chainChanged", handleChainChanged);
        }
      };
    }
  }, [checkConnection, setWalletConnected]);

  const connect = async () => {
    setIsConnecting(true);
    setError(null);

    try {
      const accounts = await connectWallet();
      if (accounts.length > 0) {
        setAccount(accounts[0]);
        setWalletConnected(true);
      }
      const currentChainId = await getWalletChainId();
      setChainId(currentChainId);
    } catch (err: any) {
      setError(err.message || "Failed to connect wallet");
    } finally {
      setIsConnecting(false);
    }
  };

  const disconnect = () => {
    setAccount(null);
    setWalletConnected(false);
  };

  const switchToTargetNetwork = async () => {
    try {
      await switchNetwork(CHAIN_ID);
      setChainId(CHAIN_ID);
    } catch (err: any) {
      setError(err.message || "Failed to switch network");
    }
  };

  return {
    account,
    chainId,
    isConnected,
    isConnecting,
    isCorrectNetwork,
    error,
    connect,
    disconnect,
    switchToTargetNetwork,
  };
}
