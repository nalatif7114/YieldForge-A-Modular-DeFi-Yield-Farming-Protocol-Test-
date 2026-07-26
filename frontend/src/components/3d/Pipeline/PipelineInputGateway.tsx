"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * PipelineInputGateway: Linear entry conduit extending from x = -9 to x = -4.5.
 * Data capsules travel along a Catmull-Rom spline into the Gateway Receiver Node.
 * Represents incoming user token deposit transactions.
 */
export function PipelineInputGateway() {
  const capsulesRef = useRef<THREE.InstancedMesh>(null!);
  const { isWalletConnected, txState } = useProtocolStore();
  const count = 12;

  // Spline curve extending horizontally along negative X axis
  const curve = useMemo(() => {
    return new THREE.CatmullRomCurve3([
      new THREE.Vector3(-9, 2.5, -2),
      new THREE.Vector3(-7, 1.8, -1),
      new THREE.Vector3(-5.5, 1.1, -0.5),
      new THREE.Vector3(-4.5, 0.8, 0),
    ]);
  }, []);

  const dummy = useMemo(() => new THREE.Object3D(), []);

  const progressArr = useMemo(() => {
    return Array.from({ length: count }).map((_, i) => i / count);
  }, [count]);

  useFrame((_, delta) => {
    if (!capsulesRef.current) return;

    let speedFactor = isWalletConnected ? 0.35 : 0.15;
    if (txState === "staking") {
      speedFactor = 0.85; // Accelerated transaction stream when user stakes
    }

    for (let i = 0; i < count; i++) {
      progressArr[i] = (progressArr[i] + delta * speedFactor) % 1;
      const point = curve.getPoint(progressArr[i]);
      const tangent = curve.getTangent(progressArr[i]);

      dummy.position.copy(point);
      dummy.lookAt(point.clone().add(tangent));
      dummy.scale.set(0.1, 0.1, 0.22);
      dummy.updateMatrix();

      capsulesRef.current.setMatrixAt(i, dummy.matrix);
    }
    capsulesRef.current.instanceMatrix.needsUpdate = true;
  });

  return (
    <group>
      {/* Spline Tube Conduit */}
      <primitive
        object={
          new THREE.Line(
            new THREE.BufferGeometry().setFromPoints(curve.getPoints(50)),
            new THREE.LineBasicMaterial({
              color: isWalletConnected ? "#38bdf8" : "#334155",
              transparent: true,
              opacity: isWalletConnected ? 0.4 : 0.15,
            })
          )
        }
      />

      {/* Gateway Receiver Node (x = -4.5) */}
      <mesh position={[-4.5, 0.8, 0]}>
        <cylinderGeometry args={[0.3, 0.3, 0.6, 16]} />
        <meshStandardMaterial
          color="#38bdf8"
          emissive="#0284c7"
          emissiveIntensity={isWalletConnected ? 0.8 : 0.2}
          roughness={0.2}
          metalness={0.8}
        />
      </mesh>

      {/* Instanced Transaction Capsules */}
      <instancedMesh ref={capsulesRef} args={[undefined, undefined, count]}>
        <boxGeometry args={[1, 1, 1]} />
        <meshStandardMaterial
          color={txState === "staking" ? "#00f0ff" : "#38bdf8"}
          emissive={txState === "staking" ? "#00f0ff" : "#0284c7"}
          emissiveIntensity={txState === "staking" ? 2.5 : 1.2}
          roughness={0.2}
        />
      </instancedMesh>
    </group>
  );
}
