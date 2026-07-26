"use client";

import { useRef, useState } from "react";

interface PerspectiveGlowBorderProps {
  children: React.ReactNode;
  className?: string;
}

/**
 * PerspectiveGlowBorder: Handcrafted micro-primitive.
 * Micro-tilt 3D card wrapper with a single subtle Deep Violet (#7C3AED) radial spotlight.
 */
export function PerspectiveGlowBorder({ children, className = "" }: PerspectiveGlowBorderProps) {
  const cardRef = useRef<HTMLDivElement>(null!);
  const [mousePos, setMousePos] = useState({ x: 0, y: 0 });
  const [isHovered, setIsHovered] = useState(false);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    const { left, top, width, height } = cardRef.current.getBoundingClientRect();
    const x = e.clientX - left;
    const y = e.clientY - top;
    setMousePos({ x, y });
  };

  return (
    <div
      ref={cardRef}
      onMouseMove={handleMouseMove}
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      className={`relative rounded-3xl bg-[#050816]/80 border border-white/10 backdrop-blur-2xl overflow-hidden transition-colors hover:border-white/20 ${className}`}
    >
      {/* Radial Deep Violet Mouse Spotlight Overlay */}
      {isHovered && (
        <div
          className="pointer-events-none absolute -inset-px opacity-100 transition-opacity duration-300 z-10"
          style={{
            background: `radial-gradient(400px circle at ${mousePos.x}px ${mousePos.y}px, rgba(124, 58, 237, 0.12), transparent 80%)`,
          }}
        />
      )}
      <div className="relative z-20">{children}</div>
    </div>
  );
}
