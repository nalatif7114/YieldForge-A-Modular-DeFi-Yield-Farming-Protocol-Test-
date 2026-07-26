"use client";

import { useRef, useEffect } from "react";
import { useThree, useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * 3D Node positions matching the Consensus Network topology.
 */
const NODE_3D_POSITIONS: Record<number, THREE.Vector3> = {
  1: new THREE.Vector3(-2.2, 0.8, 0.5),   // Node A - Primary Ingestion
  2: new THREE.Vector3(0.8, 1.6, -1.0),   // Node B - Relay East
  3: new THREE.Vector3(-0.5, -1.4, 0.8),  // Node C - Relay West
  4: new THREE.Vector3(2.5, -0.2, -0.4),  // Node D - State Commit Finalizer
};

/**
 * CinematicConsensusCamera:
 * 
 * Observing Camera Mechanics:
 * - When a packet moves, the camera slightly shifts, tracking the packet by ~2%.
 * - Smoothly returns afterwards.
 * - NEVER chases the packet.
 * - NEVER creates cinematic fly-throughs.
 * - The camera is observing, not following.
 */
export function CinematicConsensusCamera() {
  const { camera } = useThree();
  const { scrollProgress, mousePosition } = useProtocolStore();
  const { state, activeNodes } = useConsensusEngine();

  // Target vectors for observing camera position and lookAt point
  const targetCamPos = useRef(new THREE.Vector3(0, 1.2, 7.5));
  const targetLookAt = useRef(new THREE.Vector3(0, 0, 0));
  const currentLookAt = useRef(new THREE.Vector3(0, 0, 0));

  // Inertial mouse offset vector for subtle mouse parallax
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
    // 1. Inertial Mouse Movement & Parallax (Subtle observing perspective)
    mouseInertia.current.x = THREE.MathUtils.damp(
      mouseInertia.current.x,
      mousePosition.x,
      3,
      delta
    );
    mouseInertia.current.y = THREE.MathUtils.damp(
      mouseInertia.current.y,
      mousePosition.y,
      3,
      delta
    );

    const parallaxX = mouseInertia.current.x * 0.4;
    const parallaxY = mouseInertia.current.y * 0.3;

    // 2. Scroll Angle Reveal (Observing vantage point shift)
    const scrollAngle = scrollProgress * Math.PI * 0.3;
    const scrollElevation = scrollProgress * 1.5;

    // Fixed observing camera position (never flies through!)
    const baseCamPos = new THREE.Vector3(
      Math.sin(scrollAngle) * 7.5 + parallaxX,
      1.2 + scrollElevation + parallaxY,
      Math.cos(scrollAngle) * 7.5
    );

    // 3. Packet Tracking by ~2% (Micro-shift observing mechanics)
    const baseLookAt = new THREE.Vector3(0, 0, 0);
    let activePacketPoint = new THREE.Vector3(0, 0, 0);

    if (state === "TRANSACTION_RECEIVED" || state === "VALIDATING") {
      activePacketPoint.copy(NODE_3D_POSITIONS[1]);
    } else if (state === "PROPAGATING") {
      // Mid-point between relay nodes B & C
      activePacketPoint
        .addVectors(NODE_3D_POSITIONS[2], NODE_3D_POSITIONS[3])
        .multiplyScalar(0.5);
    } else if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED") {
      activePacketPoint.copy(NODE_3D_POSITIONS[4]);
    }

    // Shift camera lookAt target by EXACTLY ~2% towards the active packet
    // targetLookAt = baseLookAt + (activePacketPoint - baseLookAt) * 0.02
    const trackingVector = activePacketPoint.clone().sub(baseLookAt).multiplyScalar(0.02);
    targetLookAt.current.copy(baseLookAt).add(trackingVector);

    targetCamPos.current.copy(baseCamPos);

    // 4. Smooth Damping (Observing easing)
    const dampSpeed = 3.0;

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
