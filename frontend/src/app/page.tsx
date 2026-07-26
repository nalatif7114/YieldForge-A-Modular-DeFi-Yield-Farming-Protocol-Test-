"use client";

import dynamic from "next/dynamic";
import { OrchestraProvider } from "@/components/motion/OrchestraContext";
import { LenisMasterTimeline } from "@/components/landing/LenisMasterTimeline";
import { ControlRoomHUD } from "@/components/landing/ControlRoomHUD";
import { SubtleNoiseOverlay } from "@/components/ui/SubtleNoiseOverlay";
import { ProtocolApplicationLoader } from "@/components/ui/ProtocolApplicationLoader";

// Dynamically import ConsensusCanvas (replacing legacy PipelineCanvas) with SSR disabled
const ConsensusCanvas = dynamic(
  () => import("@/components/3d/ConsensusCanvas").then((mod) => mod.ConsensusCanvas),
  { ssr: false }
);

export default function CinematicControlRoomPage() {
  return (
    <OrchestraProvider>
      <main className="relative min-h-screen bg-[#050816] overflow-x-hidden selection:bg-indigo-500/30 selection:text-indigo-300">
        {/* Initial Application Protocol Loader */}
        <ProtocolApplicationLoader minDuration={1600} />

        {/* Lenis Smooth Scroll & Mouse Parallax Master Timeline */}
        <LenisMasterTimeline />

        {/* Subtle Analog Noise Texture Layer */}
        <SubtleNoiseOverlay />

        {/* 100% Viewport 3D Consensus Network Canvas */}
        <div className="fixed inset-0 z-0 pointer-events-auto">
          <ConsensusCanvas className="w-full h-full rounded-none border-none" showOverlay={false} />
          <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-10"></div>
        </div>

        {/* Vision Pro Minimalist Overlay HUD */}
        <ControlRoomHUD />
      </main>
    </OrchestraProvider>
  );
}
