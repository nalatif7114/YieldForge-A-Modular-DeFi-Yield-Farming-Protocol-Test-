"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * SpatialNodeMesh: Spatial graph visualizer for RPC Consensus Nodes.
 * Subscribes directly to ConsensusEngine to visualize node state, wave propagation,
 * and consensus lock in lockstep with the protocol event bus.
 */
export function SpatialNodeMesh() {
  const groupRef = useRef<THREE.Group>(null!);
  const linesRef = useRef<THREE.LineSegments>(null!);
  const { isWalletConnected } = useProtocolStore();
  const { state, activeNodes, isProcessing } = useConsensusEngine();

  const nodePositions = useMemo(() => {
    return [
      new THREE.Vector3(-2.2, 0.8, 0),
      new THREE.Vector3(2.2, 0.8, 0),
      new THREE.Vector3(0, -1.2, 0),
    ];
  }, []);

  const linePositions = useMemo(() => {
    const points: number[] = [
      nodePositions[0].x, nodePositions[0].y, nodePositions[0].z,
      nodePositions[1].x, nodePositions[1].y, nodePositions[1].z,

      nodePositions[1].x, nodePositions[1].y, nodePositions[1].z,
      nodePositions[2].x, nodePositions[2].y, nodePositions[2].z,

      nodePositions[2].x, nodePositions[2].y, nodePositions[2].z,
      nodePositions[0].x, nodePositions[0].y, nodePositions[0].z,
    ];
    return new Float32Array(points);
  }, [nodePositions]);

  useFrame((stateCtx) => {
    const t = stateCtx.clock.getElapsedTime();
    // Pulse speed reacts directly to ConsensusEngine active processing state
    const pulseSpeed = isProcessing ? 5.0 : isWalletConnected ? 2.0 : 0.8;

    if (groupRef.current) {
      groupRef.current.children.forEach((child, i) => {
        // Node 1, 2, 3 correspond to activeNodes
        const isNodeActive = activeNodes.includes(i + 1) || state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED";
        const amplitude = isNodeActive ? 0.12 : 0.05;
        child.position.y = nodePositions[i].y + Math.sin(t * pulseSpeed + i) * amplitude;
        
        // Scale node when active in consensus wave
        const targetScale = isNodeActive ? 1.3 : 1.0;
        child.scale.lerp(new THREE.Vector3(targetScale, targetScale, targetScale), 0.1);
      });
    }

    if (linesRef.current) {
      const mat = linesRef.current.material as THREE.LineBasicMaterial;
      const baseOpacity = isProcessing ? 0.8 : isWalletConnected ? 0.4 : 0.15;
      mat.opacity = baseOpacity + Math.sin(t * pulseSpeed) * 0.15;
      
      // Color change on consensus states
      if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED") {
        mat.color.set("#22c55e");
      } else if (state === "PROPAGATING" || state === "VALIDATING") {
        mat.color.set("#6366f1");
      } else {
        mat.color.set(isWalletConnected ? "#4f46e5" : "#334155");
      }
    }
  });

  return (
    <group>
      <lineSegments ref={linesRef}>
        <bufferGeometry>
          <bufferAttribute attach="attributes-position" args={[linePositions, 3]} />
        </bufferGeometry>
        <lineBasicMaterial
          color="#4f46e5"
          transparent
          opacity={0.2}
        />
      </lineSegments>

      <group ref={groupRef}>
        {nodePositions.map((pos, idx) => {
          const isNodeActive = activeNodes.includes(idx + 1) || state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED";
          const nodeColor =
            state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED"
              ? "#22c55e"
              : isNodeActive
              ? "#6366f1"
              : isWalletConnected
              ? "#475569"
              : "#1e293b";

          return (
            <group key={idx} position={pos}>
              <mesh>
                <sphereGeometry args={[0.18, 16, 16]} />
                <meshStandardMaterial
                  color={nodeColor}
                  emissive={nodeColor}
                  emissiveIntensity={isNodeActive ? 1.2 : 0.2}
                  roughness={0.1}
                  metalness={0.9}
                />
              </mesh>
              <pointLight color={nodeColor} intensity={isNodeActive ? 1.5 : 0.3} distance={3.0} />
            </group>
          );
        })}
      </group>
    </group>
  );
}
