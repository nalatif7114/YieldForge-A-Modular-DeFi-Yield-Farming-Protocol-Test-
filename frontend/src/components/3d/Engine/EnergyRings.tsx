"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * EnergyRings: Minimalist, ultra-slow conduit rings encircling the core.
 */
export function EnergyRings() {
  const ring1Ref = useRef<THREE.Mesh>(null!);
  const ring2Ref = useRef<THREE.Mesh>(null!);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    if (ring1Ref.current) {
      ring1Ref.current.rotation.y = t * 0.04;
    }
    if (ring2Ref.current) {
      ring2Ref.current.rotation.x = Math.PI / 3;
      ring2Ref.current.rotation.z = -t * 0.03;
    }
  });

  return (
    <group position={[0, 0, 0]}>
      {/* Primary Ring */}
      <mesh ref={ring1Ref}>
        <torusGeometry args={[2.2, 0.015, 16, 100]} />
        <meshStandardMaterial
          color="#6366f1"
          emissive="#4338ca"
          emissiveIntensity={0.5}
          roughness={0.1}
        />
      </mesh>

      {/* Secondary Tilted Ring */}
      <mesh ref={ring2Ref}>
        <torusGeometry args={[2.8, 0.012, 16, 100]} />
        <meshStandardMaterial
          color="#38bdf8"
          emissive="#0284c7"
          emissiveIntensity={0.3}
          roughness={0.2}
        />
      </mesh>
    </group>
  );
}
