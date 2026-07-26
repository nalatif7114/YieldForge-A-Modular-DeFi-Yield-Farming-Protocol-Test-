"use client";

import { Suspense, useState, useEffect } from "react";
import { Canvas } from "@react-three/fiber";
import { EngineScene } from "./EngineScene";

interface ProtocolEngineCanvasProps {
  stage?: number;
}

function LoadingFallback() {
  return (
    <div className="absolute inset-0 flex flex-col items-center justify-center bg-[#050816] text-slate-400 gap-3">
      <div className="w-8 h-8 border-2 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
      <span className="text-[11px] font-mono tracking-widest uppercase text-slate-500">Connecting Protocol Telemetry...</span>
    </div>
  );
}

export function ProtocolEngineCanvas({ stage = 1 }: ProtocolEngineCanvasProps) {
  const [isMounted, setIsMounted] = useState(false);

  useEffect(() => {
    setIsMounted(true);
  }, []);

  if (!isMounted) {
    return <LoadingFallback />;
  }

  return (
    <div className="w-full h-full relative">
      <Suspense fallback={<LoadingFallback />}>
        <Canvas
          camera={{ position: [0, 1, 10.5], fov: 48 }}
          dpr={[1, 2]}
          gl={{ antialias: true, alpha: true }}
          className="w-full h-full"
        >
          <EngineScene stage={stage} />
        </Canvas>
      </Suspense>
    </div>
  );
}
