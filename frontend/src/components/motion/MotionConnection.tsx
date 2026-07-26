"use client";

interface MotionConnectionProps {
  startX?: number;
  startY?: number;
  endX?: number;
  endY?: number;
  active?: boolean;
  color?: string;
  className?: string;
}

/**
 * MotionConnection: Animated SVG node connection ray.
 * Draws link lines and packet pulses between protocol telemetry components.
 */
export function MotionConnection({
  startX = 0,
  startY = 20,
  endX = 200,
  endY = 20,
  active = true,
  color = "#4f46e5",
  className = "",
}: MotionConnectionProps) {
  return (
    <svg className={`w-full h-10 overflow-visible ${className}`} viewBox={`0 0 ${endX + 20} 40`}>
      <line
        x1={startX}
        y1={startY}
        x2={endX}
        y2={endY}
        stroke={color}
        strokeWidth="1.5"
        strokeDasharray="4 4"
        className="opacity-30"
      />
      {active && (
        <circle r="3" fill={color}>
          <animateMotion
            path={`M ${startX} ${startY} L ${endX} ${endY}`}
            dur="2.5s"
            repeatCount="indefinite"
          />
        </circle>
      )}
    </svg>
  );
}
