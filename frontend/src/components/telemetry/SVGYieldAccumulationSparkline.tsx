"use client";

import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * SVGYieldAccumulationSparkline: Staking Performance Curve with champagne gold curve.
 */
export function SVGYieldAccumulationSparkline() {
  const { isWalletConnected, txState } = useProtocolStore();
  const isClaiming = txState === "claiming";

  return (
    <div className="w-full p-6 gold-card space-y-4">
      <div className="flex items-center justify-between font-mono text-xs">
        <span className="text-[#A1A1AA] uppercase tracking-wider">Staking Performance</span>
        <span className="text-[#E7C873] font-semibold">+14.80% APY</span>
      </div>

      <div className="relative h-28 w-full overflow-hidden rounded-xl bg-[#111111] border border-[rgba(212,175,55,0.08)] p-2 flex items-center">
        <svg className="w-full h-full overflow-visible" viewBox="0 0 400 80" preserveAspectRatio="none">
          <defs>
            <linearGradient id="goldYieldGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stopColor="#D4AF37" stopOpacity="0.3" />
              <stop offset="100%" stopColor="#D4AF37" stopOpacity="0.0" />
            </linearGradient>
          </defs>

          {/* Area Fill */}
          <path
            d="M 0 70 Q 100 65, 200 45 T 400 10 L 400 80 L 0 80 Z"
            fill="url(#goldYieldGradient)"
          />

          {/* Sparkline Curve */}
          <path
            d="M 0 70 Q 100 65, 200 45 T 400 10"
            fill="none"
            stroke="#D4AF37"
            strokeWidth="2.5"
            strokeLinecap="round"
          />

          {/* Live Data Point Marker */}
          <circle cx="400" cy="10" r="4" fill="#F5E6B8" className="animate-pulse" />
        </svg>

        {isClaiming && (
          <div className="absolute inset-0 bg-[#D4AF37]/10 backdrop-blur-xs flex items-center justify-center font-mono text-xs text-[#F5E6B8] font-bold">
            Harvesting Rewards to Wallet
          </div>
        )}
      </div>

      <div className="flex items-center justify-between text-[10px] font-mono text-[#A1A1AA]">
        <span>Block #5,821,800</span>
        <span>Block #5,821,904 (Current)</span>
      </div>
    </div>
  );
}
