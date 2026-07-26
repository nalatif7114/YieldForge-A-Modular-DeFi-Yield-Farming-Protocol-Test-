"use client";

import { motion } from "framer-motion";

interface ConsensusNodeProps {
  cx: number;
  cy: number;
  label?: string;
  state: "idle" | "active" | "validated";
  color?: string;
}

/**
 * ConsensusNode: Signature node primitive.
 * Scales gently (max 1.08x) when consensus wave passes through.
 */
export function ConsensusNode({
  cx,
  cy,
  label,
  state,
  color = "#4f46e5",
}: ConsensusNodeProps) {
  const isWaveActive = state === "active";
  const isValidated = state === "validated";

  return (
    <g transform={`translate(${cx}, ${cy})`}>
      {/* Wave Active Pulse Halo */}
      {isWaveActive && (
        <motion.circle
          r="16"
          fill="none"
          stroke={color}
          strokeWidth="1.5"
          initial={{ scale: 0.8, opacity: 0.8 }}
          animate={{ scale: 1.6, opacity: 0 }}
          transition={{ duration: 0.6, ease: "easeOut" }}
        />
      )}

      {/* Main Node Body */}
      <motion.circle
        r="7"
        fill={isValidated ? "#22c55e" : isWaveActive ? color : "#334155"}
        stroke={isWaveActive ? "#ffffff" : "none"}
        strokeWidth="1.5"
        animate={{
          scale: isWaveActive ? 1.08 : 1,
        }}
        transition={{ type: "spring", stiffness: 160, damping: 14 }}
      />

      {/* Node Text Label */}
      {label && (
        <text
          y="18"
          textAnchor="middle"
          fill="#94a3b8"
          fontSize="9"
          fontFamily="monospace"
          className="select-none"
        >
          {label}
        </text>
      )}
    </g>
  );
}
