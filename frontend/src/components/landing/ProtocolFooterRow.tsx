"use client";

import { useEffect, useState, useRef } from "react";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/* ─── Card Shell: 20px Radius, 150–200ms Micro-Interactions ─── */
function ActivityCard({
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
   Protocol Live Activity Row — Event Stream, State Root, Rewards
   ═══════════════════════════════════════════════════════════════ */
export function ProtocolFooterRow() {
  const { logHistory, state } = useConsensusEngine();
  const [stateRoot, setStateRoot] = useState("0x9b7e...c4d2");
  const [blockNumber, setBlockNumber] = useState(19_845_231);
  const [countdown, setCountdown] = useState({ h: 5, m: 14, s: 36 });
  const prevStateRef = useRef(state);

  // Update state root hash when STATE_COMMITTED fires
  useEffect(() => {
    if (
      prevStateRef.current !== "STATE_COMMITTED" &&
      state === "STATE_COMMITTED"
    ) {
      const hash = `0x${Math.random().toString(16).slice(2, 6)}...${Math.random().toString(16).slice(2, 6)}`;
      setStateRoot(hash);
      setBlockNumber((prev) => prev + 1);
    }
    prevStateRef.current = state;
  }, [state]);

  // Reward distribution countdown
  useEffect(() => {
    const interval = setInterval(() => {
      setCountdown((prev) => {
        let { h, m, s } = prev;
        s--;
        if (s < 0) {
          s = 59;
          m--;
        }
        if (m < 0) {
          m = 59;
          h--;
        }
        if (h < 0) {
          h = 23;
          m = 59;
          s = 59;
        }
        return { h, m, s };
      });
    }, 1000);
    return () => clearInterval(interval);
  }, []);

  const pad = (n: number) => n.toString().padStart(2, "0");

  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-3 px-4 sm:px-6 lg:px-8 xl:px-12 pb-6">
      {/* ── Live Consensus Event Stream ── */}
      <ActivityCard>
        <div className="flex items-center gap-2 mb-3">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
          <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
            Consensus Event Stream
          </span>
        </div>
        <div className="space-y-1.5 max-h-[120px] overflow-y-auto scrollbar-thin">
          {logHistory.length === 0 ? (
            <div className="text-[11px] font-mono text-slate-600 py-6 text-center">
              Awaiting protocol activity…
            </div>
          ) : (
            logHistory.slice(0, 8).map((entry, i) => {
              const time = new Date(entry.timestamp).toLocaleTimeString(
                "en-US",
                {
                  hour12: false,
                  hour: "2-digit",
                  minute: "2-digit",
                  second: "2-digit",
                }
              );
              return (
                <div
                  key={`${entry.timestamp}-${i}`}
                  className="flex items-start gap-2 text-[11px] font-mono leading-tight"
                >
                  <span className="text-slate-600 shrink-0">{time}</span>
                  <span className="text-slate-600">→</span>
                  <span className="text-slate-400 truncate">
                    {entry.message}
                  </span>
                </div>
              );
            })
          )}
        </div>
      </ActivityCard>

      {/* ── State Root ── */}
      <ActivityCard>
        <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
          State Root
        </span>
        <div className="mt-3 text-xl font-mono font-bold text-white tracking-tight">
          {stateRoot}
        </div>
        <div className="mt-3 space-y-1 text-[11px] font-mono">
          <div className="flex justify-between">
            <span className="text-slate-500">Block:</span>
            <span className="text-white">
              {blockNumber.toLocaleString()}
            </span>
          </div>
          <div className="flex justify-between">
            <span className="text-slate-500">Time:</span>
            <span className="text-white">
              {new Date().toLocaleTimeString("en-US", { hour12: false })}
            </span>
          </div>
        </div>
      </ActivityCard>

      {/* ── Next Reward Distribution ── */}
      <ActivityCard>
        <span className="text-[10px] font-mono uppercase tracking-wider text-slate-500">
          Next Reward Distribution
        </span>
        <div className="mt-3 flex items-baseline gap-1.5">
          <div className="text-center">
            <span className="text-2xl font-mono font-bold text-white">
              {pad(countdown.h)}
            </span>
            <div className="text-[9px] font-mono text-slate-600 mt-0.5">
              HRS
            </div>
          </div>
          <span className="text-sm font-mono text-slate-600 self-start mt-1">
            :
          </span>
          <div className="text-center">
            <span className="text-2xl font-mono font-bold text-white">
              {pad(countdown.m)}
            </span>
            <div className="text-[9px] font-mono text-slate-600 mt-0.5">
              MIN
            </div>
          </div>
          <span className="text-sm font-mono text-slate-600 self-start mt-1">
            :
          </span>
          <div className="text-center">
            <span className="text-2xl font-mono font-bold text-white">
              {pad(countdown.s)}
            </span>
            <div className="text-[9px] font-mono text-slate-600 mt-0.5">
              SEC
            </div>
          </div>
        </div>
        <div className="mt-3 pt-3 border-t border-white/[0.06] flex justify-between text-[11px] font-mono">
          <span className="text-slate-500">Est. Rewards</span>
          <span className="text-emerald-400 font-medium">$42,881.23</span>
        </div>
      </ActivityCard>
    </div>
  );
}
