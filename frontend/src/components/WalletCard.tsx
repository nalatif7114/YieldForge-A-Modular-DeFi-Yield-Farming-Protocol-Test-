"use client";

import { useWallet } from "@/hooks/useWallet";

export function WalletCard() {
  const { account, isConnected } = useWallet();

  if (!isConnected || !account) {
    return (
      <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm">
        <div className="flex items-center justify-between">
          <span className="text-xs font-medium uppercase tracking-wider text-slate-400">Wallet Portfolio</span>
          <span className="text-xs text-slate-500">Offline</span>
        </div>
        <div className="mt-4 text-center py-6 border border-dashed border-slate-800 rounded-xl">
          <div className="w-12 h-12 rounded-full bg-slate-800/80 mx-auto flex items-center justify-center text-xl text-slate-400 mb-3">
            👛
          </div>
          <h4 className="text-sm font-semibold text-slate-300">Wallet Not Connected</h4>
          <p className="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
            Please connect your MetaMask wallet to view your account details and token balances.
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm">
      <div className="flex items-center justify-between">
        <span className="text-xs font-medium uppercase tracking-wider text-slate-400">Connected Wallet</span>
        <span className="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
          Active
        </span>
      </div>
      <div className="mt-4">
        <label className="text-[11px] font-medium uppercase text-slate-500 block mb-1">Public Address</label>
        <div className="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 flex items-center justify-between gap-2">
          <span className="font-mono text-xs text-emerald-300 break-all">{account}</span>
        </div>
      </div>
    </div>
  );
}
