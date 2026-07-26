"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * ValidatorMesh: 3 minimalist consensus nodes with gentle levitation.
 */
export function ValidatorMesh() {
  const nodesGroupRef = useRef<THREE.Group>(null!);
  const { isWalletConnected } = useProtocolStore();

  const nodePositions = useMemo(() => {
    return [
      new THREE.Vector3(-3.2, 1.2, 1.5),
      new THREE.Vector3(3.2, -1.0, 1.5),
      new THREE.Vector3(0, 2.8, -2.0),
    ];
  }, []);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    if (nodesGroupRef.current) {
      nodesGroupRef.current.children.forEach((child, i) => {
        child.position.y = nodePositions[i].y + Math.sin(t * 0.8 + i) * 0.06;
      });
    }
  });

  return (
    <group ref={nodesGroupRef}>
      {nodePositions.map((pos, idx) => (
        <group key={idx} position={pos}>
          <mesh>
            <sphereGeometry args={[0.15, 24, 24]} />
            <meshStandardMaterial
              color={isWalletConnected ? "#6366f1" : "#475569"}
              emissive={isWalletConnected ? "#4f46e5" : "#1e293b"}
              emissiveIntensity={isWalletConnected ? 0.8 : 0.2}
              roughness={0.1}
              metalness={0.9}
            />
          </mesh>
          <pointLight
            color="#6366f1"
            distance={2}
            intensity={isWalletConnected ? 0.8 : 0.2}
          />
        </group>
      ))}
    </group>
  );
}
