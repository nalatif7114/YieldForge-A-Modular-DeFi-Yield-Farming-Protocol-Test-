"use client";

import { useCallback } from "react";
import dynamic from "next/dynamic";
import { useProtocolStore } from "@/store/useProtocolStore";
import { ConsensusWave } from "@/components/motion/consensus";

// Dynamically import 3D Canvas with SSR disabled
const ThreeCanvas = dynamic(
  () =>
    import("@react-three/fiber").then((mod) => {
      const { Canvas } = mod;
      const { EffectComposer, Bloom } = require("@react-three/postprocessing");
      const { SpatialNodeMesh } = require("./SpatialNodeMesh");

      return function CanvasWrapper() {
        return (
          <Canvas
            dpr={[1, 2]}
            gl={{ antialias: true, alpha: false, powerPreference: "high-performance" }}
            camera={{ position: [0, 1.2, 7.5], fov: 45 }}
            className="w-full h-full bg-[#030712]"
          >
            {/* Phase 3 Lighting System */}
            {/* 1. Ambient Light */}
            <ambientLight intensity={0.25} />
            {/* 2. Soft Key Light */}
            <directionalLight position={[5, 5, 5]} intensity={1.0} color="#ffffff" />
            {/* 3. Subtle Rim Light */}
            <directionalLight position={[-5, -2, -5]} intensity={0.35} color="#475569" />

            {/* 3D Validator Network Mesh & Observing Camera */}
            <SpatialNodeMesh />

            {/* Phase 3 Subtle Post-Processing */}
            <EffectComposer multisampling={4}>
              <Bloom
                intensity={0.25}
                luminanceThreshold={0.8}
                luminanceSmoothing={0.9}
                mipmapBlur
              />
            </EffectComposer>
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
 * Phase 3: ConsensusCanvas
 * 
 * Single Three.js Orchestration Layer.
 * Owns: renderer, camera, lighting, post-processing, mouse interaction, resize, responsiveness.
 * 
 * Architecture Rules:
 * - Does NOT own protocol logic (ConsensusEngine owns protocol state).
 * - Lighting: 1 soft key light, 1 subtle rim light, 1 ambient light. No colorful/HDR lights.
 * - Background: Very dark, almost black (#030712), zero gradients, zero galaxy/stars/particles.
 * - Post Processing: Very subtle bloom, minimal depth perception, high quality antialiasing.
 * - Composition: Consensus Network is the sole visual focal point.
 */
export function ConsensusCanvas({ className = "", showOverlay = true }: ConsensusCanvasProps) {
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
      className={`relative w-full h-full min-h-[400px] rounded-3xl overflow-hidden bg-[#030712] border border-white/10 ${className}`}
    >
      {/* Three.js Viewport (Renderer, Camera, Lighting, Post-Processing, SpatialNodeMesh) */}
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
