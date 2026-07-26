"use client";

import { useRef } from "react";
import { useFrame } from "@react-three/fiber";
import * as THREE from "three";

const EnergyConduitShader = {
  uniforms: {
    uTime: { value: 0 },
    uColor: { value: new THREE.Color("#7c3aed") },
    uPulseColor: { value: new THREE.Color("#38bdf8") },
  },
  vertexShader: `
    varying vec2 vUv;
    varying vec3 vNormal;
    void main() {
      vUv = uv;
      vNormal = normalize(normalMatrix * normal);
      gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
    }
  `,
  fragmentShader: `
    uniform float uTime;
    uniform vec3 uColor;
    uniform vec3 uPulseColor;
    varying vec2 vUv;
    varying vec3 vNormal;

    void main() {
      // Flowing wave pulse calculation
      float wave = sin(vUv.x * 20.0 - uTime * 4.0) * 0.5 + 0.5;
      vec3 finalColor = mix(uColor, uPulseColor, pow(wave, 3.0));
      float fresnel = pow(1.0 - abs(dot(vNormal, vec3(0.0, 0.0, 1.0))), 2.0);
      
      gl_FragColor = vec4(finalColor + vec3(fresnel * 0.4), 0.85);
    }
  `,
};

export function EnergyConduitMesh({ points }: { points: THREE.Vector3[] }) {
  const materialRef = useRef<THREE.ShaderMaterial>(null!);

  const tubeGeometry = new THREE.TubeGeometry(
    new THREE.CatmullRomCurve3(points),
    64,
    0.04,
    8,
    false
  );

  useFrame((state) => {
    if (materialRef.current) {
      materialRef.current.uniforms.uTime.value = state.clock.getElapsedTime();
    }
  });

  return (
    <mesh geometry={tubeGeometry}>
      <shaderMaterial
        ref={materialRef}
        args={[EnergyConduitShader]}
        transparent={true}
        side={THREE.DoubleSide}
      />
    </mesh>
  );
}
