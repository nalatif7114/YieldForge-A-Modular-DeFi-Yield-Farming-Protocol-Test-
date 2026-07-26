"use client";

import { useEffect } from "react";
import Lenis from "lenis";
import { useProtocolStore } from "@/store/useProtocolStore";

export function LenisMasterTimeline() {
  const { setScrollProgress, setMousePosition } = useProtocolStore();

  useEffect(() => {
    // Initialize Lenis Smooth Scroll Instance
    const lenis = new Lenis({
      duration: 1.4,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: "vertical",
      gestureOrientation: "vertical",
      smoothWheel: true,
      wheelMultiplier: 0.9,
    });

    function raf(time: number) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);

    // Bind scroll progress
    lenis.on("scroll", (e: any) => {
      const progress = e.progress || 0;
      setScrollProgress(progress);
    });

    // Mouse movement parallax listener
    const handleMouseMove = (event: MouseEvent) => {
      const x = (event.clientX / window.innerWidth) * 2 - 1;
      const y = -(event.clientY / window.innerHeight) * 2 + 1;
      setMousePosition({ x, y });
    };

    window.addEventListener("mousemove", handleMouseMove);

    return () => {
      lenis.destroy();
      window.removeEventListener("mousemove", handleMouseMove);
    };
  }, [setScrollProgress, setMousePosition]);

  return null;
}
