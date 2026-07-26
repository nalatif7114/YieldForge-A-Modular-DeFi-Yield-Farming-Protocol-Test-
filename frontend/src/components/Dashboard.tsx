"use client";

import { useWallet } from "@/hooks/useWallet";
import { useProtocolStore } from "@/store/useProtocolStore";
import { Navbar } from "./Navbar";
import { NetworkCard } from "./NetworkCard";
import { WalletCard } from "./WalletCard";
import { BalanceCard } from "./BalanceCard";
import { SupplyCard } from "./SupplyCard";

export function Dashboard() {
  const { isConnected, isCorrectNetwork, connect } = useWallet();
  const { triggerStakingEvent, triggerClaimEvent, txState } = useProtocolStore();

  return (
    <div className="min-h-screen bg-[#050816] text-slate-100 flex flex-col font-sans selection:bg-indigo-500/30 selection:text-indigo-300">
      {/* Top Navigation */}
      <Navbar />

      {/* Main Container */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-6 sm:px-8 lg:px-12 py-10 space-y-8">
        
        {/* Welcome & Overview Header */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-6">
          <div>
            <h2 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
              Protocol Telemetry & Staking Dashboard
            </h2>
            <p className="text-xs text-slate-400 mt-1 font-normal">
              Observe living smart contract state, wallet allocations, and reactive 3D telemetry.
            </p>
          </div>
          
          {/* Reactive Protocol Action Triggers */}
          <div className="flex items-center gap-3">
            <button
              onClick={triggerStakingEvent}
              disabled={txState !== "idle"}
              className="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white shadow-md shadow-indigo-600/20 transition-all"
            >
              {txState === "staking" ? "Staking in Progress..." : "Simulate Stake (YFT)"}
            </button>

            <button
              onClick={triggerClaimEvent}
              disabled={txState !== "idle"}
              className="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white shadow-md shadow-emerald-600/20 transition-all"
            >
              {txState === "claiming" ? "Claiming Rewards..." : "Simulate Claim (Yield)"}
            </button>
          </div>
        </div>

        {/* Empty State Banner (When Disconnected) */}
        {!isConnected && (
          <div className="p-8 rounded-3xl bg-white/[0.02] border border-white/10 backdrop-blur-xl relative overflow-hidden">
            <div className="max-w-xl">
              <span className="px-3 py-1 text-[11px] font-mono rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 inline-block mb-3">
                Web3 Identity Required
              </span>
              <h3 className="text-xl font-bold text-white">Connect your Web3 Wallet</h3>
              <p className="text-xs text-slate-400 mt-2 leading-relaxed font-normal">
                Connecting your wallet wakes up the 3D Protocol Engine, accelerates consensus validator synchronization, and unlocks on-chain staking telemetry.
              </p>
              <button
                onClick={connect}
                className="mt-5 px-6 py-3 rounded-xl font-semibold text-xs bg-white text-slate-950 hover:bg-slate-100 transition-all shadow-sm"
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

        {/* Protocol State Telemetry Board */}
        <div className="p-6 rounded-2xl bg-white/[0.02] border border-white/10">
          <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400 mb-3">
            Real-Time State Telemetry
          </h4>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs text-slate-300">
            <div className="p-3.5 rounded-xl bg-slate-950/60 border border-white/10">
              <span className="text-slate-500 block text-[10px] font-mono uppercase">Engine Status</span>
              <span className="font-semibold text-emerald-400">
                {isConnected ? (txState !== "idle" ? `Active (${txState})` : "Synchronized") : "Dormant (Connect Wallet)"}
              </span>
            </div>
            <div className="p-3.5 rounded-xl bg-slate-950/60 border border-white/10">
              <span className="text-slate-500 block text-[10px] font-mono uppercase">Input Gateway</span>
              <span className="font-semibold text-sky-400">
                {txState === "staking" ? "Accelerated Packet Stream" : (isConnected ? "Active Flow" : "Low Velocity")}
              </span>
            </div>
            <div className="p-3.5 rounded-xl bg-slate-950/60 border border-white/10">
              <span className="text-slate-500 block text-[10px] font-mono uppercase">Consensus Mesh</span>
              <span className="font-semibold text-indigo-400">
                {isConnected ? "Synchronized @ 2.5 Hz" : "Standby Mode"}
              </span>
            </div>
            <div className="p-3.5 rounded-xl bg-slate-950/60 border border-white/10">
              <span className="text-slate-500 block text-[10px] font-mono uppercase">Reward Conduit</span>
              <span className="font-semibold text-emerald-400">
                {txState === "claiming" ? "Emerald Distribution Active" : "Standby Beam"}
              </span>
            </div>
          </div>
        </div>

      </main>

      <footer className="border-t border-white/10 py-6 text-center text-xs text-slate-500 font-mono">
        <p>YieldForge Reactive 3D Protocol Telemetry — Sepolia Testnet</p>
      </footer>
    </div>
  );
}
