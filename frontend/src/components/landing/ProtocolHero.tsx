"use client";

import dynamic from "next/dynamic";
import Link from "next/link";
import { motion } from "framer-motion";
import { ArrowUpRight, BarChart3 } from "lucide-react";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { MagneticButton } from "@/components/ui/MagneticButton";

// Dynamically import ConsensusCanvas with SSR disabled
const ConsensusCanvas = dynamic(
  () =>
    import("@/components/3d/ConsensusCanvas").then((mod) => mod.ConsensusCanvas),
  { ssr: false }
);

export function ProtocolHero() {
  const { submitTransaction, isProcessing } = useConsensusEngine();

  return (
    <section className="flex-1 flex flex-col lg:flex-row items-stretch pt-14">
      {/* ── Left Column: 2D DOM Typography & Protocol Action Controls (85% Priority) ── */}
      <div className="flex flex-col justify-center w-full lg:w-[46%] px-6 sm:px-8 lg:px-12 xl:px-16 py-10 lg:py-4">
        <motion.div
          initial={{ opacity: 0, y: 8 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, ease: "easeOut" }}
          className="max-w-lg"
        >
          {/* Status Indicator */}
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/[0.03] border border-white/[0.08] text-[11px] font-mono text-slate-400 mb-5">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
            <span className="tracking-wider uppercase text-slate-300">LIVE PROTOCOL</span>
          </div>

          {/* Precision Headline */}
          <h1 className="text-4xl sm:text-5xl lg:text-[3.25rem] xl:text-6xl font-bold text-white tracking-tight leading-[1.08] mb-4">
            The Protocol
            <br />
            <span className="bg-gradient-to-r from-indigo-400 via-purple-400 to-indigo-300 bg-clip-text text-transparent">
              Never Sleeps.
            </span>
          </h1>

          {/* Precision Subtitle */}
          <p className="text-sm sm:text-[15px] text-slate-400 leading-relaxed mb-7 max-w-md font-normal">
            Real-time validator consensus, automated yield execution, and protocol observability.
          </p>

          {/* Action Triggers */}
          <div className="flex flex-wrap items-center gap-3">
            <MagneticButton
              onClick={() => submitTransaction("staking")}
              disabled={isProcessing}
              variant="primary"
            >
              <span>{isProcessing ? "Processing…" : "Enter Control Room"}</span>
              <ArrowUpRight className="w-3.5 h-3.5" />
            </MagneticButton>

            <Link href="/dashboard">
              <MagneticButton variant="secondary">
                <BarChart3 className="w-3.5 h-3.5" />
                <span>View Analytics</span>
              </MagneticButton>
            </Link>
          </div>
        </motion.div>
      </div>

      {/* ── Right Column: Ambient Three.js Protocol Topology (15% Supporting Layer) ── */}
      <div className="w-full lg:w-[54%] min-h-[360px] lg:min-h-[420px] p-4 lg:p-6 flex items-center justify-center">
        <div className="w-full h-full rounded-[20px] overflow-hidden bg-[#030712]/60 border border-white/[0.08] relative group hover:border-white/[0.15] transition-all duration-200">
          <ConsensusCanvas
            className="w-full h-full"
            showOverlay={false}
            variant="fullscreen"
          />
        </div>
      </div>
    </section>
  );
}
