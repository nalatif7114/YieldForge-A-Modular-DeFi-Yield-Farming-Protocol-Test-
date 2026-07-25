"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * EnergyRings: Engineered rotational conduits encircling the protocol core.
 */
export function EnergyRings() {
  const ring1Ref = useRef<THREE.Mesh>(null!);
  const ring2Ref = useRef<THREE.Mesh>(null!);
  const ring3Ref = useRef<THREE.Mesh>(null!);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    if (ring1Ref.current) {
      ring1Ref.current.rotation.x = Math.sin(t * 0.2) * 0.4;
      ring1Ref.current.rotation.y = t * 0.3;
    }
    if (ring2Ref.current) {
      ring2Ref.current.rotation.x = t * 0.25 + Math.PI / 3;
      ring2Ref.current.rotation.z = t * 0.2;
    }
    if (ring3Ref.current) {
      ring3Ref.current.rotation.y = -t * 0.35;
      ring3Ref.current.rotation.z = Math.cos(t * 0.3) * 0.4;
    }
  });

  return (
    <group position={[0, 0, 0]}>
      {/* Primary Violet Conduit Ring */}
      <mesh ref={ring1Ref}>
        <torusGeometry args={[2.4, 0.025, 16, 120]} />
        <meshStandardMaterial
          color="#7c3aed"
          emissive="#7c3aed"
          emissiveIntensity={0.8}
          roughness={0.2}
        />
      </mesh>

      {/* Secondary Sky Blue Conduit Ring */}
      <mesh ref={ring2Ref}>
        <torusGeometry args={[3.1, 0.02, 16, 120]} />
        <meshStandardMaterial
          color="#38bdf8"
          emissive="#0284c7"
          emissiveIntensity={0.6}
          roughness={0.2}
        />
      </mesh>

      {/* Outer Protocol Ring */}
      <mesh ref={ring3Ref}>
        <torusGeometry args={[3.7, 0.015, 16, 120]} />
        <meshStandardMaterial
          color="#6366f1"
          emissive="#4338ca"
          emissiveIntensity={0.4}
          roughness={0.3}
        />
      </mesh>
    </group>
  );
}
