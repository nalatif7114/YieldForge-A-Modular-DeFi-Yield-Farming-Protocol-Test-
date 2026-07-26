"use client";

import { useRef } from "react";
import { useFrame, useThree } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

interface MotionCameraRigProps {
  targetPosition?: [number, number, number];
  lookAtTarget?: [number, number, number];
}

/**
 * MotionCameraRig: Damped Three.js camera observer rig.
 * Uses critically damped vector interpolation (lerp factor 3.0) for documentary camera tracking.
 */
export function MotionCameraRig({
  targetPosition = [0, 1.8, 13.5],
  lookAtTarget = [0, 0, 0],
}: MotionCameraRigProps) {
  const { camera } = useThree();
  const { mousePosition } = useProtocolStore();

  const currentLook = useRef(new THREE.Vector3(...lookAtTarget));

  useFrame((_, delta) => {
    const parallaxX = mousePosition.x * 0.3;
    const parallaxY = mousePosition.y * 0.3;

    const finalPos = new THREE.Vector3(
      targetPosition[0] + parallaxX,
      targetPosition[1] + parallaxY,
      targetPosition[2]
    );

    camera.position.lerp(finalPos, delta * 3.0);
    currentLook.current.lerp(new THREE.Vector3(...lookAtTarget), delta * 3.0);
    camera.lookAt(currentLook.current);
  });

  return null;
}
