"use client";

import { motion } from "framer-motion";
import { useProtocolStore } from "@/store/useProtocolStore";

interface MotionParallaxProps {
  children: React.ReactNode;
  intensity?: number;
  className?: string;
}

/**
 * MotionParallax: Micro parallax dampener.
 * Maps global mouse position to smooth, subtle XY offset (max ±6px displacement).
 */
export function MotionParallax({
  children,
  intensity = 6,
  className = "",
}: MotionParallaxProps) {
  const { mousePosition } = useProtocolStore();

  const offsetX = mousePosition.x * intensity;
  const offsetY = mousePosition.y * intensity;

  return (
    <motion.div
      animate={{ x: offsetX, y: offsetY }}
      transition={{ type: "spring", stiffness: 120, damping: 20, mass: 0.1 }}
      className={className}
    >
      {children}
    </motion.div>
  );
}
