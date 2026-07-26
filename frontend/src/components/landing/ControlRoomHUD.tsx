"use client";

import { motion } from "framer-motion";
import Link from "next/link";
import { ArrowUpRight, Activity, Cpu, Layers } from "lucide-react";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { ConnectButton } from "@/components/ConnectButton";
import { MagneticButton } from "@/components/ui/MagneticButton";
import { TextReveal } from "@/components/ui/TextReveal";
import { PerspectiveGlowBorder } from "@/components/ui/PerspectiveGlowBorder";
import { ConsensusWave } from "@/components/motion/consensus";

export function ControlRoomHUD() {
  const { scrollProgress, isWalletConnected } = useProtocolStore();
  const { state, submitTransaction, isProcessing } = useConsensusEngine();

  const stages = [
    "01 OBSERVE",
    "02 DEPOSIT",
    "03 VALIDATE",
    "04 YIELD",
    "05 COMPOUND",
    "06 REWARD",
  ];

  const currentStageIndex = Math.min(
    Math.floor(scrollProgress * stages.length),
    stages.length - 1
  );

  return (
    <div className="relative z-30 pointer-events-none text-white selection:bg-indigo-500/30 font-sans">
      
      {/* Top Header Control Bar */}
      <header className="fixed top-0 left-0 right-0 z-50 py-5 px-6 sm:px-10 flex items-center justify-between backdrop-blur-md bg-[#050816]/50 border-b border-white/[0.06] pointer-events-auto">
        <Link href="/" className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-lg bg-white/[0.05] border border-white/10 flex items-center justify-center font-mono text-xs font-bold text-indigo-400">
            YF
          </div>
          <div className="flex items-center gap-2">
            <span className="text-sm font-bold tracking-tight text-white">YieldForge</span>
            <span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/[0.04] text-slate-400 border border-white/10">
              Sepolia-11155111
            </span>
          </div>
        </Link>

        {/* System Telemetry & Wallet Connection */}
        <div className="flex items-center gap-6 text-xs font-mono text-slate-400">
          <div className="hidden lg:flex items-center gap-2">
            <Activity className="w-3.5 h-3.5 text-emerald-400 animate-pulse" />
            <span>Telemetry: <strong className="text-white">{isProcessing ? "48.6 TPS" : "14.2 TPS"}</strong></span>
          </div>
          <div className="hidden lg:flex items-center gap-2">
            <Cpu className="w-3.5 h-3.5 text-sky-400" />
            <span>Consensus State: <strong className={isProcessing ? "text-amber-400 animate-pulse font-bold" : "text-emerald-400 font-bold"}>{state}</strong></span>
          </div>
          <ConnectButton />
        </div>
      </header>

      {/* Main Vision Pro Minimalist Copy (Section 1) */}
      <section className="min-h-screen flex flex-col justify-center px-6 sm:px-12 lg:px-20 max-w-4xl pt-24 pointer-events-auto">
        <motion.div
          initial={{ opacity: 0, y: 15 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8 }}
          className="space-y-6"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/[0.04] border border-white/10 text-[11px] font-mono text-slate-300">
            <span className="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            <span>Project Helios — Consensus Engine Active</span>
          </div>

          {/* Bold Headline with Handcrafted TextReveal */}
          <h1 className="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.05]">
            <TextReveal text="The Protocol Never Sleeps." />
          </h1>

          {/* One Precision Sentence */}
          <p className="text-base sm:text-lg text-slate-400 max-w-md font-normal leading-relaxed">
            Real-time observable distributed consensus network on Ethereum Sepolia.
          </p>

          <div className="pt-4 flex items-center gap-4">
            <MagneticButton onClick={() => submitTransaction("staking")} disabled={isProcessing} variant="primary">
              <span>{isProcessing ? `Processing (${state})...` : "Trigger Consensus Cycle"}</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </MagneticButton>

            <Link href="/dashboard">
              <MagneticButton variant="secondary">
                <span>Launch Telemetry Room</span>
              </MagneticButton>
            </Link>
          </div>
        </motion.div>
      </section>

      {/* Scroll Lifecycle Sections with PerspectiveGlowBorder */}
      <div className="px-6 sm:px-12 lg:px-20 max-w-7xl mx-auto space-y-[80vh] pb-[30vh]">
        
        {/* Section 2: Deposit */}
        <PerspectiveGlowBorder className="pointer-events-auto max-w-lg p-8 space-y-4">
          <span className="text-[11px] font-mono uppercase tracking-widest text-sky-400">02 // Ingestion Endpoint</span>
          <h2 className="text-2xl font-bold text-white">Mempool Transaction Ingestion</h2>
          <p className="text-xs text-slate-400 leading-relaxed font-normal">
            Payload capsules enter primary validator endpoints via gas-optimized protocol transaction streams.
          </p>
          <div className="p-3 rounded-xl bg-white/[0.03] border border-white/10 text-xs font-mono text-slate-400 flex items-center gap-2">
            <Layers className="w-4 h-4 text-sky-400" />
            <span>Zero-Knowledge Proof Verification</span>
          </div>
        </PerspectiveGlowBorder>

        {/* Section 3: Validation & Consensus Wave */}
        <PerspectiveGlowBorder className="pointer-events-auto max-w-lg p-8 space-y-4 ml-auto">
          <span className="text-[11px] font-mono uppercase tracking-widest text-indigo-400">03 // Consensus Mesh</span>
          <h2 className="text-2xl font-bold text-white">Validator Synchronization</h2>
          <p className="text-xs text-slate-400 leading-relaxed font-normal">
            Validator nodes exchange network state proofs to achieve BFT supermajority finality.
          </p>
          <ConsensusWave />
        </PerspectiveGlowBorder>

        {/* Section 4: Yield Engine */}
        <PerspectiveGlowBorder className="pointer-events-auto max-w-lg p-8 space-y-4">
          <span className="text-[11px] font-mono uppercase tracking-widest text-violet-400">04 // State Commit</span>
          <h2 className="text-2xl font-bold text-white">State Root Commitment</h2>
          <p className="text-xs text-slate-400 leading-relaxed font-normal">
            Merkle tree state roots update and finalize state changes directly into vault storage.
          </p>
        </PerspectiveGlowBorder>

        {/* Section 5: Rewards */}
        <PerspectiveGlowBorder className="pointer-events-auto max-w-lg p-8 space-y-4 ml-auto">
          <span className="text-[11px] font-mono uppercase tracking-widest text-emerald-400">05 // State Finality</span>
          <h2 className="text-2xl font-bold text-white">Transaction Completion</h2>
          <p className="text-xs text-slate-400 leading-relaxed font-normal">
            Distributed consensus cycle completes and returns validator nodes to standby monitoring.
          </p>
          <Link href="/dashboard">
            <MagneticButton variant="emerald">
              <span>Open Staking App</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </MagneticButton>
          </Link>
        </PerspectiveGlowBorder>

      </div>

      {/* Bottom Fixed Stage Lifecycle Tracker */}
      <div className="fixed bottom-6 left-6 right-6 z-40 flex items-center justify-between pointer-events-auto max-w-7xl mx-auto backdrop-blur-xl bg-[#050816]/70 p-3.5 rounded-2xl border border-white/10">
        <div className="flex items-center gap-4 text-xs font-mono">
          <span className="text-slate-500">STAGE:</span>
          <span className="text-indigo-400 font-bold">{stages[currentStageIndex]}</span>
        </div>

        <div className="flex items-center gap-2">
          {stages.map((_, idx) => (
            <div
              key={idx}
              className={`h-1.5 rounded-full transition-all duration-300 ${
                idx === currentStageIndex ? "w-6 bg-indigo-500" : "w-1.5 bg-slate-700"
              }`}
            ></div>
          ))}
        </div>
      </div>

    </div>
  );
}
