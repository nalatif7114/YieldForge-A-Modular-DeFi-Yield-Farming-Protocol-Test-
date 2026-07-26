"use client";

import dynamic from "next/dynamic";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { useWallet } from "@/hooks/useWallet";
import { NetworkHeaderTelemetry } from "./NetworkHeaderTelemetry";
import { SVGTransactionStream } from "./SVGTransactionStream";
import { SVGYieldAccumulationSparkline } from "./SVGYieldAccumulationSparkline";
import { ConsensusWave } from "@/components/motion/consensus";
import { MagneticButton } from "@/components/ui/MagneticButton";
import { PerspectiveGlowBorder } from "@/components/ui/PerspectiveGlowBorder";

// Dynamically import 3D Spatial Node Canvas
const SpatialCanvas = dynamic(
  () =>
    import("@react-three/fiber").then((mod) => {
      const { Canvas } = mod;
      const { SpatialNodeMesh } = require("@/components/3d/SpatialNodeMesh");
      return function CanvasWrapper() {
        return (
          <Canvas dpr={[1, 2]} camera={{ position: [0, 0, 6], fov: 45 }} className="w-full h-full">
            <ambientLight intensity={0.3} />
            <directionalLight position={[5, 5, 5]} intensity={1.2} />
            <SpatialNodeMesh />
          </Canvas>
        );
      };
    }),
  { ssr: false }
);

export function BehavioralDashboard() {
  const { isConnected, connect } = useWallet();
  const { state, activeNodes, logHistory, isProcessing, submitTransaction } = useConsensusEngine();

  const handleStakeClick = () => {
    submitTransaction("staking");
  };

  const handleClaimClick = () => {
    submitTransaction("claiming");
  };

  return (
    <div className="min-h-screen bg-[#050816] text-white flex flex-col font-sans selection:bg-indigo-500/30 selection:text-indigo-300">
      
      {/* Datadog / Cloudflare Radar Style Header */}
      <NetworkHeaderTelemetry />

      {/* Main Software Telemetry Viewport */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-6 sm:px-10 lg:px-12 pt-28 pb-16 space-y-8">
        
        {/* Section 1: Product Title & Interactive Action Panel */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-white/10 pb-6">
          <div>
            <div className="flex items-center gap-2">
              <span className="text-[11px] font-mono uppercase tracking-widest text-indigo-400">
                Software Telemetry Control Room
              </span>
              <span className="px-2 py-0.5 rounded-full text-[10px] font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                ConsensusEngine Active
              </span>
            </div>
            <h1 className="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mt-1">
              On-Chain Behavioral Matrix
            </h1>
            <p className="text-xs text-slate-400 mt-1 max-w-xl font-normal">
              Observable distributed system state machine — real-time protocol consensus event stream.
            </p>
          </div>

          {/* Interactive Protocol Action Triggers */}
          <div className="flex items-center gap-3">
            <MagneticButton
              onClick={handleStakeClick}
              disabled={isProcessing}
              variant="primary"
            >
              <span>{isProcessing ? `Processing (${state})...` : "Execute Stake (YFT)"}</span>
            </MagneticButton>

            <MagneticButton
              onClick={handleClaimClick}
              disabled={isProcessing}
              variant="emerald"
            >
              <span>{isProcessing ? "Validating Wave..." : "Harvest Yield (Emerald)"}</span>
            </MagneticButton>
          </div>
        </div>

        {/* Empty State Banner (Disconnected Identity) */}
        {!isConnected && (
          <div className="p-8 rounded-3xl bg-white/[0.02] border border-white/10 backdrop-blur-xl">
            <div className="max-w-xl space-y-3">
              <span className="px-3 py-1 text-[10px] font-mono rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 inline-block">
                Identity Required
              </span>
              <h2 className="text-xl font-bold text-white">Connect Web3 Wallet</h2>
              <p className="text-xs text-slate-400 font-normal leading-relaxed">
                Connecting your wallet synchronizes RPC consensus node telemetry, unlocks live contract balances, and activates automated compounding monitors.
              </p>
              <button
                onClick={connect}
                className="mt-2 px-5 py-2.5 rounded-xl font-semibold text-xs bg-white text-slate-950 hover:bg-slate-100 transition-all shadow-sm cursor-pointer"
              >
                Connect MetaMask Wallet
              </button>
            </div>
          </div>
        )}

        {/* Section 2: YieldForge Signature Consensus Wave™ & Vector Pipelines */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <ConsensusWave />
          <SVGTransactionStream />
          <SVGYieldAccumulationSparkline />
        </div>

        {/* Section 3: Spatial Consensus Topology & System Health */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          {/* Spatial 3D RPC Node Mesh (Hyper-lightweight WebGL) */}
          <PerspectiveGlowBorder className="p-6 col-span-1 lg:col-span-2 space-y-4">
            <div className="flex items-center justify-between font-mono text-xs">
              <span className="text-slate-400 uppercase tracking-wider">Spatial Consensus Node Matrix</span>
              <span className={activeNodes.length > 0 ? "text-emerald-400 font-bold" : "text-indigo-400"}>
                {activeNodes.length > 0 ? `${activeNodes.length} Nodes Validating` : "4 Nodes Synchronized"}
              </span>
            </div>
            <div className="h-56 w-full rounded-xl bg-slate-950/80 border border-white/5 relative overflow-hidden">
              <SpatialCanvas />
              <div className="absolute bottom-3 left-3 text-[10px] font-mono text-slate-500 flex items-center gap-3">
                <span>P2P Latency Links: 38ms - 44ms</span>
                <span className="text-indigo-400">Topology: Vector Graph</span>
              </div>
            </div>
          </PerspectiveGlowBorder>

          {/* Consensus Engine Real-Time Protocol Event Feed */}
          <PerspectiveGlowBorder className="p-6 col-span-1 space-y-4">
            <div className="flex items-center justify-between font-mono text-xs">
              <span className="uppercase tracking-wider text-slate-400">Consensus Event Stream</span>
              <span className="text-emerald-400 text-[10px]">LIVE</span>
            </div>
            <div className="h-56 overflow-y-auto space-y-2 font-mono text-[11px] pr-1 scrollbar-thin">
              {logHistory.length === 0 ? (
                <div className="text-slate-600 text-xs py-8 text-center">No protocol events recorded.</div>
              ) : (
                logHistory.map((log, idx) => (
                  <div key={idx} className="p-2.5 rounded-lg bg-slate-950/60 border border-white/5 space-y-1">
                    <div className="flex items-center justify-between text-[10px]">
                      <span className="text-indigo-400 font-bold">{log.state}</span>
                      <span className="text-slate-600">
                        {new Date(log.timestamp).toLocaleTimeString()}
                      </span>
                    </div>
                    <p className="text-slate-300 text-[10px] leading-tight">{log.message}</p>
                  </div>
                ))
              )}
            </div>
          </PerspectiveGlowBorder>

        </div>

      </main>

      <footer className="border-t border-white/10 py-6 text-center text-xs text-slate-500 font-mono">
        <p>YieldForge Behavioral Software Telemetry — Consensus Engine Observable System</p>
      </footer>
    </div>
  );
}
