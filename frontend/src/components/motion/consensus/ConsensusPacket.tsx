"use client";

import { motion } from "framer-motion";

interface ConsensusPacketProps {
  startX: number;
  startY: number;
  targetX: number;
  targetY: number;
  color?: string;
  onArrival?: () => void;
}

/**
 * ConsensusPacket: Incoming transaction RPC payload capsule entering the network topology.
 */
export function ConsensusPacket({
  startX,
  startY,
  targetX,
  targetY,
  color = "#38bdf8",
  onArrival,
}: ConsensusPacketProps) {
  return (
    <motion.circle
      r="4"
      fill={color}
      initial={{ cx: startX, cy: startY, opacity: 0 }}
      animate={{ cx: targetX, cy: targetY, opacity: 1 }}
      transition={{ duration: 0.4, ease: "easeOut" }}
      onAnimationComplete={onArrival}
    />
  );
}
