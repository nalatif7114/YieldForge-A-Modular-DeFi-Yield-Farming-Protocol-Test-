"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { ShieldCheck, Vault, Cpu, GitMerge, Gift } from "lucide-react";

const ARCH_COMPONENTS = [
  {
    id: "validator",
    name: "Validator Network",
    icon: ShieldCheck,
    tagline: "BFT Consensus & State Proof Verification",
    stats: [
      { label: "Active Nodes", value: "8 Endpoints" },
      { label: "Consensus Model", value: "BFT Supermajority" },
      { label: "Proof Latency", value: "120ms" },
      { label: "Fault Tolerance", value: "33% Byzantine" },
    ],
    description:
      "A distributed network of 8 validator endpoints executing real-time proof validation and block state root signatures before state commitment.",
  },
  {
    id: "vault",
    name: "Smart Vaults",
    icon: Vault,
    tagline: "ERC-4626 Tokenized Yield Storage",
    stats: [
      { label: "Vault Standard", value: "ERC-4626" },
      { label: "Deposit Fee", value: "0.00%" },
      { label: "Withdrawal Delay", value: "Instant / Zero Timelock" },
      { label: "Audited By", value: "OpenZeppelin" },
    ],
    description:
      "Institutional-grade vault architecture supporting automated yield token minting, asset custody, and multi-collateral backing.",
  },
  {
    id: "strategy",
    name: "Strategy Engine",
    icon: Cpu,
    tagline: "Autonomous Yield Routing & Rebalancing",
    stats: [
      { label: "Execution Loop", value: "15-minute Auto-compound" },
      { label: "Slippage Max", value: "0.10%" },
      { label: "Max TVL / Pool", value: "$50.0M" },
      { label: "Gas Optimization", value: "Batched Proofs" },
    ],
    description:
      "Algorithmic execution engine continuously scanning liquidity pools to optimize risk-adjusted APY without manual intervention.",
  },
  {
    id: "bridge",
    name: "Cross-Chain Bridge",
    icon: GitMerge,
    tagline: "Zero-Knowledge Asset Teleportation",
    stats: [
      { label: "Supported Chains", value: "Ethereum, Sepolia, Arbitrum" },
      { label: "Verification", value: "ZK-SNARK Proofs" },
      { label: "Relay Time", value: "< 45 Seconds" },
      { label: "Security Layer", value: "Multi-Sig Timelock" },
    ],
    description:
      "Trustless cross-chain liquidity relaying enabling seamless asset routing across Layer-1 and Layer-2 execution environments.",
  },
  {
    id: "rewards",
    name: "Reward Collector",
    icon: Gift,
    tagline: "Automated Yield Harvesting & Distribution",
    stats: [
      { label: "Reward Token", value: "EMERALD / YFT" },
      { label: "Distribution Cycle", value: "Continuous Epoch" },
      { label: "Auto-Reinvest", value: "Enabled 100%" },
      { label: "Fee Cut", value: "0.00% Protocols" },
    ],
    description:
      "Harvesting module extracting secondary protocol incentives and automatically compounding them back into primary vault shares.",
  },
];

export function SectionInteractiveArchitecture() {
  const [selectedId, setSelectedId] = useState("validator");

  const activeComp =
    ARCH_COMPONENTS.find((c) => c.id === selectedId) || ARCH_COMPONENTS[0];

  return (
    <section id="architecture" className="py-24 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#080808]">
      <div className="max-w-7xl mx-auto space-y-16">
        
        {/* Header */}
        <div className="text-center max-w-2xl mx-auto space-y-4">
          <span className="text-xs font-mono text-[#E7C873] uppercase tracking-widest">
            System Inspection
          </span>
          <h2 className="text-3xl sm:text-4xl font-bold text-[#F4F4F4] tracking-tight">
            Interactive Architecture
          </h2>
          <p className="text-sm sm:text-base text-[#A1A1AA] leading-relaxed">
            Inspect the core components powering YieldForge's institutional infrastructure.
          </p>
        </div>

        {/* Tab Selector Buttons */}
        <div className="flex flex-wrap items-center justify-center gap-3">
          {ARCH_COMPONENTS.map((comp) => {
            const Icon = comp.icon;
            const isSelected = comp.id === selectedId;

            return (
              <button
                key={comp.id}
                onClick={() => setSelectedId(comp.id)}
                className={`px-5 py-3 rounded-xl text-xs font-mono flex items-center gap-2.5 transition-all duration-300 ${
                  isSelected
                    ? "bg-[#D4AF37] text-[#080808] font-bold shadow-[0_0_20px_rgba(212,175,55,0.3)]"
                    : "bg-[#161616] text-[#A1A1AA] border border-[rgba(212,175,55,0.08)] hover:text-[#F4F4F4] hover:border-[rgba(212,175,55,0.2)]"
                }`}
              >
                <Icon className={`w-4 h-4 ${isSelected ? "text-[#080808]" : "text-[#D4AF37]"}`} />
                <span>{comp.name}</span>
              </button>
            );
          })}
        </div>

        {/* Component Display Card */}
        <AnimatePresence mode="wait">
          <motion.div
            key={activeComp.id}
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -15 }}
            transition={{ duration: 0.35 }}
            className="gold-card p-8 sm:p-12 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center"
          >
            {/* Left Specification (7 Cols) */}
            <div className="lg:col-span-7 space-y-6">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#111111] border border-[rgba(212,175,55,0.15)] text-[10px] font-mono text-[#E7C873]">
                <span>MODULE KEY: {activeComp.id.toUpperCase()}</span>
              </div>

              <h3 className="text-2xl sm:text-3xl font-bold text-[#F4F4F4]">
                {activeComp.name}
              </h3>
              <p className="text-sm font-mono text-[#D4AF37]">
                {activeComp.tagline}
              </p>
              <p className="text-xs sm:text-sm text-[#A1A1AA] leading-relaxed">
                {activeComp.description}
              </p>
            </div>

            {/* Right Stats Matrix (5 Cols) */}
            <div className="lg:col-span-5 grid grid-cols-2 gap-4">
              {activeComp.stats.map((st) => (
                <div
                  key={st.label}
                  className="p-4 rounded-xl bg-[#111111] border border-[rgba(212,175,55,0.08)] space-y-1"
                >
                  <span className="text-[10px] font-mono uppercase tracking-wider text-[#A1A1AA]">
                    {st.label}
                  </span>
                  <div className="text-sm sm:text-base font-bold font-mono text-[#F4F4F4]">
                    {st.value}
                  </div>
                </div>
              ))}
            </div>
          </motion.div>
        </AnimatePresence>

      </div>
    </section>
  );
}
