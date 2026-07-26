"use client";

import { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Activity, Database, ShieldCheck } from "lucide-react";

interface ProtocolApplicationLoaderProps {
  onComplete?: () => void;
  minDuration?: number;
}

/**
 * ProtocolApplicationLoader: Handcrafted initial application loader.
 * Maps protocol wakeup stages to crisp SVG telemetry indicators without AI/LLM jargon.
 */
export function ProtocolApplicationLoader({
  onComplete,
  minDuration = 1600,
}: ProtocolApplicationLoaderProps) {
  const [progress, setProgress] = useState(0);
  const [stageText, setStageText] = useState("Initializing Protocol");
  const [isCompleted, setIsCompleted] = useState(false);

  useEffect(() => {
    const startTime = Date.now();

    const interval = setInterval(() => {
      const elapsedTime = Date.now() - startTime;
      const rawProgress = Math.min(Math.floor((elapsedTime / minDuration) * 100), 100);

      setProgress(rawProgress);

      if (rawProgress < 25) {
        setStageText("Initializing Protocol Core");
      } else if (rawProgress < 55) {
        setStageText("Synchronizing Validator Mesh");
      } else if (rawProgress < 85) {
        setStageText("Restoring Yield Compounding Engine");
      } else if (rawProgress < 100) {
        setStageText("Preparing Telemetry Control Room");
      } else {
        setStageText("Protocol Active");
        clearInterval(interval);
        setTimeout(() => {
          setIsCompleted(true);
          if (onComplete) onComplete();
        }, 300);
      }
    }, 40);

    return () => clearInterval(interval);
  }, [minDuration, onComplete]);

  return (
    <AnimatePresence>
      {!isCompleted && (
        <motion.div
          initial={{ opacity: 1 }}
          exit={{ opacity: 0, transition: { duration: 0.8, ease: [0.16, 1, 0.3, 1] } }}
          className="fixed inset-0 z-50 bg-[#050816] text-white flex flex-col items-center justify-center font-sans selection:bg-indigo-500/30"
        >
          {/* Subtle Background Radial Vignette */}
          <div className="absolute inset-0 bg-radial-vignette pointer-events-none opacity-60"></div>

          {/* Central Telemetry Wakeup Container */}
          <div className="relative z-10 max-w-sm w-full px-6 space-y-8 text-center">
            
            {/* SVG Precision Pulse Ring */}
            <div className="relative w-20 h-20 mx-auto flex items-center justify-center">
              <svg className="w-full h-full transform -rotate-90" viewBox="0 0 80 80">
                <circle
                  cx="40"
                  cy="40"
                  r="34"
                  stroke="#1e293b"
                  strokeWidth="3"
                  fill="none"
                />
                <circle
                  cx="40"
                  cy="40"
                  r="34"
                  stroke="#4f46e5"
                  strokeWidth="3"
                  fill="none"
                  strokeDasharray="213.6"
                  strokeDashoffset={213.6 - (213.6 * progress) / 100}
                  strokeLinecap="round"
                  className="transition-all duration-150 ease-out"
                />
              </svg>
              <div className="absolute inset-0 flex items-center justify-center font-mono text-xs font-bold text-indigo-400">
                YF
              </div>
            </div>

            {/* Stage Text & Percentage Counter */}
            <div className="space-y-2">
              <div className="flex items-center justify-center gap-2 text-xs font-mono text-indigo-400">
                <Activity className="w-3.5 h-3.5 animate-pulse" />
                <span className="uppercase tracking-widest">{stageText}</span>
              </div>

              <div className="text-3xl font-extrabold font-mono text-white tracking-tight">
                {progress}%
              </div>
            </div>

            {/* Progress Bar Track */}
            <div className="h-1.5 w-full rounded-full bg-slate-900 overflow-hidden border border-white/10 p-0.5">
              <motion.div
                className="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-400"
                style={{ width: `${progress}%` }}
              />
            </div>

            {/* Telemetry Badges */}
            <div className="pt-2 flex items-center justify-center gap-6 text-[10px] font-mono text-slate-500">
              <div className="flex items-center gap-1.5">
                <ShieldCheck className="w-3 h-3 text-emerald-400" />
                <span>Re-entrancy Guard</span>
              </div>
              <div className="flex items-center gap-1.5">
                <Database className="w-3 h-3 text-sky-400" />
                <span>Sepolia-11155111</span>
              </div>
            </div>

          </div>

          <footer className="absolute bottom-8 text-[11px] font-mono text-slate-600 uppercase tracking-widest">
            YieldForge Telemetry Engine v1.0
          </footer>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
