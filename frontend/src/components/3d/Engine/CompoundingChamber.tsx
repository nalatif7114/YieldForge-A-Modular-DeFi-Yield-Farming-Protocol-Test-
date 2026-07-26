"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

/**
 * CompoundingChamber: 30 subtle ambient points drifting in a gentle spiral.
 * Maximizes rendering performance for 120 FPS target.
 */
export function CompoundingChamber() {
  const pointsRef = useRef<THREE.Points>(null!);
  const count = 30;

  const [positions, angles, radii, heights] = useMemo(() => {
    const pos = new Float32Array(count * 3);
    const ang = new Float32Array(count);
    const rad = new Float32Array(count);
    const hts = new Float32Array(count);

    for (let i = 0; i < count; i++) {
      ang[i] = Math.random() * Math.PI * 2;
      rad[i] = 1.2 + Math.random() * 2.5;
      hts[i] = (Math.random() - 0.5) * 2;

      pos[i * 3] = Math.cos(ang[i]) * rad[i];
      pos[i * 3 + 1] = hts[i];
      pos[i * 3 + 2] = Math.sin(ang[i]) * rad[i];
    }

    return [pos, ang, rad, hts];
  }, [count]);

  useFrame((_, delta) => {
    if (!pointsRef.current) return;
    const posAttr = pointsRef.current.geometry.attributes.position as THREE.BufferAttribute;
    const array = posAttr.array as Float32Array;

    for (let i = 0; i < count; i++) {
      angles[i] += delta * 0.2;
      array[i * 3] = Math.cos(angles[i]) * radii[i];
      array[i * 3 + 2] = Math.sin(angles[i]) * radii[i];
    }
    posAttr.needsUpdate = true;
  });

  return (
    <points ref={pointsRef}>
      <bufferGeometry>
        <bufferAttribute attach="attributes-position" args={[positions, 3]} />
      </bufferGeometry>
      <pointsMaterial
        size={0.04}
        color="#38bdf8"
        transparent
        opacity={0.4}
        blending={THREE.AdditiveBlending}
      />
    </points>
  );
}
