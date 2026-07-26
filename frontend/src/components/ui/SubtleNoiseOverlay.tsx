"use client";

/**
 * SubtleNoiseOverlay: Handcrafted micro-primitive.
 * SVG monochrome noise texture at 2% opacity eliminating digital gradient banding.
 */
export function SubtleNoiseOverlay() {
  return (
    <div className="pointer-events-none fixed inset-0 z-30 opacity-[0.025] mix-blend-overlay">
      <svg className="w-full h-full">
        <filter id="yieldforge-noise">
          <feTurbulence
            type="fractalNoise"
            baseFrequency="0.8"
            numOctaves="3"
            stitchTiles="stitch"
          />
        </filter>
        <rect width="100%" height="100%" filter="url(#yieldforge-noise)" />
      </svg>
    </div>
  );
}
