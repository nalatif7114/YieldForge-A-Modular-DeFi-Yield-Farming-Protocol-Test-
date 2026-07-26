"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * YieldCore: Calm, precision-engineered protocol core.
 * Features slow, subtle rotation and metallic craftsmanship.
 */
export function YieldCore() {
  const coreRef = useRef<THREE.Mesh>(null!);
  const outerRef = useRef<THREE.Mesh>(null!);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    // Micro-slow, calm rotations
    if (coreRef.current) {
      coreRef.current.rotation.y = t * 0.05;
      coreRef.current.rotation.x = Math.sin(t * 0.03) * 0.08;
    }

    if (outerRef.current) {
      outerRef.current.rotation.y = -t * 0.03;
    }
  });

  return (
    <group position={[0, 0, 0]}>
      {/* Inner Precision Core */}
      <mesh ref={coreRef}>
        <octahedronGeometry args={[1.2, 0]} />
        <meshStandardMaterial
          color="#6366f1"
          emissive="#4f46e5"
          emissiveIntensity={0.4}
          roughness={0.1}
          metalness={0.9}
        />
      </mesh>

      {/* Outer Structural Outline */}
      <mesh ref={outerRef}>
        <octahedronGeometry args={[1.5, 0]} />
        <meshStandardMaterial
          color="#38bdf8"
          emissive="#0284c7"
          emissiveIntensity={0.2}
          wireframe={true}
          transparent={true}
          opacity={0.25}
        />
      </mesh>

      <pointLight color="#6366f1" distance={6} intensity={1.5} />
    </group>
  );
}
