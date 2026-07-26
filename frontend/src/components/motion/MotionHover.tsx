"use client";

import { motion } from "framer-motion";

interface MotionHoverProps {
  children: React.ReactNode;
  scale?: number;
  className?: string;
}

/**
 * MotionHover: Tactile spring hover primitive.
 * Restrained micro-scale hover feedback (max 1.02x scale) with critically damped spring physics.
 */
export function MotionHover({
  children,
  scale = 1.02,
  className = "",
}: MotionHoverProps) {
  return (
    <motion.div
      whileHover={{ scale }}
      whileTap={{ scale: 0.98 }}
      transition={{ type: "spring", stiffness: 220, damping: 16 }}
      className={className}
    >
      {children}
    </motion.div>
  );
}
