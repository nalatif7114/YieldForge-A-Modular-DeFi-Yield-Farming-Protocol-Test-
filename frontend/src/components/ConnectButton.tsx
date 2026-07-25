"use client";

import { useWallet } from "@/hooks/useWallet";

/**
 * Truncates an Ethereum address to 0x1234...5678 format for clean UI display.
 */
function truncateAddress(address: string): string {
  if (!address) return "";
  return `${address.substring(0, 6)}...${address.substring(address.length - 4)}`;
}

export function ConnectButton() {
  const { account, isConnected, isConnecting, connect, disconnect } = useWallet();

  if (isConnecting) {
    return (
      <button
        disabled
        className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium text-sm bg-slate-800 text-slate-400 border border-slate-700 opacity-80 cursor-not-allowed transition-all"
      >
        <svg className="animate-spin h-4 w-4 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
          <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Connecting...
      </button>
    );
  }

  if (isConnected && account) {
    return (
      <div className="flex items-center gap-2">
        <div className="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900/90 border border-emerald-500/30 text-emerald-400 font-mono text-sm shadow-sm shadow-emerald-500/10">
          <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>{truncateAddress(account)}</span>
        </div>
        <button
          onClick={disconnect}
          className="px-3 py-2 rounded-xl font-medium text-xs bg-red-950/40 text-red-400 border border-red-800/40 hover:bg-red-900/50 hover:border-red-700/60 transition-all"
          title="Disconnect Wallet"
        >
          Disconnect
        </button>
      </div>
    );
  }

  return (
    <button
      onClick={connect}
      className="px-5 py-2.5 rounded-xl font-medium text-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold shadow-lg shadow-emerald-500/25 hover:shadow-emerald-400/40 hover:scale-[1.02] active:scale-[0.98] transition-all"
    >
      Connect Wallet
    </button>
  );
}
