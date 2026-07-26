"use client";

import { useState, useEffect } from "react";
import {
  ConsensusEngine,
  ConsensusState,
  ConsensusEvent,
  TransactionPayload,
} from "@/engine/ConsensusEngine";

export interface UseConsensusEngineReturn {
  state: ConsensusState;
  payload: TransactionPayload | null;
  activeNodes: number[];
  activeConnections: Array<{ from: number; to: number }>;
  logHistory: Array<{ timestamp: number; state: ConsensusState; message: string }>;
  lastEvent: ConsensusEvent | null;
  submitTransaction: (type?: "staking" | "claiming" | "transfer", sender?: string) => Promise<void>;
  nodes: typeof ConsensusEngine.nodes;
  connections: typeof ConsensusEngine.connections;
  isProcessing: boolean;
}

export function useConsensusEngine(): UseConsensusEngineReturn {
  const [state, setState] = useState<ConsensusState>(() => ConsensusEngine.getState());
  const [payload, setPayload] = useState<TransactionPayload | null>(() =>
    ConsensusEngine.getCurrentPayload()
  );
  const [activeNodes, setActiveNodes] = useState<number[]>(() =>
    ConsensusEngine.getActiveNodes()
  );
  const [activeConnections, setActiveConnections] = useState<Array<{ from: number; to: number }>>(() =>
    ConsensusEngine.getActiveConnections()
  );
  const [logHistory, setLogHistory] = useState(() => ConsensusEngine.getLogHistory());
  const [lastEvent, setLastEvent] = useState<ConsensusEvent | null>(null);

  useEffect(() => {
    const unsubscribe = ConsensusEngine.subscribe((event, newState) => {
      setState(newState);
      setPayload(ConsensusEngine.getCurrentPayload());
      setActiveNodes(ConsensusEngine.getActiveNodes());
      setActiveConnections(ConsensusEngine.getActiveConnections());
      setLogHistory(ConsensusEngine.getLogHistory());
      setLastEvent(event);
    });

    return () => {
      unsubscribe();
    };
  }, []);

  const isProcessing = state !== "IDLE" && state !== "COMPLETE";

  return {
    state,
    payload,
    activeNodes,
    activeConnections,
    logHistory,
    lastEvent,
    submitTransaction: (type, sender) => ConsensusEngine.submitTransaction(type, sender),
    nodes: ConsensusEngine.nodes,
    connections: ConsensusEngine.connections,
    isProcessing,
  };
}
