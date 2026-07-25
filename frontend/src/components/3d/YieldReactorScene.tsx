"use client";

import { OrbitControls } from "@react-three/drei";
import { EffectComposer, Bloom } from "@react-three/postprocessing";
import { YieldCore } from "./YieldCore";
import { EnergyRings } from "./EnergyRings";
import { ValidatorNodes } from "./ValidatorNodes";
import { RewardCrystal } from "./RewardCrystal";
import { TransactionParticles } from "./TransactionParticles";

/**
 * YieldReactorScene: Engineered 3D protocol scene composition with controlled lighting and bloom.
 */
export function YieldReactorScene() {
  return (
    <>
      {/* Lighting Setup */}
      <ambientLight intensity={0.35} />
      <directionalLight position={[10, 15, 10]} intensity={1.2} color="#ffffff" />
      <directionalLight position={[-10, -10, -5]} intensity={0.6} color="#38bdf8" />

      {/* Orbit Controls with Damping for Smooth Movement */}
      <OrbitControls
        enableZoom={false}
        enablePan={false}
        autoRotate={true}
        autoRotateSpeed={0.5}
        maxPolarAngle={Math.PI / 1.8}
        minPolarAngle={Math.PI / 2.4}
      />

      {/* 3D Mechanical Assemblies */}
      <YieldCore />
      <EnergyRings />
      <ValidatorNodes />
      <RewardCrystal />
      <TransactionParticles count={180} />

      {/* Subtle Refined Bloom */}
      <EffectComposer>
        <Bloom
          intensity={0.7}
          luminanceThreshold={0.3}
          luminanceSmoothing={0.8}
        />
      </EffectComposer>
    </>
  );
}
