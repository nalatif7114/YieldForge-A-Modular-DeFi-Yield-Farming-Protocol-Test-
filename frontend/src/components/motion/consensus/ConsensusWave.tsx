"use client";

import { ConsensusNode } from "./ConsensusNode";
import { ConsensusConnection } from "./ConsensusConnection";
import { ConsensusPacket } from "./ConsensusPacket";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * ConsensusWave™: Protocol State Visualization Component.
 * 
 * Consensus Wave is NOT an animation.
 * Consensus Wave is a visualization of protocol state driven purely by ConsensusEngine events.
 * Contains ZERO timers (no setTimeout/setInterval).
 */
export function ConsensusWave() {
  const { state, activeNodes, activeConnections, nodes, connections, payload } = useConsensusEngine();

  const isPacketEntering = state === "TRANSACTION_RECEIVED";

  const getStatusLabel = () => {
    switch (state) {
      case "IDLE":
        return "Idle — Ready for Ingestion";
      case "TRANSACTION_RECEIVED":
        return "Transaction Received in Mempool";
      case "VALIDATING":
        return "Validator Node A Verifying Proof";
      case "PROPAGATING":
        return "Propagating Wave to Peer Nodes";
      case "CONSENSUS_REACHED":
        return "Consensus Reached (BFT Supermajority)";
      case "STATE_COMMITTED":
        return "State Root Committed to Vault";
      case "COMPLETE":
        return "Transaction Complete";
      default:
        return "Standby";
    }
  };

  const getStatusColor = () => {
    switch (state) {
      case "TRANSACTION_RECEIVED":
        return "text-sky-400 font-semibold";
      case "VALIDATING":
        return "text-amber-400 font-semibold";
      case "PROPAGATING":
        return "text-indigo-400 font-semibold";
      case "CONSENSUS_REACHED":
        return "text-emerald-400 font-bold";
      case "STATE_COMMITTED":
        return "text-emerald-300 font-bold";
      case "COMPLETE":
        return "text-emerald-400 font-bold";
      default:
        return "text-slate-400 font-normal";
    }
  };

  return (
    <div className="w-full p-6 rounded-2xl bg-white/[0.02] border border-white/10 space-y-4">
      {/* State Metric Header */}
      <div className="flex items-center justify-between font-mono text-xs">
        <div className="flex items-center gap-2">
          <span className="text-slate-400 uppercase tracking-wider">Consensus Wave™ Protocol State</span>
          {state !== "IDLE" && (
            <span className="px-2 py-0.5 rounded text-[9px] bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
              {state}
            </span>
          )}
        </div>
        <span className={getStatusColor()}>{getStatusLabel()}</span>
      </div>

      {/* SVG Topology Visualizer */}
      <div className="relative h-32 w-full rounded-xl bg-slate-950/80 border border-white/5 flex items-center justify-center p-2">
        <svg className="w-full h-full max-w-md" viewBox="0 0 320 100" fill="none">
          {/* Connection Rays (Driven strictly by ConsensusEngine activeConnections) */}
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

          {/* Incoming Transaction Packet (Driven strictly by TRANSACTION_RECEIVED state) */}
          {isPacketEntering && (
            <ConsensusPacket startX={0} startY={50} targetX={40} targetY={50} />
          )}

          {/* Validator Nodes (Driven strictly by ConsensusEngine state & activeNodes) */}
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
                label={node.label.split(" ")[2]} // e.g. "A", "B", "C", "D"
                state={nodeState}
              />
            );
          })}
        </svg>
      </div>

      {/* Footer Info */}
      <div className="flex items-center justify-between text-[10px] font-mono text-slate-500">
        <span>Engine: ConsensusEngine (Observable System)</span>
        <span>
          {payload ? `Tx: ${payload.id} (${payload.type})` : "4 Validator Signatures Required"}
        </span>
      </div>
    </div>
  );
}
