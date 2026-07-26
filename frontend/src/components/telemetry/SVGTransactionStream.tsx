"use client";

import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * SVGTransactionStream: Data packet stream visualization.
 * Subscribes to ConsensusEngine to synchronize packet stream animation speed,
 * payload ingestion indicators, and flow rate directly with protocol event state.
 */
export function SVGTransactionStream() {
  const { isWalletConnected } = useProtocolStore();
  const { state, isProcessing, payload } = useConsensusEngine();

  const getMotionDuration = () => {
    switch (state) {
      case "TRANSACTION_RECEIVED":
        return "0.6s";
      case "VALIDATING":
      case "PROPAGATING":
        return "0.8s";
      case "CONSENSUS_REACHED":
      case "STATE_COMMITTED":
        return "1.0s";
      default:
        return "3.5s";
    }
  };

  const getPacketColor = () => {
    switch (state) {
      case "TRANSACTION_RECEIVED":
        return "#38bdf8";
      case "VALIDATING":
        return "#f59e0b";
      case "PROPAGATING":
        return "#6366f1";
      case "CONSENSUS_REACHED":
      case "STATE_COMMITTED":
      case "COMPLETE":
        return "#22c55e";
      default:
        return isWalletConnected ? "#38bdf8" : "#475569";
    }
  };

  return (
    <div className="w-full p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
      <div className="flex items-center justify-between font-mono text-xs">
        <span className="text-slate-400 uppercase tracking-wider">Mempool & Input Stream Pipeline</span>
        <span className={isProcessing ? "text-sky-400 animate-pulse font-semibold" : "text-slate-500"}>
          {isProcessing ? `Stream Active (${state})` : (isWalletConnected ? "Stream Connected" : "Stream Idle")}
        </span>
      </div>

      <div className="relative h-20 w-full overflow-hidden rounded-xl bg-slate-950/80 border border-white/5 flex items-center px-4">
        <svg className="w-full h-full" viewBox="0 0 600 60" fill="none">
          {/* Conduit Path */}
          <path
            d="M 10 30 Q 150 10, 300 30 T 590 30"
            stroke={isProcessing ? getPacketColor() : (isWalletConnected ? "#38bdf8" : "#334155")}
            strokeWidth="2"
            strokeDasharray="4 4"
            className={isProcessing ? "opacity-75 transition-colors duration-300" : "opacity-40"}
          />

          {/* Animated Data Packet Dots driven by ConsensusEngine duration & colors */}
          <circle r={isProcessing ? "5" : "4"} fill={getPacketColor()}>
            <animateMotion
              path="M 10 30 Q 150 10, 300 30 T 590 30"
              dur={getMotionDuration()}
              repeatCount="indefinite"
            />
          </circle>
          <circle r={isProcessing ? "5" : "4"} fill={getPacketColor()}>
            <animateMotion
              path="M 10 30 Q 150 10, 300 30 T 590 30"
              dur={getMotionDuration()}
              begin="0.4s"
              repeatCount="indefinite"
            />
          </circle>
          <circle r={isProcessing ? "5" : "4"} fill={getPacketColor()}>
            <animateMotion
              path="M 10 30 Q 150 10, 300 30 T 590 30"
              dur={getMotionDuration()}
              begin="0.8s"
              repeatCount="indefinite"
            />
          </circle>
        </svg>

        {/* Labels */}
        <div className="absolute left-4 text-[10px] font-mono text-slate-500">RPC Ingestion</div>
        <div className="absolute right-4 text-[10px] font-mono text-indigo-400">
          {payload ? payload.type.toUpperCase() : "Contract Storage"}
        </div>
      </div>
    </div>
  );
}
