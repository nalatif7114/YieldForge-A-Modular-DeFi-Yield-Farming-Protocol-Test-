"use client";

import { useRef, useState } from "react";
import { motion } from "framer-motion";

interface MagneticButtonProps {
  children: React.ReactNode;
  onClick?: () => void;
  className?: string;
  variant?: "primary" | "secondary" | "emerald";
}

/**
 * MagneticButton: Handcrafted micro-primitive.
 * Provides subtle spring-driven magnetic cursor attraction (max 6px displacement)
 * adhering to Apple/Nothing.tech tactile feedback principles.
 */
export function MagneticButton({
  children,
  onClick,
  className = "",
  variant = "primary",
}: MagneticButtonProps) {
  const buttonRef = useRef<HTMLDivElement>(null!);
  const [position, setPosition] = useState({ x: 0, y: 0 });

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    const { clientX, clientY } = e;
    const { left, top, width, height } = buttonRef.current.getBoundingClientRect();
    const centerX = left + width / 2;
    const centerY = top + height / 2;

    // Restrained displacement (max 6px offset)
    const distanceX = (clientX - centerX) * 0.15;
    const distanceY = (clientY - centerY) * 0.15;

    setPosition({ x: distanceX, y: distanceY });
  };

  const handleMouseLeave = () => {
    setPosition({ x: 0, y: 0 });
  };

  const variantStyles = {
    primary: "bg-white text-[#050816] hover:bg-slate-100 shadow-sm font-semibold",
    secondary: "bg-white/[0.04] text-slate-200 border border-white/10 hover:border-white/20 font-medium",
    emerald: "bg-[#22c55e] text-[#050816] hover:bg-[#16a34a] shadow-lg shadow-emerald-500/20 font-semibold",
  };

  return (
    <motion.div
      ref={buttonRef}
      onMouseMove={handleMouseMove}
      onMouseLeave={handleMouseLeave}
      animate={{ x: position.x, y: position.y }}
      transition={{ type: "spring", stiffness: 180, damping: 14, mass: 0.1 }}
      onClick={onClick}
      className={`inline-block cursor-pointer select-none rounded-xl ${className}`}
    >
      <div
        className={`px-5 py-2.5 rounded-xl text-xs flex items-center gap-2 transition-colors ${variantStyles[variant]}`}
      >
        {children}
      </div>
    </motion.div>
  );
}
