"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * PipelineValidatorMesh: Linear consensus network positioned at x = -2.
 * 4 vertical pillar nodes exchanging proof verification data rays.
 */
export function PipelineValidatorMesh() {
  const groupRef = useRef<THREE.Group>(null!);
  const linesRef = useRef<THREE.LineSegments>(null!);
  const { isWalletConnected } = useProtocolStore();

  const nodePositions = useMemo(() => {
    return [
      new THREE.Vector3(-2.8, 1.4, 0.5),
      new THREE.Vector3(-2.8, -1.4, 0.5),
      new THREE.Vector3(-1.4, 1.4, -0.5),
      new THREE.Vector3(-1.4, -1.4, -0.5),
    ];
  }, []);

  const linePositions = useMemo(() => {
    const points: number[] = [];
    for (let i = 0; i < nodePositions.length; i++) {
      for (let j = i + 1; j < nodePositions.length; j++) {
        points.push(
          nodePositions[i].x, nodePositions[i].y, nodePositions[i].z,
          nodePositions[j].x, nodePositions[j].y, nodePositions[j].z
        );
      }
    }
    return new Float32Array(points);
  }, [nodePositions]);

  useFrame((state) => {
    const t = state.clock.getElapsedTime();
    const pulseSpeed = isWalletConnected ? 2.2 : 0.8;

    if (groupRef.current) {
      groupRef.current.children.forEach((child, i) => {
        child.position.y = nodePositions[i].y + Math.sin(t * pulseSpeed + i) * 0.08;
      });
    }

    if (linesRef.current) {
      const mat = linesRef.current.material as THREE.LineBasicMaterial;
      const baseOpacity = isWalletConnected ? 0.35 : 0.12;
      mat.opacity = baseOpacity + Math.abs(Math.sin(t * pulseSpeed)) * (isWalletConnected ? 0.45 : 0.1);
    }
  });

  return (
    <group>
      {/* Network Proof Rays */}
      <lineSegments ref={linesRef}>
        <bufferGeometry>
          <bufferAttribute attach="attributes-position" args={[linePositions, 3]} />
        </bufferGeometry>
        <lineBasicMaterial
          color={isWalletConnected ? "#4f46e5" : "#334155"}
          transparent
          opacity={0.25}
        />
      </lineSegments>

      {/* 4 Validator Pillar Nodes */}
      <group ref={groupRef}>
        {nodePositions.map((pos, idx) => (
          <group key={idx} position={pos}>
            <mesh>
              <cylinderGeometry args={[0.16, 0.16, 0.8, 16]} />
              <meshStandardMaterial
                color={isWalletConnected ? "#4f46e5" : "#475569"}
                emissive={isWalletConnected ? "#4338ca" : "#1e293b"}
                emissiveIntensity={isWalletConnected ? 1.2 : 0.3}
                roughness={0.15}
                metalness={0.85}
              />
            </mesh>
            <pointLight
              color={isWalletConnected ? "#4f46e5" : "#475569"}
              distance={2.5}
              intensity={isWalletConnected ? 1.2 : 0.3}
            />
          </group>
        ))}
      </group>
    </group>
  );
}
