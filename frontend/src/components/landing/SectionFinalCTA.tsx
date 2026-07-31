"use client";

import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { ConnectButton } from "@/components/ConnectButton";

export function SectionFinalCTA() {
  return (
    <section className="py-28 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#0B0B0B] relative overflow-hidden">
      {/* Background Soft Gold Ambient Glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-[#D4AF37]/5 blur-[120px] pointer-events-none rounded-full" />

      <div className="max-w-4xl mx-auto text-center space-y-8 relative z-10">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#161616] border border-[rgba(212,175,55,0.15)] text-[11px] font-mono text-[#E7C873]">
          <span className="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse" />
          <span className="uppercase tracking-widest">DEPLOY CAPITAL NOW</span>
        </div>

        <h2 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#F4F4F4] tracking-tight leading-[1.1]">
          Ready to Put Capital
          <br />
          <span className="gold-gradient-text">to Work?</span>
        </h2>

        <p className="text-base sm:text-lg text-[#A1A1AA] leading-relaxed max-w-xl mx-auto font-normal">
          Deploy institutional liquidity into automated, risk-managed yield strategies with on-chain validator consensus.
        </p>

        <div className="pt-4 flex flex-wrap items-center justify-center gap-4">
          <Link
            href="/dashboard"
            className="gold-button-primary px-8 py-4 rounded-xl text-base font-bold flex items-center gap-2.5 shadow-[0_4px_30px_rgba(212,175,55,0.3)] hover:shadow-[0_6px_40px_rgba(212,175,55,0.5)] transition-all"
          >
            <span>Launch YieldForge</span>
            <ArrowUpRight className="w-5 h-5" />
          </Link>

          <ConnectButton />
        </div>
      </div>
    </section>
  );
}
