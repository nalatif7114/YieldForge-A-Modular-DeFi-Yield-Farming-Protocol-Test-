"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";
import { CinematicConsensusCamera } from "./CinematicConsensusCamera";

/**
 * 3D Coordinates for 4 Validator Nodes in Vector Topology
 */
const NODE_POSITIONS: Record<number, THREE.Vector3> = {
  1: new THREE.Vector3(-2.2, 0.8, 0.5),   // Node A - Primary Ingestion
  2: new THREE.Vector3(0.8, 1.6, -1.0),   // Node B - Relay East
  3: new THREE.Vector3(-0.5, -1.4, 0.8),  // Node C - Relay West
  4: new THREE.Vector3(2.5, -0.2, -0.4),  // Node D - State Commit Finalizer
};

const CONNECTIONS = [
  { from: 1, to: 2, id: "1-2" },
  { from: 1, to: 3, id: "1-3" },
  { from: 2, to: 4, id: "2-4" },
  { from: 3, to: 4, id: "3-4" },
];

/**
 * SpatialNodeMesh: 3D Consensus Network Visualizer driven strictly by ConsensusEngine.
 * 
 * Mechanics:
 * - ConsensusWave only visualizes state (zero UI timers).
 * - A 3D packet travels between validator nodes based on ConsensusEngine state.
 * - Connection lines illuminate ONLY while the packet passes through that specific ray.
 * - Return to calm idle afterwards.
 */
export function SpatialNodeMesh() {
  const nodesGroupRef = useRef<THREE.Group>(null!);
  const packetRefA = useRef<THREE.Mesh>(null!);
  const packetRefB = useRef<THREE.Mesh>(null!);
  const lineMeshRefs = useRef<Array<THREE.LineSegments | null>>([]);
  
  const { state, activeNodes, activeConnections } = useConsensusEngine();

  // Progress interpolation for 3D travelling packet (0 -> 1)
  const packetProgress = useRef(0);

  // 4 Validator Nodes
  const validatorNodes = useMemo(() => {
    return [
      { id: 1, basePos: NODE_POSITIONS[1], label: "Node A" },
      { id: 2, basePos: NODE_POSITIONS[2], label: "Node B" },
      { id: 3, basePos: NODE_POSITIONS[3], label: "Node C" },
      { id: 4, basePos: NODE_POSITIONS[4], label: "Node D" },
    ];
  }, []);

  // Pre-generate connection line geometries to avoid SVG type collision in React JSX
  const connectionGeometries = useMemo(() => {
    return CONNECTIONS.map((conn) => {
      const p1 = NODE_POSITIONS[conn.from];
      const p2 = NODE_POSITIONS[conn.to];
      const points = new Float32Array([p1.x, p1.y, p1.z, p2.x, p2.y, p2.z]);
      const geo = new THREE.BufferGeometry();
      geo.setAttribute("position", new THREE.BufferAttribute(points, 3));
      return geo;
    });
  }, []);

  useFrame((stateCtx, delta) => {
    const t = stateCtx.clock.getElapsedTime();

    // 1. Subtle Micro-Floating per Validator Node (No spinning!)
    if (nodesGroupRef.current) {
      nodesGroupRef.current.children.forEach((groupChild, i) => {
        const node = validatorNodes[i];
        if (node) {
          const floatY = Math.sin(t * 0.7 + node.id * 1.3) * 0.04;
          const floatX = Math.cos(t * 0.5 + node.id * 0.9) * 0.02;
          groupChild.position.set(
            node.basePos.x + floatX,
            node.basePos.y + floatY,
            node.basePos.z
          );
        }
      });
    }

    // 2. Traveling 3D Consensus Packet Interpolation
    if (state === "PROPAGATING") {
      packetProgress.current = THREE.MathUtils.damp(packetProgress.current, 1, 4, delta);
    } else if (state === "TRANSACTION_RECEIVED" || state === "VALIDATING") {
      packetProgress.current = 0;
    } else {
      packetProgress.current = 0;
    }

    const p = packetProgress.current;

    // Update Packet A (Node 1 -> Node 2 path)
    if (packetRefA.current) {
      if (state === "TRANSACTION_RECEIVED" || state === "VALIDATING") {
        packetRefA.current.position.copy(NODE_POSITIONS[1]);
        packetRefA.current.visible = true;
      } else if (state === "PROPAGATING") {
        packetRefA.current.position.lerpVectors(NODE_POSITIONS[1], NODE_POSITIONS[2], p);
        packetRefA.current.visible = true;
      } else {
        packetRefA.current.visible = false;
      }
    }

    // Update Packet B (Node 1 -> Node 3 path)
    if (packetRefB.current) {
      if (state === "PROPAGATING") {
        packetRefB.current.position.lerpVectors(NODE_POSITIONS[1], NODE_POSITIONS[3], p);
        packetRefB.current.visible = true;
      } else {
        packetRefB.current.visible = false;
      }
    }

    // 3. Selective Connection Line Illumination (ONLY while packet passes!)
    CONNECTIONS.forEach((conn, index) => {
      const lineSegmentsMesh = lineMeshRefs.current[index];
      if (lineSegmentsMesh) {
        const mat = lineSegmentsMesh.material as THREE.LineBasicMaterial;
        const isRayActive = activeConnections.some(
          (c) => (c.from === conn.from && c.to === conn.to) || (c.from === conn.to && c.to === conn.from)
        );

        // Connection line illuminates ONLY when packet is actively travelling across it
        const targetOpacity = isRayActive ? 0.65 : 0.12;
        mat.opacity = THREE.MathUtils.damp(mat.opacity, targetOpacity, 6, delta);

        if (isRayActive) {
          mat.color.set("#6366f1"); // Active indigo propagation ray
        } else if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED") {
          mat.color.set("#22c55e"); // Finalized green
        } else {
          mat.color.set("#475569"); // Dim idle grey
        }
      }
    });
  });

  return (
    <group>
      {/* Cinematic Camera owning all movement */}
      <CinematicConsensusCamera />

      {/* Individual Connection Rays (illuminating selectively via lineSegments) */}
      {CONNECTIONS.map((conn, idx) => (
        <lineSegments
          key={conn.id}
          ref={(el) => {
            lineMeshRefs.current[idx] = el;
          }}
          geometry={connectionGeometries[idx]}
        >
          <lineBasicMaterial color="#475569" transparent opacity={0.12} linewidth={1} />
        </lineSegments>
      ))}

      {/* Travelling 3D Packet Objects (Node 1 -> Node 2/3) */}
      <mesh ref={packetRefA} visible={false}>
        <sphereGeometry args={[0.08, 12, 12]} />
        <meshStandardMaterial color="#38bdf8" emissive="#38bdf8" emissiveIntensity={1.2} />
      </mesh>
      <mesh ref={packetRefB} visible={false}>
        <sphereGeometry args={[0.08, 12, 12]} />
        <meshStandardMaterial color="#38bdf8" emissive="#38bdf8" emissiveIntensity={1.2} />
      </mesh>

      {/* 4 Floating Validator Nodes */}
      <group ref={nodesGroupRef}>
        {validatorNodes.map((node) => {
          const isValidating = activeNodes.includes(node.id);
          const isFinalized = state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED" || state === "COMPLETE";

          let nodeColor = "#334155";
          let emissiveIntensity = 0.15;

          if (isFinalized) {
            nodeColor = "#22c55e"; // Emerald lock
            emissiveIntensity = 0.6;
          } else if (isValidating) {
            nodeColor = "#6366f1"; // Indigo validation
            emissiveIntensity = 0.8;
          }

          return (
            <group key={node.id} position={node.basePos}>
              {/* Minimal Wireframe Shell */}
              <mesh>
                <icosahedronGeometry args={[0.22, 1]} />
                <meshBasicMaterial
                  color={nodeColor}
                  wireframe
                  transparent
                  opacity={isValidating || isFinalized ? 0.4 : 0.15}
                />
              </mesh>

              {/* Minimal Core Sphere */}
              <mesh>
                <sphereGeometry args={[0.12, 16, 16]} />
                <meshStandardMaterial
                  color={nodeColor}
                  emissive={nodeColor}
                  emissiveIntensity={emissiveIntensity}
                  roughness={0.2}
                  metalness={0.8}
                />
              </mesh>

              {/* Controlled Light Bloom */}
              <pointLight
                color={nodeColor}
                intensity={isValidating || isFinalized ? 0.8 : 0.15}
                distance={2.0}
              />
            </group>
          );
        })}
      </group>
    </group>
  );
}
