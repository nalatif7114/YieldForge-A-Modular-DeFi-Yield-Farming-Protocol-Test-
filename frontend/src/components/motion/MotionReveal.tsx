"use client";

import { motion, Variants } from "framer-motion";

interface MotionRevealProps {
  children: React.ReactNode;
  delay?: number;
  stagger?: number;
  className?: string;
}

/**
 * MotionReveal: Enterprise staggered entrance primitive.
 * Animates child elements sequentially with micro-vertical displacement (y: 6px -> 0px).
 */
export function MotionReveal({
  children,
  delay = 0,
  stagger = 0.06,
  className = "",
}: MotionRevealProps) {
  const containerVariants: Variants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: stagger,
        delayChildren: delay,
      },
    },
  };

  return (
    <motion.div
      variants={containerVariants}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, margin: "-40px" }}
      className={className}
    >
      {children}
    </motion.div>
  );
}

export function MotionRevealItem({ children, className = "" }: { children: React.ReactNode; className?: string }) {
  const itemVariants: Variants = {
    hidden: { opacity: 0, y: 6 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        type: "spring" as const,
        stiffness: 140,
        damping: 18,
      },
    },
  };

  return (
    <motion.div variants={itemVariants} className={className}>
      {children}
    </motion.div>
  );
}
