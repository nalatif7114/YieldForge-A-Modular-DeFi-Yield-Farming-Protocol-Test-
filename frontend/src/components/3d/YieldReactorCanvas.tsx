"use client";

import { Suspense, useState, useEffect } from "react";
import { Canvas } from "@react-three/fiber";
import { YieldReactorScene } from "./YieldReactorScene";

function LoadingFallback() {
  return (
    <div className="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 text-emerald-400 gap-3">
      <div className="w-10 h-10 border-2 border-emerald-500/20 border-t-emerald-400 rounded-full animate-spin"></div>
      <span className="text-xs font-mono tracking-wider uppercase text-slate-400">Initializing 3D Reactor...</span>
    </div>
  );
}

export function YieldReactorCanvas() {
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
          camera={{ position: [0, 1, 11], fov: 50 }}
          dpr={[1, 2]}
          gl={{ antialias: true, alpha: true }}
          className="w-full h-full"
        >
          <YieldReactorScene />
        </Canvas>
      </Suspense>
    </div>
  );
}
