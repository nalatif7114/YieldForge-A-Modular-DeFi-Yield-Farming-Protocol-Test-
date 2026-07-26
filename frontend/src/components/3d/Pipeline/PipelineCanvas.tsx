"use client";

import { Suspense } from "react";
import { Canvas } from "@react-three/fiber";
import { PipelineScene } from "./PipelineScene";

export function PipelineCanvas() {
  return (
    <Canvas
      dpr={[1, 2]}
      camera={{ position: [0, 1.8, 13.5], fov: 45 }}
      gl={{ antialias: true, alpha: true }}
      className="w-full h-full"
    >
      <Suspense fallback={null}>
        <PipelineScene />
      </Suspense>
    </Canvas>
  );
}
