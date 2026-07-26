"use client";

import { motion } from "framer-motion";

interface MotionNodePulseProps {
  color?: string;
  active?: boolean;
  size?: number;
  className?: string;
}

/**
 * MotionNodePulse: Semantic network heartbeat pulse.
 * Signals consensus proof verification or node activity with calm, periodic alpha expansion.
 */
export function MotionNodePulse({
  color = "#4f46e5",
  active = true,
  size = 10,
  className = "",
}: MotionNodePulseProps) {
  return (
    <div className={`relative inline-flex items-center justify-center ${className}`}>
      {active && (
        <motion.span
          className="absolute rounded-full pointer-events-none"
          style={{ backgroundColor: color, width: size * 2.2, height: size * 2.2 }}
          animate={{ scale: [0.8, 1.6, 0.8], opacity: [0.6, 0, 0.6] }}
          transition={{ duration: 2.2, repeat: Infinity, ease: "easeInOut" }}
        />
      )}
      <span
        className="relative rounded-full"
        style={{ backgroundColor: color, width: size, height: size }}
      />
    </div>
  );
}
