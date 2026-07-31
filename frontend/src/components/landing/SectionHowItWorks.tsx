"use client";

import { motion } from "framer-motion";
import { ArrowDown, Wallet, Cpu, ShieldCheck, Gift, RefreshCw } from "lucide-react";

const STEPS = [
  {
    num: "01",
    icon: Wallet,
    title: "Deposit Assets",
    description:
      "Capital enters vault endpoints via gas-optimized, non-custodial smart contracts.",
    detail: "ERC-4626 Compliant Vault Standards",
  },
  {
    num: "02",
    icon: Cpu,
    title: "Strategy Engine",
    description:
      "Smart routing algorithms evaluate liquidity pools and risk metrics for allocation.",
    detail: "Real-time Risk Parameter Checks",
  },
  {
    num: "03",
    icon: ShieldCheck,
    title: "Validators",
    description:
      "Validator network achieves BFT supermajority consensus to confirm state proofs.",
    detail: "BFT Consensus Verification",
  },
  {
    num: "04",
    icon: Gift,
    title: "Reward Distribution",
    description:
      "Harvested yield rewards stream into secondary storage vaults for distribution.",
    detail: "Zero-loss Reward Routing",
  },
  {
    num: "05",
    icon: RefreshCw,
    title: "Auto Compounding",
    description:
      "Automated execution loops continuously reinvest yields to maximize compounding APY.",
    detail: "15-minute Automated Rebalance",
  },
];

export function SectionHowItWorks() {
  return (
    <section className="py-24 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#0B0B0B]">
      <div className="max-w-5xl mx-auto space-y-16">
        
        {/* Section Header */}
        <div className="text-center max-w-2xl mx-auto space-y-4">
          <span className="text-xs font-mono text-[#E7C873] uppercase tracking-widest">
            Lifecycle Protocol
          </span>
          <h2 className="text-3xl sm:text-4xl font-bold text-[#F4F4F4] tracking-tight">
            How YieldForge Works
          </h2>
          <p className="text-sm sm:text-base text-[#A1A1AA] leading-relaxed">
            A deterministic 5-stage lifecycle for institutional yield generation.
          </p>
        </div>

        {/* Vertical Timeline */}
        <div className="relative border-l-2 border-[rgba(212,175,55,0.15)] ml-4 sm:ml-32 space-y-10 pl-6 sm:pl-10">
          {STEPS.map((step, idx) => {
            const Icon = step.icon;
            return (
              <motion.div
                key={step.num}
                initial={{ opacity: 0, x: -20 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: idx * 0.1 }}
                className="relative group"
              >
                {/* Step Circle Node on Line */}
                <div className="absolute -left-[31px] sm:-left-[47px] top-1.5 w-6 h-6 rounded-full bg-[#111111] border-2 border-[#D4AF37] flex items-center justify-center shadow-[0_0_12px_rgba(212,175,55,0.3)]">
                  <div className="w-1.5 h-1.5 rounded-full bg-[#F5E6B8]" />
                </div>

                {/* Step Card Shell */}
                <div className="gold-card p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 group-hover:border-[rgba(212,175,55,0.3)] transition-colors">
                  <div className="flex items-start gap-4">
                    <div className="w-10 h-10 rounded-lg bg-[#111111] border border-[rgba(212,175,55,0.15)] flex items-center justify-center shrink-0">
                      <Icon className="w-5 h-5 text-[#D4AF37]" />
                    </div>
                    <div className="space-y-1">
                      <div className="flex items-center gap-3">
                        <span className="text-xs font-mono text-[#D4AF37]">
                          STAGE {step.num}
                        </span>
                        <h3 className="text-base sm:text-lg font-bold text-[#F4F4F4] group-hover:text-[#F5E6B8] transition-colors">
                          {step.title}
                        </h3>
                      </div>
                      <p className="text-xs sm:text-sm text-[#A1A1AA] leading-relaxed max-w-lg">
                        {step.description}
                      </p>
                    </div>
                  </div>

                  <div className="px-3 py-1 rounded bg-[#111111] border border-[rgba(212,175,55,0.08)] text-[10px] font-mono text-[#E7C873] shrink-0">
                    {step.detail}
                  </div>
                </div>

                {idx < STEPS.length - 1 && (
                  <div className="my-2 flex justify-center sm:justify-start sm:ml-4 text-[rgba(212,175,55,0.2)]">
                    <ArrowDown className="w-4 h-4" />
                  </div>
                )}
              </motion.div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
