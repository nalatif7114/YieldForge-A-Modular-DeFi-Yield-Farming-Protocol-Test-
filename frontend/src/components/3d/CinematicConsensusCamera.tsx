"use client";

import { useRef, useEffect } from "react";
import { useThree, useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * 3D Node positions matching the Project Helios Validator Topology.
 */
const HELIOS_NODE_POSITIONS: Record<number, THREE.Vector3> = {
  1: new THREE.Vector3(-2.2, 0.8, 0.5),   // Node A - Primary Ingestion
  2: new THREE.Vector3(0.8, 1.6, -1.0),   // Node B - Relay East
  3: new THREE.Vector3(-0.5, -1.4, 0.8),  // Node C - Relay West
  4: new THREE.Vector3(2.5, -0.2, -0.4),  // Node D - State Commit Finalizer
};

/**
 * Phase 1: CinematicConsensusCamera
 * 
 * Motion Reference: Inspired by 3D Gallery camera motion mechanics.
 * Extracts:
 * - Exponential smooth damping (frame-rate independent)
 * - Inertial mouse parallax (restrained < 3 deg tilt, Apple Vision Pro / Spline quality)
 * - Soft perspective shift
 * - Slight orbital movement based on scroll
 * - 2% micro-shift tracking towards active consensus packet
 * 
 * Rules:
 * - NEVER rotate continuously
 * - NEVER spin the network
 * - Camera owns ALL movement
 */
export function CinematicConsensusCamera() {
  const { camera } = useThree();
  const { scrollProgress, mousePosition } = useProtocolStore();
  const { state } = useConsensusEngine();

  // Target vectors for camera position and focus target
  const targetCamPos = useRef(new THREE.Vector3(0, 1.2, 7.5));
  const targetLookAt = useRef(new THREE.Vector3(0, 0, 0));
  const currentLookAt = useRef(new THREE.Vector3(0, 0, 0));

  // Inertial mouse offset vector for restrained parallax (< 3 deg)
  const mouseInertia = useRef(new THREE.Vector2(0, 0));

  useEffect(() => {
    if (camera instanceof THREE.PerspectiveCamera) {
      camera.fov = 45;
      camera.near = 0.1;
      camera.far = 100;
      camera.updateProjectionMatrix();
    }
  }, [camera]);

  useFrame((_, delta) => {
    // 1. Inertial Mouse Parallax (Restrained to max ~0.35 unit offset = ~2.5 deg tilt)
    mouseInertia.current.x = THREE.MathUtils.damp(
      mouseInertia.current.x,
      mousePosition.x,
      3.5,
      delta
    );
    mouseInertia.current.y = THREE.MathUtils.damp(
      mouseInertia.current.y,
      mousePosition.y,
      3.5,
      delta
    );

    const parallaxX = mouseInertia.current.x * 0.35;
    const parallaxY = mouseInertia.current.y * 0.25;

    // 2. Scroll Angle Reveal (Subtle orbital arc)
    const scrollAngle = scrollProgress * Math.PI * 0.25;
    const scrollElevation = scrollProgress * 1.2;

    // Base observing camera position
    const baseCamPos = new THREE.Vector3(
      Math.sin(scrollAngle) * 7.5 + parallaxX,
      1.2 + scrollElevation + parallaxY,
      Math.cos(scrollAngle) * 7.5
    );

    // 3. Packet Micro-Shift Tracking (~2% shift, observing perspective)
    const baseLookAt = new THREE.Vector3(0, 0, 0);
    let activePacketPoint = new THREE.Vector3(0, 0, 0);

    if (state === "TRANSACTION_RECEIVED" || state === "VALIDATING") {
      activePacketPoint.copy(HELIOS_NODE_POSITIONS[1]);
    } else if (state === "PROPAGATING") {
      activePacketPoint
        .addVectors(HELIOS_NODE_POSITIONS[2], HELIOS_NODE_POSITIONS[3])
        .multiplyScalar(0.5);
    } else if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED") {
      activePacketPoint.copy(HELIOS_NODE_POSITIONS[4]);
    }

    // Shift lookAt target by EXACTLY ~2% towards the active packet
    const trackingVector = activePacketPoint.clone().sub(baseLookAt).multiplyScalar(0.02);
    targetLookAt.current.copy(baseLookAt).add(trackingVector);

    targetCamPos.current.copy(baseCamPos);

    // 4. Smooth Damping (Frame-rate independent exponential damping)
    const dampSpeed = 3.2;

    camera.position.x = THREE.MathUtils.damp(
      camera.position.x,
      targetCamPos.current.x,
      dampSpeed,
      delta
    );
    camera.position.y = THREE.MathUtils.damp(
      camera.position.y,
      targetCamPos.current.y,
      dampSpeed,
      delta
    );
    camera.position.z = THREE.MathUtils.damp(
      camera.position.z,
      targetCamPos.current.z,
      dampSpeed,
      delta
    );

    currentLookAt.current.x = THREE.MathUtils.damp(
      currentLookAt.current.x,
      targetLookAt.current.x,
      dampSpeed,
      delta
    );
    currentLookAt.current.y = THREE.MathUtils.damp(
      currentLookAt.current.y,
      targetLookAt.current.y,
      dampSpeed,
      delta
    );
    currentLookAt.current.z = THREE.MathUtils.damp(
      currentLookAt.current.z,
      targetLookAt.current.z,
      dampSpeed,
      delta
    );

    // Camera observes network from stationary vantage point with micro 2% tracking shift
    camera.lookAt(currentLookAt.current);
  });

  return null;
}
