"use client";

import { motion } from "framer-motion";
import { Cpu, ShieldAlert, Layers, Zap } from "lucide-react";

const FEATURES = [
  {
    icon: Cpu,
    title: "Automated Strategies",
    description:
      "Algorithmic auto-compounding across liquidity pools with zero manual overhead or gas inefficiency.",
  },
  {
    icon: Zap,
    title: "Validator Consensus",
    description:
      "BFT supermajority proof verification ensuring state root integrity before block finality.",
  },
  {
    icon: Layers,
    title: "Composable Vaults",
    description:
      "Modular smart vault architecture allowing custom risk-adjusted allocation models for treasury capital.",
  },
  {
    icon: ShieldAlert,
    title: "Risk Management",
    description:
      "Real-time state telemetry, automated circuit breakers, and multi-sig security constraints.",
  },
];

export function SectionContinuousYield() {
  return (
    <section id="features" className="py-24 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#080808]">
      <div className="max-w-7xl mx-auto space-y-16">
        
        {/* Section Header */}
        <div className="text-center max-w-2xl mx-auto space-y-4">
          <span className="text-xs font-mono text-[#E7C873] uppercase tracking-widest">
            Protocol Engineering
          </span>
          <h2 className="text-3xl sm:text-4xl font-bold text-[#F4F4F4] tracking-tight">
            Built for Continuous Yield
          </h2>
          <p className="text-sm sm:text-base text-[#A1A1AA] leading-relaxed">
            Engineered to maximize capital utilization through autonomous, risk-managed yield strategies.
          </p>
        </div>

        {/* 4 Feature Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {FEATURES.map((feat, index) => {
            const Icon = feat.icon;
            return (
              <motion.div
                key={feat.title}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="gold-card p-8 flex flex-col justify-between group hover:border-[rgba(212,175,55,0.3)] transition-all duration-300 hover:-translate-y-1"
              >
                <div className="space-y-5">
                  <div className="w-12 h-12 rounded-xl bg-[#111111] border border-[rgba(212,175,55,0.15)] flex items-center justify-center group-hover:border-[#D4AF37] transition-colors">
                    <Icon className="w-6 h-6 text-[#D4AF37]" />
                  </div>
                  <h3 className="text-lg font-bold text-[#F4F4F4] group-hover:text-[#F5E6B8] transition-colors">
                    {feat.title}
                  </h3>
                  <p className="text-xs text-[#A1A1AA] leading-relaxed">
                    {feat.description}
                  </p>
                </div>

                <div className="pt-6 mt-6 border-t border-[rgba(212,175,55,0.06)] flex items-center justify-between text-[10px] font-mono text-[#A1A1AA]">
                  <span>SYSTEM LAYER 0{index + 1}</span>
                  <span className="text-[#D4AF37]">ACTIVE</span>
                </div>
              </motion.div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
