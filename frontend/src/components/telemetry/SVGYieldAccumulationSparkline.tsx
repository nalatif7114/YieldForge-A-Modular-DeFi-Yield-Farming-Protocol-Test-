"use client";

import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * SVGYieldAccumulationSparkline: Crisp vector curve plotting block-by-block yield growth.
 * Cloudflare Radar / Vercel Analytics inspired visual sparkline.
 */
export function SVGYieldAccumulationSparkline() {
  const { isWalletConnected, txState } = useProtocolStore();
  const isClaiming = txState === "claiming";

  return (
    <div className="w-full p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
      <div className="flex items-center justify-between font-mono text-xs">
        <span className="text-slate-400 uppercase tracking-wider">Yield Accumulation Trajectory</span>
        <span className="text-emerald-400 font-semibold">+12.4% APY</span>
      </div>

      <div className="relative h-28 w-full overflow-hidden rounded-xl bg-slate-950/80 border border-white/5 p-2 flex items-center">
        <svg className="w-full h-full overflow-visible" viewBox="0 0 400 80" preserveAspectRatio="none">
          <defs>
            <linearGradient id="yieldGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stopColor="#22c55e" stopOpacity="0.3" />
              <stop offset="100%" stopColor="#22c55e" stopOpacity="0.0" />
            </linearGradient>
          </defs>

          {/* Area Fill */}
          <path
            d="M 0 70 Q 100 65, 200 45 T 400 10 L 400 80 L 0 80 Z"
            fill="url(#yieldGradient)"
          />

          {/* Sparkline Curve */}
          <path
            d="M 0 70 Q 100 65, 200 45 T 400 10"
            fill="none"
            stroke="#22c55e"
            strokeWidth="2.5"
            strokeLinecap="round"
          />

          {/* Live Data Point Marker */}
          <circle cx="400" cy="10" r="4" fill="#22c55e" className="animate-pulse" />
        </svg>

        {isClaiming && (
          <div className="absolute inset-0 bg-emerald-500/10 backdrop-blur-xs flex items-center justify-center font-mono text-xs text-emerald-400 font-bold">
            Value Extracted to Wallet
          </div>
        )}
      </div>

      <div className="flex items-center justify-between text-[10px] font-mono text-slate-500">
        <span>Block #5,821,800</span>
        <span>Block #5,821,904 (Current)</span>
      </div>
    </div>
  );
}
