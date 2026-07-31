"use client";

import { ConsensusNode } from "./ConsensusNode";
import { ConsensusConnection } from "./ConsensusConnection";
import { ConsensusPacket } from "./ConsensusPacket";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * Strategy Lifecycle Component (formerly ConsensusWave).
 * Driven purely by ConsensusEngine state machine events.
 */
export function ConsensusWave() {
  const { state, activeNodes, activeConnections, nodes, connections, payload } = useConsensusEngine();

  const isPacketEntering = state === "TRANSACTION_RECEIVED";

  const getStatusLabel = () => {
    switch (state) {
      case "IDLE":
        return "Standby — Awaiting Allocation";
      case "TRANSACTION_RECEIVED":
        return "Transaction Ingested into Mempool";
      case "VALIDATING":
        return "Validator A Verifying Zero-Knowledge Proof";
      case "PROPAGATING":
        return "Propagating State Proof to Peer Nodes";
      case "CONSENSUS_REACHED":
        return "Consensus Achieved (BFT Supermajority)";
      case "STATE_COMMITTED":
        return "State Root Committed to Smart Vault";
      case "COMPLETE":
        return "Transaction Finalized";
      default:
        return "Standby";
    }
  };

  const getStatusColor = () => {
    switch (state) {
      case "TRANSACTION_RECEIVED":
        return "text-[#F5E6B8] font-semibold";
      case "VALIDATING":
        return "text-[#E7C873] font-semibold";
      case "PROPAGATING":
        return "text-[#D4AF37] font-semibold";
      case "CONSENSUS_REACHED":
      case "STATE_COMMITTED":
      case "COMPLETE":
        return "text-emerald-400 font-bold";
      default:
        return "text-[#A1A1AA] font-normal";
    }
  };

  return (
    <div className="w-full p-6 gold-card space-y-4">
      {/* State Metric Header */}
      <div className="flex items-center justify-between font-mono text-xs">
        <div className="flex items-center gap-2">
          <span className="text-[#A1A1AA] uppercase tracking-wider">Active Strategy Lifecycle</span>
          {state !== "IDLE" && (
            <span className="px-2.5 py-0.5 rounded text-[9px] bg-[#D4AF37]/15 text-[#F5E6B8] border border-[rgba(212,175,55,0.25)]">
              {state}
            </span>
          )}
        </div>
        <span className={getStatusColor()}>{getStatusLabel()}</span>
      </div>

      {/* SVG Topology Visualizer */}
      <div className="relative h-32 w-full rounded-xl bg-[#111111] border border-[rgba(212,175,55,0.08)] flex items-center justify-center p-2">
        <svg className="w-full h-full max-w-md" viewBox="0 0 320 100" fill="none">
          {/* Connection Rays */}
          {connections.map((conn, idx) => {
            const n1 = nodes.find((n) => n.id === conn.from)!;
            const n2 = nodes.find((n) => n.id === conn.to)!;
            const isRayActive = activeConnections.some(
              (c) => (c.from === conn.from && c.to === conn.to) || (c.from === conn.to && c.to === conn.from)
            );

            return (
              <ConsensusConnection
                key={idx}
                x1={n1.cx}
                y1={n1.cy}
                x2={n2.cx}
                y2={n2.cy}
                active={isRayActive}
              />
            );
          })}

          {/* Incoming Transaction Packet */}
          {isPacketEntering && (
            <ConsensusPacket startX={0} startY={50} targetX={40} targetY={50} />
          )}

          {/* Validator Nodes */}
          {nodes.map((node) => {
            let nodeState: "idle" | "active" | "validated" = "idle";
            
            if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED" || state === "COMPLETE") {
              nodeState = "validated";
            } else if (activeNodes.includes(node.id)) {
              nodeState = "active";
            }

            return (
              <ConsensusNode
                key={node.id}
                cx={node.cx}
                cy={node.cy}
                label={node.label.split(" ")[2]}
                state={nodeState}
              />
            );
          })}
        </svg>
      </div>

      {/* Footer Info */}
      <div className="flex items-center justify-between text-[10px] font-mono text-[#A1A1AA]">
        <span>Consensus Engine Event Bus</span>
        <span>
          {payload ? `Tx: ${payload.id} (${payload.type})` : "4 Signatures Required"}
        </span>
      </div>
    </div>
  );
}
