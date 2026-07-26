"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * PipelineYieldEngine: Mechanical compounding pump positioned at x = +2.5.
 * Interlocking gear rings compute block-by-block yield allocations.
 */
export function PipelineYieldEngine() {
  const outerGearRef = useRef<THREE.Mesh>(null!);
  const innerGearRef = useRef<THREE.Mesh>(null!);
  const { txState } = useProtocolStore();

  useFrame((_, delta) => {
    const isProcessing = txState === "staking" || txState === "claiming";
    const speed = delta * (isProcessing ? 1.8 : 0.6);

    if (outerGearRef.current) {
      outerGearRef.current.rotation.z += speed * 0.4;
    }
    if (innerGearRef.current) {
      innerGearRef.current.rotation.z -= speed * 0.8;
    }
  });

  return (
    <group position={[2.5, 0, 0]}>
      {/* Outer Interlocking Gear Ring */}
      <mesh ref={outerGearRef}>
        <torusGeometry args={[1.6, 0.06, 16, 60]} />
        <meshStandardMaterial
          color="#7c3aed"
          emissive="#5b21b6"
          emissiveIntensity={0.8}
          roughness={0.15}
          metalness={0.85}
        />
      </mesh>

      {/* Inner Mechanical Cylinder */}
      <mesh ref={innerGearRef}>
        <cylinderGeometry args={[0.9, 0.9, 1.2, 24]} />
        <meshStandardMaterial
          color="#6366f1"
          emissive="#4338ca"
          emissiveIntensity={0.5}
          wireframe={true}
          transparent={true}
          opacity={0.4}
        />
      </mesh>

      <pointLight color="#7c3aed" distance={5} intensity={2} />
    </group>
  );
}
