"use client";

import dynamic from "next/dynamic";
import { LenisMasterTimeline } from "@/components/landing/LenisMasterTimeline";
import { ControlRoomHUD } from "@/components/landing/ControlRoomHUD";

// Dynamically import 3D Protocol Engine Canvas with SSR disabled
const ProtocolEngineCanvas = dynamic(
  () => import("@/components/3d/Engine/ProtocolEngineCanvas").then((mod) => mod.ProtocolEngineCanvas),
  { ssr: false }
);

export default function CinematicControlRoomPage() {
  return (
    <main className="relative min-h-screen bg-[#050816] overflow-x-hidden selection:bg-indigo-500/30 selection:text-indigo-300">
      {/* Lenis Smooth Scroll & Mouse Parallax Master Timeline */}
      <LenisMasterTimeline />

      {/* Infinite 100% Viewport 3D Protocol Engine Canvas */}
      <div className="fixed inset-0 z-0 pointer-events-auto">
        <ProtocolEngineCanvas />
        <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-10"></div>
      </div>

      {/* Vision Pro Minimalist Overlay HUD */}
      <ControlRoomHUD />
    </main>
  );
}
