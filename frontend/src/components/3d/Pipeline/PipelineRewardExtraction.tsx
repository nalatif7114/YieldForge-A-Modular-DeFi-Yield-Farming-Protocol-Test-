"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * PipelineRewardExtraction: Reward distribution module positioned at x = +7.0.
 * Vertical beam remains DORMANT (opacity 0.15) during normal operations.
 * Illuminates in bright Emerald Green (#22C55E) ONLY when rewards are successfully claimed.
 */
export function PipelineRewardExtraction() {
  const beamRef = useRef<THREE.Mesh>(null!);
  const packetsRef = useRef<THREE.InstancedMesh>(null!);
  const { txState, rewardEnergyPulse } = useProtocolStore();

  const isClaiming = txState === "claiming" || rewardEnergyPulse > 0;
  const count = 6;

  // Exit conduit spline curve leading back toward wallet vector [8.5, 2.5, 0]
  const exitCurve = useMemo(() => {
    return new THREE.CatmullRomCurve3([
      new THREE.Vector3(7.0, 0, 0),
      new THREE.Vector3(7.5, 1.2, 0.5),
      new THREE.Vector3(8.5, 2.5, 0),
    ]);
  }, []);

  const dummy = useMemo(() => new THREE.Object3D(), []);

  const progressArr = useMemo(() => {
    return Array.from({ length: count }).map((_, i) => i / count);
  }, [count]);

  useFrame((_, delta) => {
    if (beamRef.current) {
      const mat = beamRef.current.material as THREE.MeshStandardMaterial;
      mat.opacity = isClaiming ? 0.8 : 0.12;
      mat.emissiveIntensity = isClaiming ? 2.5 : 0.3;
    }

    if (packetsRef.current) {
      if (isClaiming) {
        for (let i = 0; i < count; i++) {
          progressArr[i] = (progressArr[i] + delta * 0.8) % 1;
          const point = exitCurve.getPoint(progressArr[i]);
          dummy.position.copy(point);
          dummy.scale.set(0.18, 0.18, 0.18);
          dummy.updateMatrix();
          packetsRef.current.setMatrixAt(i, dummy.matrix);
        }
        packetsRef.current.instanceMatrix.needsUpdate = true;
      } else {
        // Hide packets when dormant
        for (let i = 0; i < count; i++) {
          dummy.scale.set(0, 0, 0);
          dummy.updateMatrix();
          packetsRef.current.setMatrixAt(i, dummy.matrix);
        }
        packetsRef.current.instanceMatrix.needsUpdate = true;
      }
    }
  });

  return (
    <group position={[7.0, 0, 0]}>
      {/* Reward Vault Collector Node */}
      <mesh position={[0, 0, 0]}>
        <boxGeometry args={[0.8, 0.8, 0.8]} />
        <meshStandardMaterial
          color={isClaiming ? "#22c55e" : "#334155"}
          emissive={isClaiming ? "#22c55e" : "#1e293b"}
          emissiveIntensity={isClaiming ? 2.0 : 0.2}
          roughness={0.1}
          metalness={0.9}
        />
      </mesh>

      {/* Vertical Extraction Beam (Dormant by default) */}
      <mesh ref={beamRef} position={[0, 2.0, 0]}>
        <cylinderGeometry args={[0.08, 0.08, 4, 16]} />
        <meshStandardMaterial
          color="#22c55e"
          emissive="#22c55e"
          emissiveIntensity={0.3}
          transparent
          opacity={0.12}
        />
      </mesh>

      {/* Exit Conduit Spline */}
      <primitive
        object={
          new THREE.Line(
            new THREE.BufferGeometry().setFromPoints(exitCurve.getPoints(30)),
            new THREE.LineBasicMaterial({
              color: "#22c55e",
              transparent: true,
              opacity: isClaiming ? 0.6 : 0.1,
            })
          )
        }
      />

      {/* Reward Packets (Fires ONLY on harvest) */}
      <instancedMesh ref={packetsRef} args={[undefined, undefined, count]}>
        <octahedronGeometry args={[1, 0]} />
        <meshStandardMaterial
          color="#22c55e"
          emissive="#4ade80"
          emissiveIntensity={3.0}
          roughness={0.1}
        />
      </instancedMesh>

      <pointLight color="#22c55e" intensity={isClaiming ? 3.0 : 0.4} distance={4} />
    </group>
  );
}
