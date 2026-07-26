"use client";

import { useRef } from "react";
import { useFrame, useThree } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * PipelineCamera: Documentary observer camera tracking horizontal linear pipeline progress.
 */
export function PipelineCamera() {
  const { camera } = useThree();
  const { scrollProgress, mousePosition } = useProtocolStore();

  const targetPos = useRef(new THREE.Vector3(0, 1.8, 13.5));
  const targetLook = useRef(new THREE.Vector3(0, 0, 0));
  const currentLook = useRef(new THREE.Vector3(0, 0, 0));

  useFrame((_, delta) => {
    const p = scrollProgress;

    if (p < 0.25) {
      // Overview Stage: Macro view of full pipeline
      targetPos.current.set(0, 1.8, 13.5);
      targetLook.current.set(0, 0, 0);
    } else if (p < 0.5) {
      // Input Gateway Stage: Focus on transaction entry [x = -6]
      targetPos.current.set(-6, 1.2, 7.5);
      targetLook.current.set(-6, 0.8, 0);
    } else if (p < 0.75) {
      // Validator Mesh Stage: Focus on consensus nodes [x = -2]
      targetPos.current.set(-2, 0.6, 6.5);
      targetLook.current.set(-2, 0, 0);
    } else {
      // Compounding Engine & Reward Stage: Focus on yield pump [x = +4.5]
      targetPos.current.set(4.5, 1.2, 7.5);
      targetLook.current.set(4.5, 0, 0);
    }

    const parallaxX = mousePosition.x * 0.3;
    const parallaxY = mousePosition.y * 0.3;

    const finalPos = new THREE.Vector3(
      targetPos.current.x + parallaxX,
      targetPos.current.y + parallaxY,
      targetPos.current.z
    );

    camera.position.lerp(finalPos, delta * 3);
    currentLook.current.lerp(targetLook.current, delta * 3);
    camera.lookAt(currentLook.current);
  });

  return null;
}
