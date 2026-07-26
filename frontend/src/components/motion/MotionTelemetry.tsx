"use client";

import { motion } from "framer-motion";

interface MotionTelemetryProps {
  value: string | number;
  label?: string;
  className?: string;
}

/**
 * MotionTelemetry: Metric ticker transition primitive.
 * Smoothly updates numerical or string telemetry values with a quiet opacity scale shift.
 */
export function MotionTelemetry({ value, label, className = "" }: MotionTelemetryProps) {
  return (
    <div className={`font-mono ${className}`}>
      {label && <span className="text-slate-500 text-[10px] uppercase block mb-0.5">{label}</span>}
      <motion.span
        key={String(value)}
        initial={{ opacity: 0, y: 2 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.25, ease: [0.16, 1, 0.3, 1] }}
        className="inline-block font-semibold"
      >
        {value}
      </motion.span>
    </div>
  );
}
