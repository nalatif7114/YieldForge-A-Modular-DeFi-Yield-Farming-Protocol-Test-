"use client";

import { motion } from "framer-motion";
import { useProtocolStore } from "@/store/useProtocolStore";

interface MotionScrollProps {
  children: React.ReactNode;
  start?: number;
  end?: number;
  className?: string;
}

/**
 * MotionScroll: Lenis scroll progress primitive.
 * Maps master scroll progress (0.0 - 1.0) to child opacity and vertical translation.
 */
export function MotionScroll({
  children,
  start = 0,
  end = 1,
  className = "",
}: MotionScrollProps) {
  const { scrollProgress } = useProtocolStore();

  const isActive = scrollProgress >= start && scrollProgress <= end;

  return (
    <motion.div
      animate={{
        opacity: isActive ? 1 : 0.25,
        y: isActive ? 0 : 8,
      }}
      transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
      className={className}
    >
      {children}
    </motion.div>
  );
}
