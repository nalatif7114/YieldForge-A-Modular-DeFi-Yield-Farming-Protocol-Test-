"use client";

import { useMemo } from "react";
import { EffectComposer, Bloom } from "@react-three/postprocessing";
import * as THREE from "three";
import { CinematicCamera } from "./CinematicCamera";
import { EnergyConduitMesh } from "./Shaders/EnergyConduitMaterial";
import { InputGateway } from "./InputGateway";
import { ValidatorMesh } from "./ValidatorMesh";
import { YieldEngine } from "./YieldEngine";
import { CompoundingChamber } from "./CompoundingChamber";
import { RewardGenerator } from "./RewardGenerator";

interface EngineSceneProps {
  stage?: number;
}

/**
 * EngineScene: Infinite 3D Control Room Composition.
 * Driven by documentary camera controller, custom shader conduits, and real-time Web3 state.
 */
export function EngineScene({ stage = 1 }: EngineSceneProps) {
  // Define custom shader conduit curve from gateway to core
  const conduitPoints = useMemo(() => {
    return [
      new THREE.Vector3(-8, 3, -3),
      new THREE.Vector3(-4, 1.5, -1),
      new THREE.Vector3(0, 0, 0),
    ];
  }, []);

  return (
    <>
      {/* Precision Camera Controller */}
      <CinematicCamera />

      {/* Atmospheric Lighting */}
      <ambientLight intensity={0.3} />
      <directionalLight position={[10, 15, 10]} intensity={1.4} color="#ffffff" />
      <directionalLight position={[-10, -10, -5]} intensity={0.7} color="#38bdf8" />
      <directionalLight position={[0, -10, 10]} intensity={0.5} color="#7c3aed" />

      {/* Custom Shader Energy Conduit */}
      <EnergyConduitMesh points={conduitPoints} />

      {/* Protocol Subsystems */}
      <InputGateway intensity={stage >= 2 ? 1.5 : 1} />
      <ValidatorMesh />
      <YieldEngine speedMultiplier={stage >= 4 ? 1.8 : 1} />
      <CompoundingChamber />
      <RewardGenerator />

      {/* Refined Postprocessing Bloom */}
      <EffectComposer>
        <Bloom
          intensity={0.65}
          luminanceThreshold={0.3}
          luminanceSmoothing={0.8}
        />
      </EffectComposer>
    </>
  );
}
