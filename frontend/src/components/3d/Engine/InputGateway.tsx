"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

interface InputGatewayProps {
  intensity?: number;
}

/**
 * InputGateway: Stream of data capsules continuously entering the protocol engine along a spline curve.
 * Reacts to on-chain state: Wallet connection wakes up flow; Staking transaction events accelerate packet entry.
 */
export function InputGateway({ intensity = 1 }: InputGatewayProps) {
  const capsulesRef = useRef<THREE.InstancedMesh>(null!);
  const { isWalletConnected, txState } = useProtocolStore();
  const count = 16;

  // Define curve from entry vector [-8, 3, -3] into core [0, 0, 0]
  const curve = useMemo(() => {
    return new THREE.CatmullRomCurve3([
      new THREE.Vector3(-8, 3, -3),
      new THREE.Vector3(-5, 2, -1),
      new THREE.Vector3(-2, 0.5, -0.5),
      new THREE.Vector3(0, 0, 0),
    ]);
  }, []);

  const dummy = useMemo(() => new THREE.Object3D(), []);

  const progressArr = useMemo(() => {
    return Array.from({ length: count }).map((_, i) => i / count);
  }, [count]);

  useFrame((state, delta) => {
    if (!capsulesRef.current) return;

    // Base speed driven by wallet connection & active staking event
    let speedFactor = isWalletConnected ? 0.4 : 0.15;
    if (txState === "staking") {
      speedFactor = 0.9; // Fast transaction stream when user stakes!
    }

    for (let i = 0; i < count; i++) {
      progressArr[i] = (progressArr[i] + delta * speedFactor * intensity) % 1;
      const point = curve.getPoint(progressArr[i]);
      const tangent = curve.getTangent(progressArr[i]);

      dummy.position.copy(point);
      dummy.lookAt(point.clone().add(tangent));
      dummy.scale.set(0.12, 0.12, 0.25);
      dummy.updateMatrix();

      capsulesRef.current.setMatrixAt(i, dummy.matrix);
    }
    capsulesRef.current.instanceMatrix.needsUpdate = true;
  });

  return (
    <group>
      {/* Visual Guide Line for Input Conduit */}
      <primitive
        object={
          new THREE.Line(
            new THREE.BufferGeometry().setFromPoints(curve.getPoints(50)),
            new THREE.LineBasicMaterial({
              color: isWalletConnected ? "#38bdf8" : "#475569",
              transparent: true,
              opacity: isWalletConnected ? 0.4 : 0.15,
            })
          )
        }
      />

      {/* Instanced Data Capsules */}
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
