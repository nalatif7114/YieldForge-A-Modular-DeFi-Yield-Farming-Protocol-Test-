"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

interface YieldEngineProps {
  speedMultiplier?: number;
}

/**
 * YieldEngine: Mechanical processing heart of the protocol.
 * Features nested mechanical gear rings, energy conduits, and an industrial rhythm.
 */
export function YieldEngine({ speedMultiplier = 1 }: YieldEngineProps) {
  const outerGearRef = useRef<THREE.Mesh>(null!);
  const innerGearRef = useRef<THREE.Mesh>(null!);
  const coreRef = useRef<THREE.Mesh>(null!);
  const lightRef = useRef<THREE.PointLight>(null!);

  useFrame((state, delta) => {
    const t = state.clock.getElapsedTime();
    const speed = delta * speedMultiplier;

    // Industrial counter-rotation hierarchy
    if (outerGearRef.current) {
      outerGearRef.current.rotation.z += speed * 0.4;
      outerGearRef.current.rotation.x = Math.sin(t * 0.5) * 0.15;
    }

    if (innerGearRef.current) {
      innerGearRef.current.rotation.z -= speed * 0.8;
      innerGearRef.current.rotation.y = Math.cos(t * 0.5) * 0.15;
    }

    if (coreRef.current) {
      coreRef.current.rotation.y += speed * 0.6;
      const pulse = 1 + Math.sin(t * 2.5) * 0.05;
      coreRef.current.scale.set(pulse, pulse, pulse);
    }

    if (lightRef.current) {
      lightRef.current.intensity = 2.5 + Math.sin(t * 3) * 1.0;
    }
  });

  return (
    <group position={[0, 0, 0]}>
      {/* Outer Mechanical Gear Ring */}
      <mesh ref={outerGearRef}>
        <torusGeometry args={[2.2, 0.08, 16, 60]} />
        <meshStandardMaterial
          color="#4f46e5"
          emissive="#3730a3"
          emissiveIntensity={0.6}
          roughness={0.2}
          metalness={0.9}
        />
      </mesh>

      {/* Inner Precision Gear Ring */}
      <mesh ref={innerGearRef}>
        <torusGeometry args={[1.5, 0.06, 16, 48]} />
        <meshStandardMaterial
          color="#7c3aed"
          emissive="#5b21b6"
          emissiveIntensity={0.8}
          roughness={0.15}
          metalness={0.85}
        />
      </mesh>

      {/* Processing Engine Core */}
      <mesh ref={coreRef}>
        <octahedronGeometry args={[0.9, 1]} />
        <meshStandardMaterial
          color="#7c3aed"
          emissive="#6d28d9"
          emissiveIntensity={1.2}
          roughness={0.1}
          metalness={0.95}
        />
      </mesh>

      <pointLight ref={lightRef} color="#7c3aed" distance={8} intensity={3} />
    </group>
  );
}
