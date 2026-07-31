"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { Activity, Wifi, Database, Layers } from "lucide-react";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { ConnectButton } from "@/components/ConnectButton";

/**
 * Institutional Header Bar — Enterprise SaaS telemetry header for YieldForge App Dashboard.
 */
export function NetworkHeaderTelemetry() {
  const { isWalletConnected } = useProtocolStore();
  const { state, isProcessing } = useConsensusEngine();
  const [blockHeight, setBlockHeight] = useState(5821904);
  const [latency, setLatency] = useState(42);

  useEffect(() => {
    // Live block height incrementing every 12 seconds
    const blockInterval = setInterval(() => {
      setBlockHeight((prev) => prev + 1);
    }, 12000);

    // Minor RPC latency fluctuation (38ms - 46ms)
    const latencyInterval = setInterval(() => {
      setLatency(38 + Math.floor(Math.random() * 9));
    }, 4000);

    return () => {
      clearInterval(blockInterval);
      clearInterval(latencyInterval);
    };
  }, []);

  const getStatusText = () => {
    if (isProcessing) return `Processing (${state})`;
    if (isWalletConnected) return "Synchronized";
    return "Standby";
  };

  const getStatusStyle = () => {
    if (isProcessing) return "text-[#E7C873] font-bold animate-pulse";
    if (isWalletConnected) return "text-emerald-400 font-bold";
    return "text-[#A1A1AA] font-normal";
  };

  return (
    <header className="fixed top-0 left-0 right-0 z-50 h-16 px-6 sm:px-10 flex items-center justify-between backdrop-blur-xl bg-[#080808]/85 border-b border-[rgba(212,175,55,0.08)] text-[#F4F4F4]">
      {/* Brand & Network Label */}
      <div className="flex items-center gap-3">
        <Link href="/" className="w-8 h-8 rounded-lg bg-[#161616] border border-[rgba(212,175,55,0.2)] flex items-center justify-center font-mono text-xs font-bold text-[#D4AF37] hover:border-[#D4AF37] transition-colors">
          YF
        </Link>
        <div className="flex items-center gap-2 font-mono text-xs">
          <span className="font-bold text-[#F4F4F4] tracking-tight">YieldForge Platform</span>
          <span className="text-[10px] px-2.5 py-0.5 rounded-full bg-[#161616] text-[#A1A1AA] border border-[rgba(212,175,55,0.1)]">
            Ethereum Mainnet
          </span>
        </div>
      </div>

      {/* Real-Time Platform Metrics Bar (SaaS Style) */}
      <div className="hidden lg:flex items-center gap-8 font-mono text-xs text-[#A1A1AA]">
        <div className="flex items-center gap-2">
          <Database className="w-3.5 h-3.5 text-[#D4AF37]" />
          <span>Block: <strong className="text-[#F4F4F4]">#{blockHeight.toLocaleString()}</strong></span>
        </div>

        <div className="flex items-center gap-2">
          <Wifi className="w-3.5 h-3.5 text-[#E7C873]" />
          <span>Ping: <strong className="text-[#F5E6B8]">{latency} ms</strong></span>
        </div>

        <div className="flex items-center gap-2">
          <Activity className="w-3.5 h-3.5 text-emerald-400 animate-pulse" />
          <span>Throughput: <strong className="text-[#F4F4F4]">{isProcessing ? "48.6 TPS" : "14.2 TPS"}</strong></span>
        </div>

        <div className="flex items-center gap-2">
          <Layers className="w-3.5 h-3.5 text-[#D4AF37]" />
          <span>Status: <strong className={getStatusStyle()}>{getStatusText()}</strong></span>
        </div>
      </div>

      {/* Web3 Wallet Connection */}
      <ConnectButton />
    </header>
  );
}
