"use client";

import { motion } from "framer-motion";

interface MotionDepthProps {
  children: React.ReactNode;
  depth?: number;
  className?: string;
}

/**
 * MotionDepth: Z-axis spatial depth primitive.
 * Applies subtle Z-axis perspective shifts for layered telemetry cards.
 */
export function MotionDepth({ children, depth = 10, className = "" }: MotionDepthProps) {
  return (
    <motion.div
      style={{ perspective: 1000 }}
      whileHover={{ translateZ: depth }}
      transition={{ type: "spring", stiffness: 150, damping: 18 }}
      className={className}
    >
      {children}
    </motion.div>
  );
}
