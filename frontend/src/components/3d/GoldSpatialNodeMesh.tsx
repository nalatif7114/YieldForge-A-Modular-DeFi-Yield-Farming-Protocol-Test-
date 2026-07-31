"use client";

import { useRef, useMemo } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";
import { useProtocolStore } from "@/store/useProtocolStore";
import { useConsensusEngine } from "@/hooks/useConsensusEngine";

/**
 * 8-Node Institutional Validator Topology (Gold Luxury Theme)
 */
const GOLD_TOPOLOGY: Record<number, THREE.Vector3> = {
  1: new THREE.Vector3(0.0, 2.8, -0.6),    // Apex Validator
  2: new THREE.Vector3(-3.2, 1.5, 0.4),   // North-West Relay
  3: new THREE.Vector3(3.2, 1.5, -0.5),    // North-East Relay
  4: new THREE.Vector3(-4.0, -0.3, 0.5),   // Primary Vault Node
  5: new THREE.Vector3(4.0, -0.3, -0.4),   // Settlement Endpoint
  6: new THREE.Vector3(-3.0, -2.2, 0.2),   // South-West Relay
  7: new THREE.Vector3(3.0, -2.2, -0.7),   // South-East Relay
  8: new THREE.Vector3(0.0, -3.0, -0.8),   // Anchor Validator
};

const GOLD_CONNECTIONS = [
  { from: 1, to: 2 },
  { from: 1, to: 3 },
  { from: 2, to: 4 },
  { from: 2, to: 5 },
  { from: 3, to: 4 },
  { from: 3, to: 5 },
  { from: 4, to: 6 },
  { from: 4, to: 5 },
  { from: 5, to: 7 },
  { from: 6, to: 8 },
  { from: 7, to: 8 },
];

export function GoldSpatialNodeMesh() {
  const nodesGroupRef = useRef<THREE.Group>(null!);
  const packetMeshRef = useRef<THREE.Mesh>(null!);
  const particlesRef = useRef<THREE.Points>(null!);
  const lineMeshRefs = useRef<Array<THREE.LineSegments | null>>([]);
  
  const { mousePosition } = useProtocolStore();
  const { state, activeNodes, activeConnections } = useConsensusEngine();

  const packetProgress = useRef(0);

  // Floating Particles Array (Gold Champagne Ambient Field)
  const particlesGeometry = useMemo(() => {
    const count = 40;
    const positions = new Float32Array(count * 3);
    for (let i = 0; i < count * 3; i += 3) {
      positions[i] = (Math.random() - 0.5) * 14;
      positions[i + 1] = (Math.random() - 0.5) * 10;
      positions[i + 2] = (Math.random() - 0.5) * 6 - 2;
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute("position", new THREE.BufferAttribute(positions, 3));
    return geo;
  }, []);

  const validatorNodes = useMemo(() => {
    return Object.entries(GOLD_TOPOLOGY).map(([idStr, basePos]) => ({
      id: Number(idStr),
      basePos,
    }));
  }, []);

  const connectionGeometries = useMemo(() => {
    return GOLD_CONNECTIONS.map((conn) => {
      const p1 = GOLD_TOPOLOGY[conn.from];
      const p2 = GOLD_TOPOLOGY[conn.to];
      const points = new Float32Array([p1.x, p1.y, p1.z, p2.x, p2.y, p2.z]);
      const geo = new THREE.BufferGeometry();
      geo.setAttribute("position", new THREE.BufferAttribute(points, 3));
      return geo;
    });
  }, []);

  useFrame((stateCtx, delta) => {
    const t = stateCtx.clock.getElapsedTime();

    // 1. Slow Orbital Camera Parallax (Max 2%)
    const targetX = mousePosition.x * 0.35;
    const targetY = mousePosition.y * 0.25;
    stateCtx.camera.position.x = THREE.MathUtils.damp(stateCtx.camera.position.x, targetX, 2.5, delta);
    stateCtx.camera.position.y = THREE.MathUtils.damp(stateCtx.camera.position.y, 1.0 + targetY, 2.5, delta);
    stateCtx.camera.lookAt(0, 0, 0);

    // 2. Slow Micro Floating Nodes (< 1px)
    if (nodesGroupRef.current) {
      nodesGroupRef.current.children.forEach((groupChild, i) => {
        const node = validatorNodes[i];
        if (node) {
          const microY = Math.sin(t * 0.4 + node.id * 0.8) * 0.015;
          const microX = Math.cos(t * 0.3 + node.id * 0.6) * 0.01;
          groupChild.position.set(
            node.basePos.x + microX,
            node.basePos.y + microY,
            node.basePos.z
          );
        }
      });
    }

    // 3. Gentle Ambient Particle Drift
    if (particlesRef.current) {
      particlesRef.current.rotation.y = t * 0.02;
    }

    // 4. Gold Packet Propagation
    if (state === "PROPAGATING") {
      packetProgress.current = THREE.MathUtils.damp(packetProgress.current, 1, 3.0, delta);
    } else {
      packetProgress.current = 0;
    }

    if (packetMeshRef.current) {
      if (state === "TRANSACTION_RECEIVED" || state === "VALIDATING") {
        packetMeshRef.current.position.copy(GOLD_TOPOLOGY[4]);
        packetMeshRef.current.visible = true;
      } else if (state === "PROPAGATING") {
        packetMeshRef.current.position.lerpVectors(
          GOLD_TOPOLOGY[4],
          GOLD_TOPOLOGY[5],
          packetProgress.current
        );
        packetMeshRef.current.visible = true;
      } else {
        packetMeshRef.current.visible = false;
      }
    }

    // 5. Connection Line Illumination (Gold Luxury Tint)
    GOLD_CONNECTIONS.forEach((conn, index) => {
      const lineMesh = lineMeshRefs.current[index];
      if (lineMesh) {
        const mat = lineMesh.material as THREE.LineBasicMaterial;
        const isRayActive = activeConnections.some(
          (c) => (c.from === conn.from && c.to === conn.to) || (c.from === conn.to && c.to === conn.from)
        );

        const targetOpacity = isRayActive ? 0.70 : 0.15;
        mat.opacity = THREE.MathUtils.damp(mat.opacity, targetOpacity, 4, delta);

        if (isRayActive) {
          mat.color.set("#D4AF37"); // Primary Gold
        } else if (state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED") {
          mat.color.set("#E7C873"); // Secondary Gold
        } else {
          mat.color.set("#332b15"); // Muted Gold Slate
        }
      }
    });
  });

  return (
    <group>
      {/* Gold Ambient Floating Particles */}
      <points ref={particlesRef} geometry={particlesGeometry}>
        <pointsMaterial
          size={0.04}
          color="#F5E6B8"
          transparent
          opacity={0.3}
          sizeAttenuation
        />
      </points>

      {/* Gold Connection Lines */}
      {GOLD_CONNECTIONS.map((conn, idx) => (
        <lineSegments
          key={`${conn.from}-${conn.to}`}
          ref={(el) => {
            lineMeshRefs.current[idx] = el;
          }}
          geometry={connectionGeometries[idx]}
        >
          <lineBasicMaterial color="#332b15" transparent opacity={0.15} linewidth={1} />
        </lineSegments>
      ))}

      {/* Luxury Gold Protocol Packet */}
      <mesh ref={packetMeshRef} visible={false}>
        <sphereGeometry args={[0.07, 16, 16]} />
        <meshStandardMaterial
          color="#F5E6B8"
          emissive="#D4AF37"
          emissiveIntensity={0.8}
          metalness={0.9}
          roughness={0.1}
        />
      </mesh>

      {/* Institutional Hardware Validator Endpoints */}
      <group ref={nodesGroupRef}>
        {validatorNodes.map((node) => {
          const isValidating = activeNodes.includes(node.id);
          const isFinalized = state === "CONSENSUS_REACHED" || state === "STATE_COMMITTED";

          let edgeColor = "rgba(212,175,55,0.2)";
          let ledColor = "#D4AF37";
          let ledIntensity = 0.2;

          if (isFinalized || isValidating) {
            edgeColor = "#E7C873";
            ledColor = "#F5E6B8";
            ledIntensity = 0.8;
          }

          return (
            <group key={node.id} position={node.basePos}>
              {/* Chassis: Dark Metallic Body */}
              <mesh>
                <boxGeometry args={[0.45, 0.32, 0.45]} />
                <meshStandardMaterial
                  color="#111111"
                  metalness={0.95}
                  roughness={0.15}
                />
              </mesh>

              {/* Edge Highlights: Gold Rim */}
              <lineSegments>
                <edgesGeometry args={[new THREE.BoxGeometry(0.45, 0.32, 0.45)]} />
                <lineBasicMaterial
                  color={edgeColor}
                  transparent
                  opacity={isValidating || isFinalized ? 0.8 : 0.25}
                />
              </lineSegments>

              {/* Status LED: Champagne Gold Micro Lens */}
              <mesh position={[0, 0.17, 0]}>
                <sphereGeometry args={[0.035, 16, 16]} />
                <meshStandardMaterial
                  color={ledColor}
                  emissive={ledColor}
                  emissiveIntensity={ledIntensity}
                />
              </mesh>

              {/* Soft Ambient Light */}
              <pointLight
                color="#D4AF37"
                intensity={isValidating || isFinalized ? 0.4 : 0.05}
                distance={1.8}
              />
            </group>
          );
        })}
      </group>
    </group>
  );
}
