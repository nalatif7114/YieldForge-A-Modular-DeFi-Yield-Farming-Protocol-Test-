"use client";

import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * SVGTransactionStream: Data packet stream visualization with luxury gold conduit.
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
        return "#F5E6B8";
      case "VALIDATING":
        return "#E7C873";
      case "PROPAGATING":
        return "#D4AF37";
      case "CONSENSUS_REACHED":
      case "STATE_COMMITTED":
      case "COMPLETE":
        return "#22C55E";
      default:
        return isWalletConnected ? "#D4AF37" : "#52525B";
    }
  };

  return (
    <div className="w-full p-6 gold-card space-y-4">
      <div className="flex items-center justify-between font-mono text-xs">
        <span className="text-[#A1A1AA] uppercase tracking-wider">Transaction Stream</span>
        <span className={isProcessing ? "text-[#E7C873] animate-pulse font-semibold" : "text-[#A1A1AA]"}>
          {isProcessing ? `Active (${state})` : (isWalletConnected ? "Connected" : "Standby")}
        </span>
      </div>

      <div className="relative h-20 w-full overflow-hidden rounded-xl bg-[#111111] border border-[rgba(212,175,55,0.08)] flex items-center px-4">
        <svg className="w-full h-full" viewBox="0 0 600 60" fill="none">
          {/* Conduit Path */}
          <path
            d="M 10 30 Q 150 10, 300 30 T 590 30"
            stroke={isProcessing ? getPacketColor() : (isWalletConnected ? "#D4AF37" : "#3F3F46")}
            strokeWidth="2"
            strokeDasharray="4 4"
            className={isProcessing ? "opacity-90 transition-colors duration-300" : "opacity-40"}
          />

          {/* Animated Data Packet Dots */}
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
        <div className="absolute left-4 text-[10px] font-mono text-[#A1A1AA]">Vault Ingestion</div>
        <div className="absolute right-4 text-[10px] font-mono text-[#E7C873]">
          {payload ? payload.type.toUpperCase() : "Smart Contract Storage"}
        </div>
      </div>
    </div>
  );
}
