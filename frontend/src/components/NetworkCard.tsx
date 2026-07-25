"use client";

import { useWallet } from "@/hooks/useWallet";
import { CHAIN_ID } from "@/lib/web3/client";

function getNetworkName(id: number): string {
  switch (id) {
    case 31337:
      return "Hardhat Local Network";
    case 11155111:
      return "Ethereum Sepolia Testnet";
    case 1:
      return "Ethereum Mainnet";
    default:
      return id ? `Unknown Network (ID: ${id})` : "Disconnected";
  }
}

export function NetworkCard() {
  const { chainId, isConnected, isCorrectNetwork, switchToTargetNetwork } = useWallet();

  const networkName = getNetworkName(chainId);
  const targetNetworkName = getNetworkName(CHAIN_ID);

  if (!isConnected) {
    return (
      <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm">
        <div className="flex items-center justify-between">
          <span className="text-xs font-medium uppercase tracking-wider text-slate-400">Network Status</span>
          <span className="w-2.5 h-2.5 rounded-full bg-slate-600"></span>
        </div>
        <div className="mt-3">
          <h3 className="text-lg font-semibold text-slate-300">Not Connected</h3>
          <p className="text-xs text-slate-500 mt-1">Connect your Web3 wallet to check network status.</p>
        </div>
      </div>
    );
  }

  if (!isCorrectNetwork) {
    return (
      <div className="p-6 rounded-2xl bg-amber-950/30 border border-amber-500/40 backdrop-blur-sm shadow-lg shadow-amber-500/5">
        <div className="flex items-center justify-between">
          <span className="text-xs font-medium uppercase tracking-wider text-amber-400">Network Alert</span>
          <span className="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
        </div>
        <div className="mt-3">
          <h3 className="text-lg font-semibold text-amber-200">Wrong Network Detected</h3>
          <p className="text-xs text-amber-300/80 mt-1">
            Wallet is connected to <span className="font-semibold text-amber-100">{networkName}</span>. Target network is <span className="font-semibold text-amber-100">{targetNetworkName}</span>.
          </p>
        </div>
        <button
          onClick={switchToTargetNetwork}
          className="mt-4 w-full py-2.5 px-4 rounded-xl text-xs font-semibold bg-amber-500 hover:bg-amber-400 text-slate-950 transition-all shadow-md shadow-amber-500/20"
        >
          Switch to {targetNetworkName}
        </button>
      </div>
    );
  }

  return (
    <div className="p-6 rounded-2xl bg-slate-900/60 border border-emerald-500/30 backdrop-blur-sm">
      <div className="flex items-center justify-between">
        <span className="text-xs font-medium uppercase tracking-wider text-slate-400">Active Network</span>
        <span className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
          <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          Connected
        </span>
      </div>
      <div className="mt-3">
        <h3 className="text-lg font-semibold text-white">{networkName}</h3>
        <p className="text-xs text-slate-400 mt-1">Chain ID: <span className="font-mono text-emerald-400">{chainId}</span></p>
      </div>
    </div>
  );
}
