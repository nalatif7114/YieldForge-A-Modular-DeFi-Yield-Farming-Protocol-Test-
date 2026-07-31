"use client";

import dynamic from "next/dynamic";
import Link from "next/link";
import { motion } from "framer-motion";
import { ArrowUpRight, BookOpen, ShieldCheck, Code2, Lock } from "lucide-react";

const GoldConsensusCanvas = dynamic(
  () =>
    import("@/components/3d/GoldConsensusCanvas").then(
      (mod) => mod.GoldConsensusCanvas
    ),
  { ssr: false }
);

export function InstitutionalHero() {
  return (
    <section id="hero" className="relative min-h-screen pt-28 pb-16 flex flex-col justify-center px-6 sm:px-10 lg:px-16 overflow-hidden">
      <div className="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        {/* ── Left Column: Institutional Narrative & CTAs (7 Cols) ── */}
        <motion.div
          initial={{ opacity: 0, y: 16 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, ease: "easeOut" }}
          className="lg:col-span-7 space-y-6"
        >
          {/* Badge */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#161616] border border-[rgba(212,175,55,0.15)] text-[11px] font-mono text-[#E7C873]">
            <span className="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse" />
            <span className="tracking-widest uppercase">INSTITUTIONAL YIELD INFRASTRUCTURE</span>
          </div>

          {/* Headline */}
          <h1 className="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold tracking-tight text-[#F4F4F4] leading-[1.06]">
            The Protocol
            <br />
            <span className="gold-gradient-text">Never Sleeps.</span>
          </h1>

          {/* Subtitle */}
          <p className="text-base sm:text-lg text-[#A1A1AA] leading-relaxed max-w-xl font-normal">
            YieldForge is a modular on-chain infrastructure for automated yield generation,
            validator coordination, and institutional-grade capital efficiency.
          </p>

          {/* Buttons */}
          <div className="pt-2 flex flex-wrap items-center gap-4">
            <Link
              href="/dashboard"
              className="gold-button-primary px-6 py-3 rounded-xl text-sm font-semibold flex items-center gap-2"
            >
              <span>Launch App</span>
              <ArrowUpRight className="w-4 h-4" />
            </Link>

            <a
              href="#documentation"
              className="px-6 py-3 rounded-xl text-sm font-medium text-[#F4F4F4] bg-[#161616] border border-[rgba(212,175,55,0.12)] hover:border-[rgba(212,175,55,0.3)] transition-colors flex items-center gap-2"
            >
              <BookOpen className="w-4 h-4 text-[#E7C873]" />
              <span>Read Documentation</span>
            </a>
          </div>

          {/* Trust Indicators */}
          <div className="pt-6 border-t border-[rgba(212,175,55,0.08)] flex flex-wrap items-center gap-6 text-xs font-mono text-[#A1A1AA]">
            <div className="flex items-center gap-2">
              <Code2 className="w-4 h-4 text-[#D4AF37]" />
              <span>Open Source</span>
            </div>
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-[#D4AF37]" />
              <span>Auditable</span>
            </div>
            <div className="flex items-center gap-2">
              <Lock className="w-4 h-4 text-[#D4AF37]" />
              <span>Non-Custodial</span>
            </div>
          </div>
        </motion.div>

        {/* ── Right Column: 3D Gold Institutional Visualization (5 Cols) ── */}
        <motion.div
          initial={{ opacity: 0, scale: 0.96 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.9, ease: "easeOut", delay: 0.2 }}
          className="lg:col-span-5 relative h-[420px] lg:h-[520px] rounded-[24px] bg-[#111111]/80 border border-[rgba(212,175,55,0.1)] p-2 overflow-hidden shadow-2xl"
        >
          <GoldConsensusCanvas className="w-full h-full" />
        </motion.div>

      </div>
    </section>
  );
}
