"use client";

import { useState, useEffect } from "react";
import dynamic from "next/dynamic";
import Link from "next/link";
import { motion } from "framer-motion";
import { ArrowRight, ShieldCheck, Activity, Cpu, Layers } from "lucide-react";

// Dynamically import 3D Protocol Engine Canvas with SSR disabled
const ProtocolEngineCanvas = dynamic(
  () => import("../3d/Engine/ProtocolEngineCanvas").then((mod) => mod.ProtocolEngineCanvas),
  { ssr: false }
);

export function ScrollProtocolController() {
  const [activeStage, setActiveStage] = useState(1);

  const stages = [
    {
      num: "01",
      title: "Observe Protocol",
      headline: "The Protocol Never Sleeps.",
      subtext: "Continuous state execution and automated compounding infrastructure on Ethereum.",
      detail: "Live telemetry monitoring active contract operations in real time.",
    },
    {
      num: "02",
      title: "Deposit Capital",
      headline: "Capital Gateway.",
      subtext: "Token deposits enter the contract via gas-optimized ERC-20 transferFrom streams.",
      detail: "Checks-Effects-Interactions safety standard enforced on every transaction.",
    },
    {
      num: "03",
      title: "Consensus Validation",
      headline: "Validator Synchronization.",
      subtext: "Consensus nodes verify proof header integrity across distributed network states.",
      detail: "Proof verification synchronized on every block confirmation.",
    },
    {
      num: "04",
      title: "Yield Processing",
      headline: "Automated Compounding.",
      subtext: "Mechanical engine computes yield allocations per block without manual intervention.",
      detail: "State mathematical precision with custom Solidity 0.8+ error handling.",
    },
    {
      num: "05",
      title: "Asset Accumulation",
      headline: "Compounding Funnel.",
      subtext: "Accrued value spirals back into active liquidity pools to maximize yield growth.",
      detail: "Continuous asset velocity with zero protocol downtime.",
    },
    {
      num: "06",
      title: "Reward Distribution",
      headline: "Verified Value Output.",
      subtext: "Processed yield rewards become claimable directly back to user wallets.",
      detail: "Emerald indicators illuminate upon successful value distribution.",
    },
  ];

  return (
    <div className="relative bg-[#050816] text-white">
      {/* Fixed Full-Screen Viewport for Living 3D Protocol Engine */}
      <div className="fixed inset-0 z-0 pointer-events-auto">
        <ProtocolEngineCanvas stage={activeStage} />
        {/* Subtle Vignette & Atmospheric Fog */}
        <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-10"></div>
      </div>

      {/* Foreground Content Container */}
      <div className="relative z-20 pointer-events-none">
        
        {/* Top Floating Telemetry Bar */}
        <header className="sticky top-0 z-50 backdrop-blur-xl bg-[#050816]/70 border-b border-white/10 pointer-events-auto">
          <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 h-20 flex items-center justify-between">
            <Link href="/" className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-center text-lg">
                🌾
              </div>
              <div className="flex items-center gap-2">
                <span className="text-base font-bold text-white tracking-tight">YieldForge</span>
                <span className="px-2 py-0.5 text-[10px] font-mono rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                  Engine Active
                </span>
              </div>
            </Link>

            <div className="flex items-center gap-6 text-xs font-mono text-slate-400">
              <div className="hidden md:flex items-center gap-2">
                <Activity className="w-3.5 h-3.5 text-emerald-400 animate-pulse" />
                <span>TPS: <strong className="text-white">14.2</strong></span>
              </div>
              <div className="hidden md:flex items-center gap-2">
                <Cpu className="w-3.5 h-3.5 text-sky-400" />
                <span>Gas: <strong className="text-white">12 Gwei</strong></span>
              </div>
              <Link
                href="/dashboard"
                className="px-4 py-2 rounded-xl text-xs font-semibold bg-white text-slate-950 hover:bg-slate-100 transition-all shadow-sm"
              >
                Launch Dashboard
              </Link>
            </div>
          </div>
        </header>

        {/* Scrollable Stage Journey Sections */}
        <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
          {stages.map((stageItem, index) => {
            const stageNum = index + 1;
            return (
              <section
                key={stageItem.num}
                className="min-h-[100vh] flex flex-col justify-center py-20 pointer-events-auto"
                onMouseEnter={() => setActiveStage(stageNum)}
              >
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6 }}
                  viewport={{ amount: 0.5 }}
                  className="max-w-xl p-8 sm:p-10 rounded-3xl bg-[#050816]/85 border border-white/10 backdrop-blur-2xl shadow-2xl space-y-6"
                >
                  {/* Stage Index Header */}
                  <div className="flex items-center justify-between border-b border-white/10 pb-4">
                    <div className="flex items-center gap-2">
                      <span className="text-xs font-mono font-bold text-indigo-400">
                        {stageItem.num}
                      </span>
                      <span className="text-slate-600">/</span>
                      <span className="text-xs font-mono uppercase tracking-widest text-slate-400">
                        {stageItem.title}
                      </span>
                    </div>
                    {stageNum === activeStage && (
                      <span className="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-mono">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Observing
                      </span>
                    )}
                  </div>

                  {/* Stage Headline & Copy */}
                  <div className="space-y-3">
                    <h2 className="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                      {stageItem.headline}
                    </h2>
                    <p className="text-sm text-slate-300 font-normal leading-relaxed">
                      {stageItem.subtext}
                    </p>
                  </div>

                  {/* Protocol Detail Badge */}
                  <div className="p-3.5 rounded-xl bg-white/[0.03] border border-white/10 text-xs font-mono text-slate-400 flex items-center gap-2">
                    <Layers className="w-4 h-4 text-sky-400 shrink-0" />
                    <span>{stageItem.detail}</span>
                  </div>

                  {/* Stage Action Link on Final Stage */}
                  {stageNum === 6 && (
                    <div className="pt-2">
                      <Link
                        href="/dashboard"
                        className="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-xs bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20 transition-all"
                      >
                        <span>Open Dashboard & Stake YFT</span>
                        <ArrowRight className="w-4 h-4" />
                      </Link>
                    </div>
                  )}
                </motion.div>
              </section>
            );
          })}
        </div>

        {/* Protocol Footer */}
        <footer className="border-t border-white/10 py-12 bg-[#050816]/90 backdrop-blur-xl text-center text-xs text-slate-500 pointer-events-auto">
          <p className="font-mono">YieldForge Protocol Telemetry Engine — Ethereum Sepolia Testnet</p>
        </footer>

      </div>
    </div>
  );
}
