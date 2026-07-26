"use client";

import dynamic from "next/dynamic";
import { OrchestraProvider } from "@/components/motion/OrchestraContext";
import { LenisMasterTimeline } from "@/components/landing/LenisMasterTimeline";
import { ControlRoomHUD } from "@/components/landing/ControlRoomHUD";
import { SubtleNoiseOverlay } from "@/components/ui/SubtleNoiseOverlay";
import { ProtocolApplicationLoader } from "@/components/ui/ProtocolApplicationLoader";

// Dynamically import 3D Pipeline Canvas with SSR disabled
const PipelineCanvas = dynamic(
  () => import("@/components/3d/Pipeline/PipelineCanvas").then((mod) => mod.PipelineCanvas),
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

        {/* Infinite 100% Viewport 3D Linear Pipeline Canvas */}
        <div className="fixed inset-0 z-0 pointer-events-auto">
          <PipelineCanvas />
          <div className="absolute inset-0 bg-radial-vignette pointer-events-none z-10"></div>
        </div>

        {/* Vision Pro Minimalist Overlay HUD */}
        <ControlRoomHUD />
      </main>
    </OrchestraProvider>
  );
}
