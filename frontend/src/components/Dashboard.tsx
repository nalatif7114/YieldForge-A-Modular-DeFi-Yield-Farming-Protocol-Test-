"use client";

import { useWallet } from "@/hooks/useWallet";
import { Navbar } from "./Navbar";
import { NetworkCard } from "./NetworkCard";
import { WalletCard } from "./WalletCard";
import { BalanceCard } from "./BalanceCard";
import { SupplyCard } from "./SupplyCard";

export function Dashboard() {
  const { isConnected, isCorrectNetwork, connect } = useWallet();

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-emerald-500/30 selection:text-emerald-300">
      {/* Top Navigation */}
      <Navbar />

      {/* Main Container */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        {/* Welcome & Overview Header */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-6">
          <div>
            <h2 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
              DeFi Simulation Dashboard
            </h2>
            <p className="text-sm text-slate-400 mt-1">
              Phase 3 Overview: Inspect on-chain token supply, connected wallet details, and testnet balances.
            </p>
          </div>
          <div className="flex items-center gap-2">
            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-medium text-slate-300">
              <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
              YieldForge Token (YFT)
            </span>
          </div>
        </div>

        {/* Empty State Banner (When Disconnected) */}
        {!isConnected && (
          <div className="p-8 rounded-3xl bg-gradient-to-r from-emerald-950/40 via-slate-900/60 to-slate-900/60 border border-emerald-500/20 shadow-xl relative overflow-hidden">
            <div className="max-w-xl">
              <span className="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 inline-block mb-3">
                Web3 Wallet Required
              </span>
              <h3 className="text-xl font-bold text-white">Connect your MetaMask Wallet</h3>
              <p className="text-xs text-slate-400 mt-2 leading-relaxed">
                Connect your browser wallet to view your YFT token balances, verify network parameters, and prepare for upcoming Yield Farming simulation features.
              </p>
              <button
                onClick={connect}
                className="mt-5 px-6 py-3 rounded-xl font-semibold text-xs bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all"
              >
                Connect MetaMask Wallet
              </button>
            </div>
          </div>
        )}

        {/* Network & Wallet Metrics Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <NetworkCard />
          <BalanceCard />
          <SupplyCard />
          <WalletCard />
        </div>

        {/* System Info Banner */}
        <div className="p-6 rounded-2xl bg-slate-900/40 border border-slate-800/80">
          <h4 className="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
            Phase 3 Protocol Information
          </h4>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-400">
            <div className="p-3 rounded-xl bg-slate-950/60 border border-slate-800/60">
              <span className="text-slate-500 block text-[10px]">Contract Standard</span>
              <span className="font-semibold text-slate-200">ERC-20 + Pausable + Burnable</span>
            </div>
            <div className="p-3 rounded-xl bg-slate-950/60 border border-slate-800/60">
              <span className="text-slate-500 block text-[10px]">Client Provider</span>
              <span className="font-semibold text-slate-200">Viem + React Hooks</span>
            </div>
            <div className="p-3 rounded-xl bg-slate-950/60 border border-slate-800/60">
              <span className="text-slate-500 block text-[10px]">Network Target</span>
              <span className="font-semibold text-slate-200">{isCorrectNetwork ? "Aligned with Configured Network" : "Network Switch Required"}</span>
            </div>
          </div>
        </div>

      </main>

      {/* Simple Footer */}
      <footer className="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <p>YieldForge DeFi Learning Simulation Protocol — Phase 3</p>
      </footer>
    </div>
  );
}
