"use client";

import { motion } from "framer-motion";
import { ShieldCheck, Clock, FileCheck, Eye, KeyRound } from "lucide-react";

const SECURITY_ITEMS = [
  {
    icon: ShieldCheck,
    title: "OpenZeppelin Standards",
    description:
      "Vault and contract implementations inherit battle-tested OpenZeppelin security contracts.",
  },
  {
    icon: Clock,
    title: "48-Hour Timelock",
    description:
      "Critical protocol parameter updates require a mandatory 48-hour timelock delay for governance inspection.",
  },
  {
    icon: FileCheck,
    title: "Audits & Verification",
    description:
      "All smart contracts undergo rigorous formal verification and third-party security audits.",
  },
  {
    icon: KeyRound,
    title: "Permissionless & Non-Custodial",
    description:
      "Users retain full cryptographic custody of assets with instant withdrawal capabilities.",
  },
  {
    icon: Eye,
    title: "Transparent Execution",
    description:
      "On-chain transactions and validator consensus state proofs are 100% publicly observable.",
  },
];

export function SectionSecurity() {
  return (
    <section id="security" className="py-24 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#080808]">
      <div className="max-w-7xl mx-auto space-y-16">
        
        {/* Header */}
        <div className="text-center max-w-2xl mx-auto space-y-4">
          <span className="text-xs font-mono text-[#E7C873] uppercase tracking-widest">
            Capital Protection
          </span>
          <h2 className="text-3xl sm:text-4xl font-bold text-[#F4F4F4] tracking-tight">
            Institutional-grade Security
          </h2>
          <p className="text-sm sm:text-base text-[#A1A1AA] leading-relaxed">
            Multi-layered security architecture ensuring capital protection and verifiable code integrity.
          </p>
        </div>

        {/* Security Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {SECURITY_ITEMS.map((item, idx) => {
            const Icon = item.icon;
            return (
              <motion.div
                key={item.title}
                initial={{ opacity: 0, y: 15 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: idx * 0.08 }}
                className="gold-card p-8 space-y-4 group hover:border-[rgba(212,175,55,0.3)] transition-all duration-300"
              >
                <div className="w-10 h-10 rounded-lg bg-[#111111] border border-[rgba(212,175,55,0.15)] flex items-center justify-center group-hover:border-[#D4AF37] transition-colors">
                  <Icon className="w-5 h-5 text-[#D4AF37]" />
                </div>
                <h3 className="text-lg font-bold text-[#F4F4F4] group-hover:text-[#F5E6B8] transition-colors">
                  {item.title}
                </h3>
                <p className="text-xs text-[#A1A1AA] leading-relaxed">
                  {item.description}
                </p>
              </motion.div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
