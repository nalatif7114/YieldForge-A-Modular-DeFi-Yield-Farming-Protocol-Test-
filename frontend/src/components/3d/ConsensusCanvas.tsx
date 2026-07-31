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
            className="w-full h-full"
            style={{ background: "#050816" }}
          >
            {/* Lighting System */}
            <ambientLight intensity={0.25} />
            <directionalLight position={[5, 5, 5]} intensity={1.0} color="#ffffff" />
            <directionalLight position={[-5, -2, -5]} intensity={0.35} color="#475569" />

            {/* 3D Validator Network Mesh & Observing Camera */}
            <SpatialNodeMesh />

            {/* Subtle Post-Processing */}
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
  /** "card" = rounded border for dashboard embeds; "fullscreen" = seamless hero background */
  variant?: "card" | "fullscreen";
}

export function ConsensusCanvas({
  className = "",
  showOverlay = true,
  variant = "card",
}: ConsensusCanvasProps) {
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

  const containerStyles =
    variant === "card"
      ? "rounded-3xl bg-[#030712] border border-white/10"
      : "bg-[#050816]";

  return (
    <div
      onMouseMove={handleMouseMove}
      className={`relative w-full h-full min-h-[400px] overflow-hidden ${containerStyles} ${className}`}
    >
      {/* Three.js Viewport */}
      <div className="absolute inset-0 z-0">
        <ThreeCanvas />
      </div>

      {/* ConsensusWave Overlay (dashboard only) */}
      {showOverlay && (
        <div className="relative z-10 p-4 max-w-md pointer-events-auto">
          <ConsensusWave />
        </div>
      )}
    </div>
  );
}
