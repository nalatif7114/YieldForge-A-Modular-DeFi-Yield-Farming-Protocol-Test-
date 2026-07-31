"use client";

import { useEffect, useState, useRef } from "react";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/* ═══════════════════════════════════════════════════════════════
   Glassmorphism Card Shell — 20px Radius, 150–200ms Micro Interactions
   ═══════════════════════════════════════════════════════════════ */
function MetricCard({
  children,
  className = "",
}: {
  children: React.ReactNode;
  className?: string;
}) {
  return (
    <div
      className={`rounded-[20px] bg-white/[0.03] border border-white/[0.08] backdrop-blur-sm p-4 sm:p-5 hover:-translate-y-0.5 hover:border-white/[0.15] transition-all duration-200 ${className}`}
    >
      {children}
    </div>
  );
}

/* ═══════════════════════════════════════════════════════════════
   SVG Circular Health Ring
   ═══════════════════════════════════════════════════════════════ */
function HealthRing({ value }: { value: number }) {
  const size = 52;
  const sw = 3;
  const r = (size - sw) / 2;
  const c = 2 * Math.PI * r;
  const offset = c - (value / 100) * c;

  return (
    <svg
      width={size}
      height={size}
      viewBox={`0 0 ${size} ${size}`}
      className="shrink-0"
    >
      {/* Track */}
      <circle
        cx={size / 2}
        cy={size / 2}
        r={r}
        fill="none"
        stroke="rgba(255,255,255,0.06)"
        strokeWidth={sw}
      />
      {/* Progress Arc */}
      <circle
        cx={size / 2}
        cy={size / 2}
        r={r}
        fill="none"
        stroke="#22c55e"
        strokeWidth={sw}
        strokeDasharray={c}
        strokeDashoffset={offset}
        strokeLinecap="round"
        transform={`rotate(-90 ${size / 2} ${size / 2})`}
        className="transition-all duration-1000"
      />
      {/* Center Label */}
      <text
        x={size / 2}
        y={size / 2}
        textAnchor="middle"
        dominantBaseline="central"
        fill="white"
        style={{
          fontSize: "12px",
          fontWeight: 700,
          fontFamily: "var(--font-geist-mono)",
        }}
      >
        {value}%
      </text>
    </svg>
  );
}

/* ═══════════════════════════════════════════════════════════════
   Mini Sparkline
   ═══════════════════════════════════════════════════════════════ */
function Sparkline({
  data,
  color = "#6366f1",
}: {
  data: number[];
  color?: string;
}) {
  const w = 80;
  const h = 28;
  const max = Math.max(...data);
  const min = Math.min(...data);
  const range = max - min || 1;

  const points = data
    .map((v, i) => {
      const x = (i / (data.length - 1)) * w;
      const y = h - ((v - min) / range) * (h - 4) - 2;
      return `${x},${y}`;
    })
    .join(" ");

  return (
    <svg width={w} height={h} className="opacity-50 shrink-0">
      <polyline
        points={points}
        fill="none"
        stroke={color}
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}

/* ═══════════════════════════════════════════════════════════════
   Protocol Metrics Row — 5 Event-Driven Dashboard Cards
   ═══════════════════════════════════════════════════════════════ */
export function ProtocolMetricsRow() {
  const { state, activeNodes } = useConsensusEngine();
  const [txCount, setTxCount] = useState(128_542);
  const prevStateRef = useRef(state);

  // Increment transaction count when a consensus cycle completes
  useEffect(() => {
    if (prevStateRef.current !== "COMPLETE" && state === "COMPLETE") {
      setTxCount((prev) => prev + 1);
    }
    prevStateRef.current = state;
  }, [state]);

  const sparklineData = [12, 15, 14, 18, 22, 19, 24, 28, 25, 30, 27, 32];

  // Map 8 validator nodes to live status driven by ConsensusEngine
  const validatorDots = Array.from({ length: 8 }, (_, i) => {
    const nodeId = i + 1;
    if (activeNodes.includes(nodeId)) return "active";
    if (
      state === "CONSENSUS_REACHED" ||
      state === "STATE_COMMITTED"
    )
      return "synced";
    return "idle";
  });

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 px-4 sm:px-6 lg:px-8 xl:px-12 py-3">
      {/* ── Protocol Health ── */}
      <MetricCard>
        <div className="flex items-center justify-between mb-3">
          <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
            Protocol Health
          </span>
          <span className="flex items-center gap-1 text-[10px] font-mono text-emerald-400">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-400" />
            Healthy
          </span>
        </div>
        <div className="flex items-center gap-4">
          <HealthRing value={98.7} />
          <div className="space-y-1.5 text-[11px] font-mono">
            <div className="flex justify-between gap-6">
              <span className="text-slate-500">Uptime</span>
              <span className="text-white">99.92%</span>
            </div>
            <div className="flex justify-between gap-6">
              <span className="text-slate-500">Latency</span>
              <span className="text-white">120ms</span>
            </div>
            <div className="flex justify-between gap-6">
              <span className="text-slate-500">Finality</span>
              <span className="text-white">−12.4s</span>
            </div>
          </div>
        </div>
      </MetricCard>

      {/* ── Transactions (24H) ── */}
      <MetricCard>
        <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
          Transactions (24H)
        </span>
        <div className="mt-3 flex items-end justify-between">
          <div>
            <div className="text-2xl font-bold text-white font-mono tracking-tight">
              {txCount.toLocaleString()}
            </div>
            <span className="text-[10px] font-mono text-emerald-400">
              ▲ 18.24%
            </span>
          </div>
          <Sparkline data={sparklineData} />
        </div>
      </MetricCard>

      {/* ── Yield Generated (24H) ── */}
      <MetricCard>
        <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
          Yield Generated (24H)
        </span>
        <div className="mt-3 text-2xl font-bold text-white font-mono tracking-tight">
          $183,671.42
        </div>
        <div className="mt-1.5 space-y-0.5 text-[10px] font-mono text-slate-500">
          <div>Auto-compounded</div>
          <div>⟳ Every 15 minutes</div>
        </div>
      </MetricCard>

      {/* ── Active Pools ── */}
      <MetricCard>
        <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
          Active Pools
        </span>
        <div className="mt-3 text-2xl font-bold text-white font-mono tracking-tight">
          12
        </div>
        <div className="text-[10px] font-mono text-slate-500 mt-0.5 mb-2.5">
          Total Active Strategies
        </div>
        <div className="flex items-center">
          {[
            "bg-indigo-500",
            "bg-violet-500",
            "bg-sky-500",
            "bg-emerald-500",
            "bg-amber-500",
          ].map((bg, i) => (
            <div
              key={i}
              className={`w-5 h-5 rounded-full ${bg} opacity-60 border-2 border-[#050816] ${
                i > 0 ? "-ml-1.5" : ""
              }`}
            />
          ))}
          <span className="text-[10px] font-mono text-slate-500 ml-2">+7</span>
        </div>
      </MetricCard>

      {/* ── Network Status ── */}
      <MetricCard>
        <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
          Network Status
        </span>
        <div className="mt-3 grid grid-cols-4 gap-2.5 mb-3">
          {validatorDots.map((status, i) => (
            <div
              key={i}
              className={`w-4 h-4 rounded-full transition-colors duration-500 ${
                status === "active"
                  ? "bg-indigo-500 shadow-[0_0_6px_rgba(99,102,241,0.4)]"
                  : status === "synced"
                    ? "bg-emerald-500 shadow-[0_0_6px_rgba(34,197,94,0.4)]"
                    : "bg-slate-700"
              }`}
            />
          ))}
        </div>
        <div className="flex items-center gap-3 text-[9px] font-mono text-slate-500">
          <div className="flex items-center gap-1">
            <span className="w-1.5 h-1.5 rounded-full bg-indigo-500" /> Active
          </div>
          <div className="flex items-center gap-1">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" /> Synced
          </div>
          <div className="flex items-center gap-1">
            <span className="w-1.5 h-1.5 rounded-full bg-slate-700" /> Idle
          </div>
        </div>
      </MetricCard>
    </div>
  );
}
