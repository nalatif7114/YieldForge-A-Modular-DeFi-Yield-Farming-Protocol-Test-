"use client";

import dynamic from "next/dynamic";
import Link from "next/link";
import { motion } from "framer-motion";
import { ArrowRight, ShieldCheck, Zap, Layers } from "lucide-react";

// Dynamically import ConsensusCanvas with SSR disabled
const ConsensusCanvas = dynamic(
  () => import("../3d/ConsensusCanvas").then((mod) => mod.ConsensusCanvas),
  { ssr: false }
);

export function HeroSection() {
  const steps = [
    { num: "01", label: "Wallet" },
    { num: "02", label: "Token" },
    { num: "03", label: "Stake" },
    { num: "04", label: "Validator" },
    { num: "05", label: "Reward" },
  ];

  return (
    <section className="relative min-h-[92vh] flex items-center justify-center pt-28 pb-20 overflow-hidden bg-[#030712]">
      {/* Subtle Violet Radial Background Blur */}
      <div className="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-violet-600/10 rounded-full blur-[160px] pointer-events-none"></div>

      <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full grid grid-cols-1 lg:grid-cols-12 gap-16 items-center relative z-10">
        
        {/* Left Column: Short, Impactful Typography */}
        <motion.div
          initial={{ opacity: 0, y: 15 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
          className="lg:col-span-6 space-y-10"
        >
          {/* Version Badge */}
          <div className="inline-flex items-center gap-2.5 px-3 py-1 rounded-full bg-white/[0.04] border border-white/10 text-slate-300 text-xs font-mono backdrop-blur-md">
            <span className="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></span>
            <span>YieldForge Engine v1.0</span>
            <span className="text-slate-600">|</span>
            <span className="text-slate-400">Sepolia Active</span>
          </div>

          {/* Short, High-Impact Headline */}
          <div className="space-y-4">
            <h1 className="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.05]">
              Visualize <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-sky-300 to-emerald-300">
                Protocol Yield.
              </span>
            </h1>
            <p className="text-base sm:text-lg text-slate-400 max-w-md font-normal leading-relaxed">
              Real-time procedural visual diagnostics for Ethereum Sepolia yield farming infrastructure.
            </p>
          </div>

          {/* Minimalist Protocol Lifecycle Track */}
          <div className="space-y-3 pt-2">
            <div className="text-[11px] font-mono uppercase tracking-widest text-slate-500">
              Transaction Pipeline
            </div>
            <div className="flex items-center justify-between p-3.5 rounded-xl bg-white/[0.02] border border-white/10 backdrop-blur-md">
              {steps.map((step, index) => (
                <div key={step.label} className="flex items-center gap-2">
                  <div className="flex items-center gap-1.5">
                    <span className="text-[10px] font-mono text-violet-400">{step.num}</span>
                    <span className="text-xs font-medium text-slate-200">{step.label}</span>
                  </div>
                  {index < steps.length - 1 && (
                    <span className="text-slate-700 text-xs font-mono ml-1">→</span>
                  )}
                </div>
              ))}
            </div>
          </div>

          {/* Action CTAs */}
          <div className="flex items-center gap-4 pt-2">
            <Link
              href="/dashboard"
              className="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl font-medium text-sm bg-white hover:bg-slate-100 text-slate-950 transition-all shadow-lg shadow-white/10 hover:scale-[1.01] active:scale-[0.99]"
            >
              <span>Launch Dashboard</span>
              <ArrowRight className="w-4 h-4" />
            </Link>

            <a
              href="#architecture"
              className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-medium text-sm bg-white/[0.03] hover:bg-white/[0.06] text-slate-300 border border-white/10 hover:border-white/20 transition-all"
            >
              <span>System Specs</span>
            </a>
          </div>

          {/* Engineering Indicators */}
          <div className="grid grid-cols-3 gap-6 pt-6 border-t border-white/10 text-xs text-slate-400 font-mono">
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-violet-400" />
              <span>Audited Logic</span>
            </div>
            <div className="flex items-center gap-2">
              <Zap className="w-4 h-4 text-sky-400" />
              <span>Gas Optimized</span>
            </div>
            <div className="flex items-center gap-2">
              <Layers className="w-4 h-4 text-emerald-400" />
              <span>Pausable</span>
            </div>
          </div>
        </motion.div>

        {/* Right Column: Hero Canvas (ConsensusCanvas) */}
        <motion.div
          initial={{ opacity: 0, scale: 0.98 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.7, delay: 0.15 }}
          className="lg:col-span-6 h-[500px] sm:h-[580px] relative rounded-3xl overflow-hidden border border-white/10 bg-slate-950/60 shadow-2xl group"
        >
          {/* Subtle Vignette Overlay */}
          <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-10"></div>

          {/* Hero Canvas displaying ConsensusCanvas */}
          <ConsensusCanvas className="w-full h-full" />
        </motion.div>

      </div>
    </section>
  );
}
