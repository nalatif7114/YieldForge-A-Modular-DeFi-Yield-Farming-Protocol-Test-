"use client";

import { motion } from "framer-motion";

const METRICS = [
  {
    label: "Total Value Locked (TVL)",
    value: "$248.5M+",
    sub: "Secured in Smart Vaults",
    trend: "+24.8% YoY",
  },
  {
    label: "Active Yield Strategies",
    value: "12",
    sub: "Automated Allocation Pools",
    trend: "100% Audited",
  },
  {
    label: "Processed Transactions",
    value: "1.2M+",
    sub: "On-Chain Consensus Proofs",
    trend: "Zero Failures",
  },
  {
    label: "Protocol Uptime",
    value: "99.98%",
    sub: "Continuous State Availability",
    trend: "BFT Verified",
  },
];

export function SectionInstitutionalMetrics() {
  return (
    <section className="py-24 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#0B0B0B]">
      <div className="max-w-7xl mx-auto space-y-16">
        
        {/* Header */}
        <div className="text-center max-w-2xl mx-auto space-y-4">
          <span className="text-xs font-mono text-[#E7C873] uppercase tracking-widest">
            Institutional Performance
          </span>
          <h2 className="text-3xl sm:text-4xl font-bold text-[#F4F4F4] tracking-tight">
            Institutional Metrics
          </h2>
          <p className="text-sm sm:text-base text-[#A1A1AA] leading-relaxed">
            Battle-tested infrastructure operating at institutional scale.
          </p>
        </div>

        {/* 4 Metric Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {METRICS.map((m, idx) => (
            <motion.div
              key={m.label}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: idx * 0.1 }}
              className="gold-card p-8 space-y-4 flex flex-col justify-between hover:border-[rgba(212,175,55,0.3)] transition-all duration-300 hover:-translate-y-1"
            >
              <div className="space-y-2">
                <span className="text-xs font-mono text-[#A1A1AA] uppercase tracking-wider">
                  {m.label}
                </span>
                <div className="text-4xl sm:text-5xl font-extrabold font-mono text-[#F4F4F4] gold-gradient-text tracking-tight">
                  {m.value}
                </div>
              </div>

              <div className="pt-4 border-t border-[rgba(212,175,55,0.06)] flex items-center justify-between text-[11px] font-mono">
                <span className="text-[#A1A1AA]">{m.sub}</span>
                <span className="text-[#E7C873] font-semibold">{m.trend}</span>
              </div>
            </motion.div>
          ))}
        </div>

      </div>
    </section>
  );
}
