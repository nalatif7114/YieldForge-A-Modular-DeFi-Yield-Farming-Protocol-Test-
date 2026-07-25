"use client";

import { useWallet } from "@/hooks/useWallet";
import { useTokenBalance } from "@/hooks/useTokenBalance";

export function BalanceCard() {
  const { account, isConnected } = useWallet();
  const { formattedBalance, isLoading, refetch } = useTokenBalance(account);

  if (isLoading) {
    return (
      <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm animate-pulse">
        <div className="h-4 w-28 bg-slate-800 rounded mb-4"></div>
        <div className="h-9 w-40 bg-slate-800 rounded mb-2"></div>
        <div className="h-3 w-20 bg-slate-800/60 rounded"></div>
      </div>
    );
  }

  return (
    <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm relative overflow-hidden group hover:border-emerald-500/40 transition-all">
      <div className="absolute -right-8 -top-8 w-28 h-28 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all pointer-events-none"></div>

      <div className="flex items-center justify-between">
        <span className="text-xs font-medium uppercase tracking-wider text-slate-400">Your YFT Balance</span>
        <button
          onClick={refetch}
          className="text-xs text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1"
          title="Refresh Balance"
        >
          <span>↻</span> Refresh
        </button>
      </div>

      <div className="mt-4">
        <div className="flex items-baseline gap-2">
          <span className="text-3xl font-extrabold text-white tracking-tight">
            {isConnected ? formattedBalance : "0.00"}
          </span>
          <span className="text-sm font-semibold text-emerald-400">YFT</span>
        </div>
        <p className="text-xs text-slate-500 mt-1">
          {isConnected ? "Tokens available in connected wallet" : "Connect wallet to view token balance"}
        </p>
      </div>
    </div>
  );
}
