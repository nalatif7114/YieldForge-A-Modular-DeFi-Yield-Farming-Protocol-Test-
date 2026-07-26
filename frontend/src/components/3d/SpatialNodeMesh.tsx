"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { CinematicConsensusCamera } from "./CinematicConsensusCamera";

/**
 * Project Helios 8-Node Infrastructure Topology
 */
const HELIOS_8NODE_TOPOLOGY: Record<number, THREE.Vector3> = {
  1: new THREE.Vector3(0.0, 3.2, -0.8),    // Top Apex Validator
  2: new THREE.Vector3(-3.6, 1.8, 0.4),   // North-West Relay
  3: new THREE.Vector3(3.6, 1.8, -0.6),    // North-East Relay
  4: new THREE.Vector3(-4.5, -0.4, 0.6),   // Primary Ingestion Endpoint
  5: new THREE.Vector3(4.5, -0.4, -0.4),   // Finalizer Commit Endpoint
  6: new THREE.Vector3(-3.4, -2.4, 0.2),   // South-West Relay
  7: new THREE.Vector3(3.4, -2.4, -0.8),   // South-East Relay
  8: new THREE.Vector3(0.0, -3.4, -1.0),   // Bottom Anchor Validator
};

const HELIOS_CONNECTIONS = [
  { from: 1, to: 2, id: "1-2" },
  { from: 1, to: 3, id: "1-3" },
  { from: 2, to: 4, id: "2-4" },
  { from: 2, to: 5, id: "2-5" },
  { from: 3, to: 4, id: "3-4" },
  { from: 3, to: 5, id: "3-5" },
  { from: 4, to: 6, id: "4-6" },
  { from: 4, to: 5, id: "4-5" },
  { from: 5, to: 7, id: "5-7" },
  { from: 6, to: 8, id: "6-8" },
  { from: 7, to: 8, id: "7-8" },
];

/**
 * Phase 4: Protocol Event Renderer (SpatialNodeMesh)
 * 
 * Rules:
 * - Pure renderer translating ConsensusEngine state into physical node & ray responses.
 * - Zero timers in UI.
 * - Packet: Simple, lightweight 0.065 unit protocol message sphere (no plasma/fireballs).
 * - Node Response: Understated edge highlights & micro elevation (nothing theatrical).
 * - Edge Illumination: ONLY traversed active edges illuminate to 60%; others stay at 10%.
 * - Camera Bias: Observer camera shifts max 2%.
 */
export function SpatialNodeMesh() {
  const nodesGroupRef = useRef<THREE.Group>(null!);
  const packetMeshARef = useRef<THREE.Mesh>(null!);
  const packetMeshBRef = useRef<THREE.Mesh>(null!);
  const lineMeshRefs = useRef<Array<THREE.LineSegments | null>>([]);
  
  const { state, activeNodes, activeConnections } = useConsensusEngine();

  // Smooth linear packet progress across traversed edges
  const packetProgress = useRef(0);

  const validatorNodes = useMemo(() => {
    return Object.entries(HELIOS_8NODE_TOPOLOGY).map(([idStr, basePos]) => ({
      id: Number(idStr),
      basePos,
    }));
  }, []);

  const connectionGeometries = useMemo(() => {
    return HELIOS_CONNECTIONS.map((conn) => {
      const p1 = HELIOS_8NODE_TOPOLOGY[conn.from];
      const p2 = HELIOS_8NODE_TOPOLOGY[conn.to];
      const points = new Float32Array([p1.x, p1.y, p1.z, p2.x, p2.y, p2.z]);
      const geo = new THREE.BufferGeometry();
      geo.setAttribute("position", new THREE.BufferAttribute(points, 3));
      return geo;
    });
  }, []);

  useFrame((stateCtx, delta) => {
    const t = stateCtx.clock.getElapsedTime();

    // 1. Microscopic Node Floating (< 1px visual drift, almost static)
    if (nodesGroupRef.current) {
      nodesGroupRef.current.children.forEach((groupChild, i) => {
        const node = validatorNodes[i];
        if (node) {
          const isValidating = activeNodes.includes(node.id);
          const microY = Math.sin(t * 0.35 + node.id * 0.9) * 0.012;
          const microX = Math.cos(t * 0.25 + node.id * 0.7) * 0.008;
          
          // Understated elevation response when validating (micro +0.03 shift)
          const elevationOffset = isValidating ? 0.03 : 0.0;
          const currentY = groupChild.position.y;
          const targetY = node.basePos.y + microY + elevationOffset;

          groupChild.position.set(
            node.basePos.x + microX,
            THREE.MathUtils.damp(currentY, targetY, 4, delta),
            node.basePos.z
          );
        }
      });
    }

    // 2. Packet Lifecycle & Traversal
    if (state === "PROPAGATING") {
      packetProgress.current = THREE.MathUtils.damp(packetProgress.current, 1, 3.5, delta);
    } else {
      packetProgress.current = 0;
    }

    const p = packetProgress.current;

    // Primary Ingestion Packet A (Node 4 -> Node 5 Commit Path)
    if (packetMeshARef.current) {
      if (state === "TRANSACTION_RECEIVED" || state === "VALIDATING") {
        packetMeshARef.current.position.copy(HELIOS_8NODE_TOPOLOGY[4]);
        packetMeshARef.current.visible = true;
      } else if (state === "PROPAGATING") {
        packetMeshARef.current.position.lerpVectors(
          HELIOS_8NODE_TOPOLOGY[4],
          HELIOS_8NODE_TOPOLOGY[5],
          p
        );
        packetMeshARef.current.visible = true;
      } else {
        packetMeshARef.current.visible = false;
      }
    }

    // Secondary Relay Packet B (Node 4 -> Node 2 Relay Path)
    if (packetMeshBRef.current) {
      if (state === "PROPAGATING") {
        packetMeshBRef.current.position.lerpVectors(
          HELIOS_8NODE_TOPOLOGY[4],
          HELIOS_8NODE_TOPOLOGY[2],
          p
        );
        packetMeshBRef.current.visible = true;
      } else {
        packetMeshBRef.current.visible = false;
      }
    }

    // 3. Selective Edge Illumination (Idle = 10% opacity; ONLY active path illuminates to 60%)
    HELIOS_CONNECTIONS.forEach((conn, index) => {
      const lineMesh = lineMeshRefs.current[index];
      if (lineMesh) {
        const mat = lineMesh.material as THREE.LineBasicMaterial;
        const isRayActive = activeConnections.some(
          (c) => (c.from === conn.from && c.to === conn.to) || (c.from === conn.to && c.to === conn.from)
        );

        // ONLY traversed active edge illuminates
        const targetOpacity = isRayActive ? 0.60 : 0.10;
        mat.opacity = THREE.MathUtils.damp(mat.opacity, targetOpacity, 5, delta);

        if (isRayActive) {
          mat.color.set("#6366f1"); // Active propagation path
        } else if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED") {
          mat.color.set("#22c55e"); // Finalized lock
        } else {
          mat.color.set("#1e293b"); // 10% idle slate
        }
      }
    });
  });

  return (
    <group>
      {/* Observer Camera with 2% packet tracking shift */}
      <CinematicConsensusCamera />

      {/* Extremely Thin WebGL Connection Lines (10% idle opacity) */}
      {HELIOS_CONNECTIONS.map((conn, idx) => (
        <lineSegments
          key={conn.id}
          ref={(el) => {
            lineMeshRefs.current[idx] = el;
          }}
          geometry={connectionGeometries[idx]}
        >
          <lineBasicMaterial color="#1e293b" transparent opacity={0.10} linewidth={1} />
        </lineSegments>
      ))}

      {/* Lightweight Protocol Message Packets */}
      <mesh ref={packetMeshARef} visible={false}>
        <sphereGeometry args={[0.065, 12, 12]} />
        <meshStandardMaterial color="#38bdf8" emissive="#38bdf8" emissiveIntensity={0.6} />
      </mesh>
      <mesh ref={packetMeshBRef} visible={false}>
        <sphereGeometry args={[0.065, 12, 12]} />
        <meshStandardMaterial color="#38bdf8" emissive="#38bdf8" emissiveIntensity={0.6} />
      </mesh>

      {/* Hardware Validator Endpoints */}
      <group ref={nodesGroupRef}>
        {validatorNodes.map((node) => {
          const isValidating = activeNodes.includes(node.id);
          const isFinalized = state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED" || state === "COMPLETE";

          let edgeColor = "#334155";
          let ledColor = "#1e293b";
          let ledIntensity = 0.15;

          if (isFinalized) {
            edgeColor = "#22c55e"; // Understated emerald finalized outline
            ledColor = "#22c55e";
            ledIntensity = 0.5;
          } else if (isValidating) {
            edgeColor = "#6366f1"; // Understated indigo validation outline
            ledColor = "#6366f1";
            ledIntensity = 0.7;
          }

          return (
            <group key={node.id} position={node.basePos}>
              {/* Dark Metallic Industrial Chassis */}
              <mesh>
                <boxGeometry args={[0.42, 0.30, 0.42]} />
                <meshStandardMaterial
                  color="#090d16"
                  roughness={0.20}
                  metalness={0.90}
                />
              </mesh>

              {/* Minimal Edge Outline Highlight */}
              <lineSegments>
                <edgesGeometry args={[new THREE.BoxGeometry(0.42, 0.30, 0.42)]} />
                <lineBasicMaterial
                  color={edgeColor}
                  transparent
                  opacity={isValidating || isFinalized ? 0.7 : 0.20}
                />
              </lineSegments>

              {/* Micro Status LED Endpoint */}
              <mesh position={[0, 0.16, 0]}>
                <sphereGeometry args={[0.03, 12, 12]} />
                <meshStandardMaterial
                  color={ledColor}
                  emissive={ledColor}
                  emissiveIntensity={ledIntensity}
                />
              </mesh>

              {/* Subtle Controlled Light */}
              <pointLight
                color={ledColor}
                intensity={isValidating || isFinalized ? 0.30 : 0.04}
                distance={1.5}
              />
            </group>
          );
        })}
      </group>
    </group>
  );
}
