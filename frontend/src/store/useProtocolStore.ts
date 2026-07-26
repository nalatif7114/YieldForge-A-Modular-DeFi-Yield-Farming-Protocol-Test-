import { create } from "zustand";

export type ProtocolTxState = "idle" | "staking" | "unstaking" | "claiming";

interface ProtocolState {
  isWalletConnected: boolean;
  stakedBalance: bigint;
  txState: ProtocolTxState;
  rewardEnergyPulse: number;
  scrollProgress: number;
  mousePosition: { x: number; y: number };
  
  // State setters
  setWalletConnected: (connected: boolean) => void;
  setStakedBalance: (balance: bigint) => void;
  setScrollProgress: (progress: number) => void;
  setMousePosition: (pos: { x: number; y: number }) => void;
  triggerStakingEvent: () => void;
  triggerClaimEvent: () => void;
}

export const useProtocolStore = create<ProtocolState>((set) => ({
  isWalletConnected: false,
  stakedBalance: 0n,
  txState: "idle",
  rewardEnergyPulse: 0,
  scrollProgress: 0,
  mousePosition: { x: 0, y: 0 },

  setWalletConnected: (connected) =>
    set({ isWalletConnected: connected }),

  setStakedBalance: (balance) =>
    set({ stakedBalance: balance }),

  setScrollProgress: (progress) =>
    set({ scrollProgress: progress }),

  setMousePosition: (pos) =>
    set({ mousePosition: pos }),

  triggerStakingEvent: () => {
    set({ txState: "staking" });
    setTimeout(() => set({ txState: "idle" }), 4500);
  },

  triggerClaimEvent: () => {
    set({ txState: "claiming", rewardEnergyPulse: 1 });
    setTimeout(() => set({ txState: "idle", rewardEnergyPulse: 0 }), 4500);
  },
}));
