"use client";

import { motion } from "framer-motion";

interface ConsensusConnectionProps {
  x1: number;
  y1: number;
  x2: number;
  y2: number;
  active?: boolean;
  color?: string;
}

/**
 * ConsensusConnection: Signature connection ray.
 * Illuminates briefly (opacity: 0.15 -> 0.7) when carrying consensus wave propagation.
 */
export function ConsensusConnection({
  x1,
  y1,
  x2,
  y2,
  active = false,
  color = "#4f46e5",
}: ConsensusConnectionProps) {
  return (
    <g>
      {/* Base Connection Ray Line */}
      <line
        x1={x1}
        y1={y1}
        x2={x2}
        y2={y2}
        stroke={active ? color : "#334155"}
        strokeWidth="1.5"
        strokeDasharray="4 4"
        className={active ? "opacity-75 transition-opacity duration-300" : "opacity-20"}
      />

      {/* Travelling Packet Pulse */}
      {active && (
        <motion.circle r="3" fill={color}>
          <animateMotion
            path={`M ${x1} ${y1} L ${x2} ${y2}`}
            dur="0.5s"
            repeatCount="1"
            fill="freeze"
          />
        </motion.circle>
      )}
    </g>
  );
}
