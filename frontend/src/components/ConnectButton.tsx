"use client";

import { useWallet } from "@/hooks/useWallet";

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
        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-mono bg-white/[0.04] text-slate-400 border border-white/10 opacity-70 cursor-not-allowed"
      >
        <svg className="animate-spin h-3.5 w-3.5 text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
        <div className="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/[0.04] border border-violet-500/30 text-slate-200 font-mono text-xs">
          <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>{truncateAddress(account)}</span>
        </div>
        <button
          onClick={disconnect}
          className="px-2.5 py-1.5 rounded-xl font-mono text-[11px] bg-red-950/30 text-red-400 border border-red-800/30 hover:bg-red-900/40 transition-all"
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
      className="px-4 py-2 rounded-xl text-xs font-semibold bg-white hover:bg-slate-100 text-slate-950 transition-all shadow-sm hover:scale-[1.01] active:scale-[0.99]"
    >
      Connect Wallet
    </button>
  );
}
