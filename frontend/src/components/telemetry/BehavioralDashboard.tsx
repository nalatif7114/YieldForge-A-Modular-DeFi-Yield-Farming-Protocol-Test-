"use client";

import dynamic from "next/dynamic";
import { motion } from "framer-motion";
import { ArrowUpRight, ShieldCheck, Wallet, RefreshCw, Layers } from "lucide-react";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { useWallet } from "@/hooks/useWallet";
import { NetworkHeaderTelemetry } from "./NetworkHeaderTelemetry";
import { SVGTransactionStream } from "./SVGTransactionStream";
import { SVGYieldAccumulationSparkline } from "./SVGYieldAccumulationSparkline";
import { ConsensusWave } from "@/components/motion/consensus";

// Dynamically import 3D Gold Spatial Canvas
const GoldSpatialCanvas = dynamic(
  () =>
    import("@/components/3d/GoldConsensusCanvas").then(
      (mod) => mod.GoldConsensusCanvas
    ),
  { ssr: false }
);

export function BehavioralDashboard() {
  const { isConnected, connect } = useWallet();
  const { state, activeNodes, logHistory, isProcessing, submitTransaction } =
    useConsensusEngine();

  const handleStakeClick = () => {
    submitTransaction("staking");
  };

  const handleClaimClick = () => {
    submitTransaction("claiming");
  };

  return (
    <div className="min-h-screen bg-[#080808] text-[#F4F4F4] flex flex-col font-sans selection:bg-[#D4AF37]/30 selection:text-[#F5E6B8]">
      {/* ── Institutional Top Bar ── */}
      <NetworkHeaderTelemetry />

      {/* ── Main Dashboard Viewport ── */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-6 sm:px-10 lg:px-12 pt-28 pb-16 space-y-8">
        
        {/* ── Section 1: Portfolio Overview & Primary Action Controls ── */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-[rgba(212,175,55,0.08)] pb-6">
          <div className="space-y-1">
            <div className="flex items-center gap-2">
              <span className="text-[11px] font-mono uppercase tracking-widest text-[#E7C873]">
                Institutional Yield Platform
              </span>
              <span className="px-2.5 py-0.5 rounded-full text-[10px] font-mono bg-[#D4AF37]/10 text-[#E7C873] border border-[rgba(212,175,55,0.2)]">
                Active System
              </span>
            </div>
            <h1 className="text-3xl sm:text-4xl font-extrabold text-[#F4F4F4] tracking-tight">
              Portfolio Overview
            </h1>
            <p className="text-xs text-[#A1A1AA] max-w-xl font-normal leading-relaxed">
              Institutional asset management, staking positions, and automated yield performance.
            </p>
          </div>

          {/* Protocol Action Triggers */}
          <div className="flex items-center gap-3 shrink-0">
            <button
              onClick={handleStakeClick}
              disabled={isProcessing}
              className={`gold-button-primary px-5 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 cursor-pointer ${
                isProcessing ? "opacity-50 cursor-not-allowed" : ""
              }`}
            >
              <span>{isProcessing ? `Processing (${state})…` : "Stake Assets (YFT)"}</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </button>

            <button
              onClick={handleClaimClick}
              disabled={isProcessing}
              className={`px-5 py-2.5 rounded-xl text-xs font-semibold bg-[#161616] text-[#F5E6B8] border border-[rgba(212,175,55,0.2)] hover:border-[#D4AF37] transition-all cursor-pointer ${
                isProcessing ? "opacity-50 cursor-not-allowed" : ""
              }`}
            >
              <span>{isProcessing ? "Validating…" : "Harvest Yield (EMERALD)"}</span>
              <RefreshCw className="w-3.5 h-3.5 text-[#D4AF37]" />
            </button>
          </div>
        </div>

        {/* ── Disconnected Wallet Banner ── */}
        {!isConnected && (
          <div className="p-8 gold-card backdrop-blur-xl">
            <div className="max-w-xl space-y-3">
              <span className="px-3 py-1 text-[10px] font-mono rounded-full bg-[#D4AF37]/10 text-[#E7C873] border border-[rgba(212,175,55,0.2)] inline-block">
                Authentication Required
              </span>
              <h2 className="text-xl font-bold text-[#F4F4F4]">Connect Web3 Wallet</h2>
              <p className="text-xs text-[#A1A1AA] font-normal leading-relaxed">
                Connecting your wallet synchronizes RPC consensus node telemetry, unlocks live contract balances, and activates automated compounding monitors.
              </p>
              <button
                onClick={connect}
                className="mt-2 gold-button-primary px-5 py-2.5 rounded-xl text-xs font-semibold cursor-pointer"
              >
                Connect MetaMask Wallet
              </button>
            </div>
          </div>
        )}

        {/* ── Summary Metrics Bar (4 Key Metrics Cards) ── */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div className="gold-card p-5 space-y-1.5 hover:-translate-y-0.5 transition-all duration-200">
            <span className="text-[10px] font-mono uppercase tracking-wider text-[#A1A1AA]">
              Total Staked Value
            </span>
            <div className="text-2xl font-bold font-mono text-[#F4F4F4] gold-gradient-text">
              $248,500.00
            </div>
            <div className="text-[10px] font-mono text-[#A1A1AA]">12,450 YFT Position</div>
          </div>

          <div className="gold-card p-5 space-y-1.5 hover:-translate-y-0.5 transition-all duration-200">
            <span className="text-[10px] font-mono uppercase tracking-wider text-[#A1A1AA]">
              Earned Rewards
            </span>
            <div className="text-2xl font-bold font-mono text-[#F4F4F4] gold-gradient-text">
              $14,825.40
            </div>
            <div className="text-[10px] font-mono text-emerald-400">482.5 EMERALD Available</div>
          </div>

          <div className="gold-card p-5 space-y-1.5 hover:-translate-y-0.5 transition-all duration-200">
            <span className="text-[10px] font-mono uppercase tracking-wider text-[#A1A1AA]">
              Net APY Trajectory
            </span>
            <div className="text-2xl font-bold font-mono text-emerald-400">
              14.80%
            </div>
            <div className="text-[10px] font-mono text-[#A1A1AA]">Auto-compounded 15m</div>
          </div>

          <div className="gold-card p-5 space-y-1.5 hover:-translate-y-0.5 transition-all duration-200">
            <span className="text-[10px] font-mono uppercase tracking-wider text-[#A1A1AA]">
              Validator Health
            </span>
            <div className="text-2xl font-bold font-mono text-[#F5E6B8]">
              8 / 8 Active
            </div>
            <div className="text-[10px] font-mono text-[#E7C873]">BFT Supermajority</div>
          </div>
        </div>

        {/* ── Section 2: Strategy Lifecycle & Transaction Flow ── */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <ConsensusWave />
          <SVGTransactionStream />
          <SVGYieldAccumulationSparkline />
        </div>

        {/* ── Section 3: Validator Health & Recent Transactions ── */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          {/* Validator Health 3D Scene Card */}
          <div className="p-6 gold-card col-span-1 lg:col-span-2 space-y-4">
            <div className="flex items-center justify-between font-mono text-xs">
              <span className="text-[#A1A1AA] uppercase tracking-wider">Validator Health</span>
              <span className={activeNodes.length > 0 ? "text-[#E7C873] font-bold" : "text-emerald-400"}>
                {activeNodes.length > 0 ? `${activeNodes.length} Nodes Validating` : "8 Nodes Synchronized"}
              </span>
            </div>
            <div className="h-60 w-full rounded-xl bg-[#111111] border border-[rgba(212,175,55,0.08)] relative overflow-hidden">
              <GoldSpatialCanvas />
              <div className="absolute bottom-3 left-3 text-[10px] font-mono text-[#A1A1AA] flex items-center gap-3">
                <span>P2P Latency: 38ms - 44ms</span>
                <span className="text-[#E7C873]">Topology: 8-Node BFT Mesh</span>
              </div>
            </div>
          </div>

          {/* Recent Transactions Feed */}
          <div className="p-6 gold-card col-span-1 space-y-4">
            <div className="flex items-center justify-between font-mono text-xs">
              <span className="uppercase tracking-wider text-[#A1A1AA]">Recent Transactions</span>
              <span className="text-emerald-400 text-[10px] font-bold">LIVE</span>
            </div>
            <div className="h-60 overflow-y-auto space-y-2 font-mono text-[11px] pr-1 scrollbar-thin">
              {logHistory.length === 0 ? (
                <div className="text-[#A1A1AA] text-xs py-12 text-center">No transactions recorded.</div>
              ) : (
                logHistory.map((log, idx) => (
                  <div key={idx} className="p-3 rounded-lg bg-[#111111] border border-[rgba(212,175,55,0.06)] space-y-1">
                    <div className="flex items-center justify-between text-[10px]">
                      <span className="text-[#E7C873] font-bold">{log.state}</span>
                      <span className="text-[#A1A1AA]">
                        {new Date(log.timestamp).toLocaleTimeString()}
                      </span>
                    </div>
                    <p className="text-[#F4F4F4] text-[10px] leading-tight">{log.message}</p>
                  </div>
                ))
              )}
            </div>
          </div>

        </div>

      </main>

      {/* ── Footer ── */}
      <footer className="border-t border-[rgba(212,175,55,0.08)] py-6 text-center text-xs text-[#A1A1AA] font-mono bg-[#080808]">
        <p>YieldForge Institutional Platform — High-Efficiency Yield Infrastructure</p>
      </footer>
    </div>
  );
}
