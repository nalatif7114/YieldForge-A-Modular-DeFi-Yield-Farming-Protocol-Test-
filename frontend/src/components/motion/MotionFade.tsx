"use client";

import { motion } from "framer-motion";

interface MotionFadeProps {
  children: React.ReactNode;
  delay?: number;
  duration?: number;
  className?: string;
}

/**
 * MotionFade: Enterprise micro-fade primitive.
 * Soft alpha transition (200-300ms) for quiet, professional UI component entry.
 */
export function MotionFade({
  children,
  delay = 0,
  duration = 0.3,
  className = "",
}: MotionFadeProps) {
  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      transition={{ duration, delay, ease: [0.16, 1, 0.3, 1] }}
      className={className}
    >
      {children}
    </motion.div>
  );
}
