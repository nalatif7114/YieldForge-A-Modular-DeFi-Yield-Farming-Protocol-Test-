"use client";

import dynamic from "next/dynamic";
import { ConsensusWave } from "@/components/motion/consensus";

// Dynamically import 3D Canvas with SSR disabled
const ThreeCanvas = dynamic(
  () =>
    import("@react-three/fiber").then((mod) => {
      const { Canvas } = mod;
      const { SpatialNodeMesh } = require("./SpatialNodeMesh");
      return function CanvasWrapper() {
        return (
          <Canvas dpr={[1, 2]} camera={{ position: [0, 1.2, 7.5], fov: 45 }} className="w-full h-full">
            <ambientLight intensity={0.3} />
            <directionalLight position={[5, 5, 5]} intensity={1.2} />
            <SpatialNodeMesh />
          </Canvas>
        );
      };
    }),
  { ssr: false }
);

interface ConsensusCanvasProps {
  className?: string;
  showOverlay?: boolean;
}

/**
 * ConsensusCanvas: Unified Canvas Container.
 * Encapsulates:
 * - SpatialNodeMesh (Floating 3D Validator Nodes & Selective Connection Rays)
 * - CinematicConsensusCamera (Observing camera with ~2% tracking micro-shift)
 * - ConsensusWave (Protocol state visualizer)
 */
export function ConsensusCanvas({ className = "", showOverlay = true }: ConsensusCanvasProps) {
  return (
    <div className={`relative w-full h-full min-h-[380px] rounded-3xl overflow-hidden bg-slate-950/80 border border-white/10 ${className}`}>
      {/* 3D Scene viewport (SpatialNodeMesh & CinematicConsensusCamera) */}
      <div className="absolute inset-0 z-0">
        <ThreeCanvas />
      </div>

      {/* ConsensusWave Overlay */}
      {showOverlay && (
        <div className="relative z-10 p-4 max-w-md pointer-events-auto">
          <ConsensusWave />
        </div>
      )}
    </div>
  );
}
