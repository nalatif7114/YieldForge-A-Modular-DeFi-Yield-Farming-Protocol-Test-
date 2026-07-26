"use client";

import { createContext, useContext, useState, useEffect } from "react";

export type OrchestraStage =
  | "loading"
  | "header"
  | "headline"
  | "depth"
  | "telemetry"
  | "pipeline"
  | "interactive"
  | "ready";

interface OrchestraContextType {
  stage: OrchestraStage;
  stageProgress: number; // 0 to 1
  isReady: boolean;
}

const OrchestraContext = createContext<OrchestraContextType>({
  stage: "loading",
  stageProgress: 0,
  isReady: false,
});

export function OrchestraProvider({ children }: { children: React.ReactNode }) {
  const [stage, setStage] = useState<OrchestraStage>("loading");
  const [stageProgress, setStageProgress] = useState(0);

  useEffect(() => {
    // Orchestrated Timeline Execution Schedule
    const timeline = [
      { stage: "loading" as OrchestraStage, time: 0, progress: 0.1 },
      { stage: "header" as OrchestraStage, time: 600, progress: 0.25 },
      { stage: "headline" as OrchestraStage, time: 900, progress: 0.4 },
      { stage: "depth" as OrchestraStage, time: 1200, progress: 0.55 },
      { stage: "telemetry" as OrchestraStage, time: 1500, progress: 0.7 },
      { stage: "pipeline" as OrchestraStage, time: 1800, progress: 0.85 },
      { stage: "interactive" as OrchestraStage, time: 2200, progress: 0.95 },
      { stage: "ready" as OrchestraStage, time: 2500, progress: 1.0 },
    ];

    const timeouts = timeline.map((item) =>
      setTimeout(() => {
        setStage(item.stage);
        setStageProgress(item.progress);
      }, item.time)
    );

    return () => timeouts.forEach(clearTimeout);
  }, []);

  return (
    <OrchestraContext.Provider
      value={{
        stage,
        stageProgress,
        isReady: stage === "ready" || stage === "interactive",
      }}
    >
      {children}
    </OrchestraContext.Provider>
  );
}

export function useOrchestra() {
  return useContext(OrchestraContext);
}
