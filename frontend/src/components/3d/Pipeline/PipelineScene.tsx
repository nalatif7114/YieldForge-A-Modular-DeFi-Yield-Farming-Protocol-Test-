"use client";

import { EffectComposer, Bloom } from "@react-three/postprocessing";
import { PipelineCamera } from "./PipelineCamera";
import { PipelineInputGateway } from "./PipelineInputGateway";
import { PipelineValidatorMesh } from "./PipelineValidatorMesh";
import { PipelineYieldEngine } from "./PipelineYieldEngine";
import { PipelineRewardExtraction } from "./PipelineRewardExtraction";

export function PipelineScene() {
  return (
    <>
      {/* Precision Documentary Camera */}
      <PipelineCamera />

      {/* Atmospheric Industrial Lighting */}
      <ambientLight intensity={0.25} />
      <directionalLight position={[10, 15, 10]} intensity={1.2} color="#ffffff" />
      <directionalLight position={[-10, -10, -5]} intensity={0.6} color="#38bdf8" />
      <directionalLight position={[5, -10, 10]} intensity={0.5} color="#7c3aed" />

      {/* 4 Linear Machine Subsystems Spanning X = -9 to X = +8 */}
      <PipelineInputGateway />
      <PipelineValidatorMesh />
      <PipelineYieldEngine />
      <PipelineRewardExtraction />

      {/* Refined Postprocessing Bloom */}
      <EffectComposer>
        <Bloom
          intensity={0.6}
          luminanceThreshold={0.3}
          luminanceSmoothing={0.8}
        />
      </EffectComposer>
    </>
  );
}
