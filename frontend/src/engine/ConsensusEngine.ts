/**
 * ConsensusEngine: Central source of truth for protocol consensus execution.
 * Manages protocol state transitions, validator node topology, event subscription,
 * and event emission for all dashboard components (ConsensusWave, Telemetry, NodeMesh, Packets).
 */

export type ConsensusState =
  | "IDLE"
  | "TRANSACTION_RECEIVED"
  | "VALIDATING"
  | "PROPAGATING"
  | "CONSENSUS_REACHED"
  | "STATE_COMMITTED"
  | "COMPLETE";

export interface TransactionPayload {
  id: string;
  type: "staking" | "claiming" | "transfer" | "custom";
  sender: string;
  amount?: string;
  timestamp: number;
}

export interface NodeStateInfo {
  id: number;
  label: string;
  cx: number;
  cy: number;
  status: "idle" | "validating" | "propagating" | "validated";
  signature?: string;
}

export interface ConsensusEvent {
  type:
    | "STATE_CHANGED"
    | "TRANSACTION_RECEIVED"
    | "NODE_VALIDATING"
    | "WAVE_PROPAGATED"
    | "CONSENSUS_REACHED"
    | "STATE_COMMITTED"
    | "COMPLETED";
  state: ConsensusState;
  payload?: TransactionPayload;
  activeNodes: number[];
  activeConnections: Array<{ from: number; to: number }>;
  timestamp: number;
  logMessage: string;
}

export type ConsensusListener = (event: ConsensusEvent, state: ConsensusState) => void;

class ConsensusEngineClass {
  private currentState: ConsensusState = "IDLE";
  private currentPayload: TransactionPayload | null = null;
  private listeners: Set<ConsensusListener> = new Set();
  private activeNodes: number[] = [];
  private activeConnections: Array<{ from: number; to: number }> = [];
  private stateLogHistory: Array<{ timestamp: number; state: ConsensusState; message: string }> = [];

  // Topology node definitions
  public readonly nodes: NodeStateInfo[] = [
    { id: 1, label: "Validator Node A (Primary Ingestion)", cx: 40, cy: 50, status: "idle" },
    { id: 2, label: "Validator Node B (Relay East)", cx: 160, cy: 20, status: "idle" },
    { id: 3, label: "Validator Node C (Relay West)", cx: 160, cy: 80, status: "idle" },
    { id: 4, label: "Validator Node D (State Commit Finalizer)", cx: 280, cy: 50, status: "idle" },
  ];

  public readonly connections = [
    { from: 1, to: 2 },
    { from: 1, to: 3 },
    { from: 2, to: 4 },
    { from: 3, to: 4 },
  ];

  /**
   * Subscribe a component or telemetry module to protocol consensus events.
   */
  public subscribe(listener: ConsensusListener): () => void {
    this.listeners.add(listener);
    // Send immediate initial state sync
    listener(this.createEvent("STATE_CHANGED", "Engine connected to subscriber"), this.currentState);
    return () => {
      this.listeners.delete(listener);
    };
  }

  public getState(): ConsensusState {
    return this.currentState;
  }

  public getCurrentPayload(): TransactionPayload | null {
    return this.currentPayload;
  }

  public getActiveNodes(): number[] {
    return [...this.activeNodes];
  }

  public getActiveConnections(): Array<{ from: number; to: number }> {
    return [...this.activeConnections];
  }

  public getLogHistory() {
    return [...this.stateLogHistory];
  }

  /**
   * Main protocol event trigger.
   * Advances protocol state machine through explicit protocol events.
   */
  public submitTransaction(type: "staking" | "claiming" | "transfer" = "staking", sender: string = "0x71C...39A"): Promise<void> {
    if (this.currentState !== "IDLE" && this.currentState !== "COMPLETE") {
      console.warn("ConsensusEngine: Transaction submission ignored, engine is busy processing consensus.");
      return Promise.resolve();
    }

    const payload: TransactionPayload = {
      id: `tx_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
      type,
      sender,
      amount: type === "staking" ? "100 YFT" : "12.4 EMERALD",
      timestamp: Date.now(),
    };

    this.currentPayload = payload;

    return this.executeProtocolSequence();
  }

  /**
   * Internal protocol event driven sequence execution.
   * State transitions are driven by protocol step events.
   */
  private async executeProtocolSequence(): Promise<void> {
    // 1. TRANSACTION_RECEIVED
    this.transitionTo("TRANSACTION_RECEIVED", [], [], "RPC payload ingested into mempool.");
    await this.delay(400);

    // 2. VALIDATING
    this.transitionTo("VALIDATING", [1], [], "Primary Validator Node A verifying zero-knowledge state proof.");
    await this.delay(500);

    // 3. PROPAGATING
    this.transitionTo(
      "PROPAGATING",
      [1, 2, 3],
      [
        { from: 1, to: 2 },
        { from: 1, to: 3 },
      ],
      "Consensus wave propagating state proof to Validator Nodes B & C."
    );
    await this.delay(500);

    // 4. CONSENSUS_REACHED
    this.transitionTo(
      "CONSENSUS_REACHED",
      [1, 2, 3, 4],
      [
        { from: 2, to: 4 },
        { from: 3, to: 4 },
      ],
      "BFT Supermajority threshold achieved (4/4 Validator Signatures)."
    );
    await this.delay(500);

    // 5. STATE_COMMITTED
    this.transitionTo("STATE_COMMITTED", [1, 2, 3, 4], [], "Merkle tree state root updated & committed to storage block.");
    await this.delay(600);

    // 6. COMPLETE
    this.transitionTo("COMPLETE", [], [], "Transaction finality confirmed across distributed network.");
    await this.delay(700);

    // 7. Reset back to IDLE
    this.transitionTo("IDLE", [], [], "Consensus Engine returned to standby idle state.");
  }

  private transitionTo(
    nextState: ConsensusState,
    activeNodes: number[],
    activeConnections: Array<{ from: number; to: number }>,
    logMessage: string
  ) {
    this.currentState = nextState;
    this.activeNodes = activeNodes;
    this.activeConnections = activeConnections;

    const logEntry = {
      timestamp: Date.now(),
      state: nextState,
      message: logMessage,
    };
    this.stateLogHistory.unshift(logEntry);
    if (this.stateLogHistory.length > 20) {
      this.stateLogHistory.pop();
    }

    const event = this.createEvent(
      this.getEventTypeForState(nextState),
      logMessage
    );

    this.notifySubscribers(event);
  }

  private createEvent(
    type: ConsensusEvent["type"],
    logMessage: string
  ): ConsensusEvent {
    return {
      type,
      state: this.currentState,
      payload: this.currentPayload || undefined,
      activeNodes: [...this.activeNodes],
      activeConnections: [...this.activeConnections],
      timestamp: Date.now(),
      logMessage,
    };
  }

  private getEventTypeForState(state: ConsensusState): ConsensusEvent["type"] {
    switch (state) {
      case "TRANSACTION_RECEIVED":
        return "TRANSACTION_RECEIVED";
      case "VALIDATING":
        return "NODE_VALIDATING";
      case "PROPAGATING":
        return "WAVE_PROPAGATED";
      case "CONSENSUS_REACHED":
        return "CONSENSUS_REACHED";
      case "STATE_COMMITTED":
        return "STATE_COMMITTED";
      case "COMPLETE":
        return "COMPLETED";
      default:
        return "STATE_CHANGED";
    }
  }

  private notifySubscribers(event: ConsensusEvent) {
    this.listeners.forEach((listener) => {
      try {
        listener(event, this.currentState);
      } catch (err) {
        console.error("ConsensusEngine subscriber error:", err);
      }
    });
  }

  private delay(ms: number): Promise<void> {
    return new Promise((resolve) => setTimeout(resolve, ms));
  }
}

export const ConsensusEngine = new ConsensusEngineClass();
