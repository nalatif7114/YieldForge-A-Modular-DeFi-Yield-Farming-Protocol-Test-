"use client";

import { useRef } from "react";
import { useFrame, useThree } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";

/**
 * CinematicCamera: Documentary-style camera controller.
 * Smoothly interpolates camera position and lookAt target along a timeline driven by Lenis scroll progress and subtle mouse parallax.
 */
export function CinematicCamera() {
  const { camera } = useThree();
  const { scrollProgress, mousePosition } = useProtocolStore();

  const targetPos = useRef(new THREE.Vector3(0, 1, 10.5));
  const targetLook = useRef(new THREE.Vector3(0, 0, 0));
  const currentLook = useRef(new THREE.Vector3(0, 0, 0));

  useFrame((_, delta) => {
    const p = scrollProgress;

    // Define 5 keyframe vectors based on scroll journey
    if (p < 0.25) {
      // Stage 1: Macro Protocol View
      targetPos.current.set(0, 1.2, 10.5);
      targetLook.current.set(0, 0, 0);
    } else if (p < 0.5) {
      // Stage 2: Input Gateway Focus
      targetPos.current.set(-4.5, 2.2, 7.5);
      targetLook.current.set(-2, 0.5, -0.5);
    } else if (p < 0.75) {
      // Stage 3: Validator Mesh & Core Focus
      targetPos.current.set(3.8, -1.2, 6.8);
      targetLook.current.set(0, 0, 0);
    } else {
      // Stage 4: Reward Generator Focus
      targetPos.current.set(0, 3.5, 7.5);
      targetLook.current.set(0, 2.5, 0);
    }

    // Add subtle mouse parallax offset
    const parallaxX = mousePosition.x * 0.4;
    const parallaxY = mousePosition.y * 0.4;

    const finalPos = new THREE.Vector3(
      targetPos.current.x + parallaxX,
      targetPos.current.y + parallaxY,
      targetPos.current.z
    );

    // Smooth damp camera position and target
    camera.position.lerp(finalPos, delta * 3);
    currentLook.current.lerp(targetLook.current, delta * 3);
    camera.lookAt(currentLook.current);
  });

  return null;
}
