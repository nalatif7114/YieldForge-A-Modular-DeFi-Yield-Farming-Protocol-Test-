"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * YieldCore: Engineered protocol machine core with dual-layer geometric precision.
 */
export function YieldCore() {
  const innerCoreRef = useRef<THREE.Mesh>(null!);
  const outerCageRef = useRef<THREE.Mesh>(null!);
  const lightRef = useRef<THREE.PointLight>(null!);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    // Precision rotations
    if (innerCoreRef.current) {
      innerCoreRef.current.rotation.x = t * 0.25;
      innerCoreRef.current.rotation.y = t * 0.4;
    }

    if (outerCageRef.current) {
      outerCageRef.current.rotation.x = -t * 0.15;
      outerCageRef.current.rotation.z = t * 0.2;
    }

    // Subtle rhythmic breath (engine pulse)
    const pulse = 1 + Math.sin(t * 1.8) * 0.04;
    if (innerCoreRef.current) {
      innerCoreRef.current.scale.set(pulse, pulse, pulse);
    }
    if (lightRef.current) {
      lightRef.current.intensity = 2.0 + Math.sin(t * 2) * 0.8;
    }
  });

  return (
    <group position={[0, 0, 0]}>
      {/* Inner Precision Geometry */}
      <mesh ref={innerCoreRef}>
        <octahedronGeometry args={[1.3, 0]} />
        <meshStandardMaterial
          color="#7c3aed"
          emissive="#6d28d9"
          emissiveIntensity={0.6}
          roughness={0.15}
          metalness={0.85}
        />
      </mesh>

      {/* Outer Structural Cage */}
      <mesh ref={outerCageRef}>
        <icosahedronGeometry args={[1.8, 1]} />
        <meshStandardMaterial
          color="#38bdf8"
          emissive="#0284c7"
          emissiveIntensity={0.25}
          wireframe={true}
          transparent={true}
          opacity={0.35}
        />
      </mesh>

      {/* Core Protocol Light */}
      <pointLight ref={lightRef} color="#7c3aed" distance={8} intensity={2.5} />
    </group>
  );
}
