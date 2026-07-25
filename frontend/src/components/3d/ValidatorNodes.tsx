"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * ValidatorNodes: Orbiting consensus validator nodes representing decentralized network activity.
 */
export function ValidatorNodes() {
  const groupRef = useRef<THREE.Group>(null!);

  const nodes = useMemo(() => {
    return Array.from({ length: 4 }).map((_, i) => {
      const radius = 4.4 + (i % 2) * 0.5;
      const speed = 0.3 + i * 0.08;
      const initialAngle = (i * Math.PI * 2) / 4;
      const tilt = (i - 1.5) * 0.25;
      return { radius, speed, initialAngle, tilt, id: i };
    });
  }, []);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();

    if (groupRef.current) {
      groupRef.current.children.forEach((child, idx) => {
        const node = nodes[idx];
        if (node) {
          const angle = node.initialAngle + t * node.speed;
          child.position.x = Math.cos(angle) * node.radius;
          child.position.z = Math.sin(angle) * node.radius;
          child.position.y = Math.sin(angle * 1.5) * node.tilt;
        }
      });
    }
  });

  return (
    <group ref={groupRef}>
      {nodes.map((node) => (
        <group key={node.id}>
          <mesh>
            <sphereGeometry args={[0.18, 24, 24]} />
            <meshStandardMaterial
              color="#38bdf8"
              emissive="#0284c7"
              emissiveIntensity={1.0}
              roughness={0.15}
              metalness={0.8}
            />
          </mesh>
          <pointLight color="#38bdf8" intensity={0.8} distance={2.5} />
        </group>
      ))}
    </group>
  );
}
