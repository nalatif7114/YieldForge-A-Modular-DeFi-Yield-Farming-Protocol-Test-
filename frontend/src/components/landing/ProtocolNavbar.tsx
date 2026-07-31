"use client";

import Link from "next/link";
import { Activity } from "lucide-react";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { ConnectButton } from "@/components/ConnectButton";

const NAV_LINKS = [
  { label: "Protocol", href: "/" },
  { label: "Telemetry", href: "/dashboard" },
  { label: "Validators", href: "#" },
  { label: "Rewards", href: "#" },
  { label: "Docs", href: "#" },
];

export function ProtocolNavbar() {
  const { isProcessing } = useConsensusEngine();

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 h-14 px-4 sm:px-6 lg:px-8 flex items-center justify-between backdrop-blur-xl bg-[#050816]/70 border-b border-white/[0.06]">
      {/* ── Logo ── */}
      <Link href="/" className="flex items-center gap-2.5 shrink-0">
        <div className="w-7 h-7 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
          <span className="font-mono text-[10px] font-bold text-indigo-400">YF</span>
        </div>
        <span className="text-sm font-semibold text-white tracking-tight hidden sm:block">
          YieldForge
        </span>
      </Link>

      {/* ── Center Nav Links ── */}
      <div className="hidden md:flex items-center gap-0.5">
        {NAV_LINKS.map((link) => (
          <Link
            key={link.label}
            href={link.href}
            className="px-3 py-1.5 text-[13px] text-slate-400 hover:text-white transition-colors duration-200 rounded-lg hover:bg-white/[0.04]"
          >
            {link.label}
          </Link>
        ))}
      </div>

      {/* ── Right: Telemetry Badges + Wallet ── */}
      <div className="flex items-center gap-3 shrink-0">
        <div className="hidden lg:flex items-center gap-2.5 text-xs font-mono">
          {/* TPS Badge */}
          <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/[0.03] border border-white/[0.06]">
            <Activity className="w-3 h-3 text-emerald-400" />
            <span className="text-slate-500">Telemetry:</span>
            <span className="text-white font-medium">
              {isProcessing ? "48.6" : "14.2"} TPS
            </span>
          </div>

          {/* Status Badge */}
          <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/[0.03] border border-white/[0.06]">
            <span
              className={`w-1.5 h-1.5 rounded-full ${
                isProcessing ? "bg-amber-400 animate-pulse" : "bg-emerald-400"
              }`}
            />
            <span className="text-slate-500">Status:</span>
            <span
              className={`font-medium ${
                isProcessing ? "text-amber-400" : "text-emerald-400"
              }`}
            >
              {isProcessing ? "Processing" : "Active"}
            </span>
          </div>
        </div>

        <ConnectButton />
      </div>
    </nav>
  );
}
