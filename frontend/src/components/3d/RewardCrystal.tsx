"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * RewardCrystal: Engineered crystal module receiving compound yield energy.
 */
export function RewardCrystal() {
  const crystalRef = useRef<THREE.Mesh>(null!);

  const crystalPos = new THREE.Vector3(3.4, 2.0, -1);
  const corePos = new THREE.Vector3(0, 0, 0);

  const lineGeometry = new THREE.BufferGeometry().setFromPoints([corePos, crystalPos]);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    if (crystalRef.current) {
      crystalRef.current.rotation.y = t * 0.6;
      crystalRef.current.rotation.x = Math.sin(t * 0.4) * 0.2;
      crystalRef.current.position.y = 2.0 + Math.sin(t * 1.5) * 0.18;
    }
  });

  return (
    <group>
      {/* Energy Conduit Line */}
      <primitive
        object={
          new THREE.Line(
            lineGeometry,
            new THREE.LineBasicMaterial({ color: "#38bdf8", transparent: true, opacity: 0.4 })
          )
        }
      />

      {/* Floating Reward Crystal */}
      <mesh ref={crystalRef} position={crystalPos}>
        <octahedronGeometry args={[0.7, 0]} />
        <meshStandardMaterial
          color="#14f195"
          emissive="#10b981"
          emissiveIntensity={0.8}
          roughness={0.1}
          metalness={0.9}
        />
      </mesh>

      <pointLight position={crystalPos} color="#14f195" distance={4} intensity={1.5} />
    </group>
  );
}
