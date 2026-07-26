"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * RewardGenerator: Sleek, minimalist vertical extraction conduit.
 * Emerald (#22C55E) appears exclusively here when rewards are processed.
 */
export function RewardGenerator() {
  const beamRef = useRef<THREE.Mesh>(null!);
  const { txState, rewardEnergyPulse } = useProtocolStore();
  const isClaiming = txState === "claiming" || rewardEnergyPulse > 0;

  useFrame((state) => {
    const t = state.clock.getElapsedTime();
    if (beamRef.current) {
      const mat = beamRef.current.material as THREE.MeshStandardMaterial;
      mat.emissiveIntensity = isClaiming ? 1.8 : 0.4 + Math.sin(t * 1.5) * 0.2;
    }
  });

  return (
    <group position={[0, 0, 0]}>
      {/* Sleek Minimalist Conduit Beam */}
      <mesh ref={beamRef} position={[0, 2.2, 0]}>
        <cylinderGeometry args={[0.04, 0.04, 4.4, 16]} />
        <meshStandardMaterial
          color="#22c55e"
          emissive="#22c55e"
          emissiveIntensity={0.5}
          transparent
          opacity={0.3}
        />
      </mesh>

      {/* Top Reward Collector Node */}
      <mesh position={[0, 4.4, 0]}>
        <octahedronGeometry args={[0.3, 0]} />
        <meshStandardMaterial
          color="#22c55e"
          emissive="#22c55e"
          emissiveIntensity={isClaiming ? 2.0 : 0.8}
          roughness={0.1}
          metalness={0.9}
        />
      </mesh>
      <pointLight position={[0, 4.4, 0]} color="#22c55e" intensity={isClaiming ? 2.0 : 0.8} distance={4} />
    </group>
  );
}
