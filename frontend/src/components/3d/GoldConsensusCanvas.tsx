"use client";

import { useCallback } from "react";
import dynamic from "next/dynamic";
import { useProtocolStore } from "@/store/useProtocolStore";

// Dynamically import Three.js Canvas with SSR disabled
const ThreeCanvas = dynamic(
  () =>
    import("@react-three/fiber").then((mod) => {
      const { Canvas } = mod;
      const { GoldSpatialNodeMesh } = require("./GoldSpatialNodeMesh");

      return function CanvasWrapper() {
        return (
          <Canvas
            dpr={[1, 2]}
            gl={{ antialias: true, alpha: true, powerPreference: "high-performance" }}
            camera={{ position: [0, 1.0, 7.2], fov: 45 }}
            className="w-full h-full"
          >
            {/* Lighting System: Champagne Gold Key & Soft Ambient */}
            <ambientLight intensity={0.3} />
            <directionalLight position={[6, 6, 6]} intensity={1.2} color="#F5E6B8" />
            <directionalLight position={[-6, -3, -4]} intensity={0.4} color="#D4AF37" />

            {/* 3D Gold Institutional Validator Topology */}
            <GoldSpatialNodeMesh />
          </Canvas>
        );
      };
    }),
  { ssr: false }
);

interface GoldConsensusCanvasProps {
  className?: string;
}

export function GoldConsensusCanvas({ className = "" }: GoldConsensusCanvasProps) {
  const { setMousePosition } = useProtocolStore();

  const handleMouseMove = useCallback(
    (e: React.MouseEvent<HTMLDivElement>) => {
      const rect = e.currentTarget.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
      const y = -(((e.clientY - rect.top) / rect.height) * 2 - 1);
      setMousePosition({ x, y });
    },
    [setMousePosition]
  );

  return (
    <div
      onMouseMove={handleMouseMove}
      className={`relative w-full h-full min-h-[380px] overflow-hidden ${className}`}
    >
      <ThreeCanvas />
    </div>
  );
}
