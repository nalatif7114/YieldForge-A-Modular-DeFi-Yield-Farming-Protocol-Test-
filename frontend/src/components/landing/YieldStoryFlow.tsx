"use client";

import { motion } from "framer-motion";
import { Wallet, Coins, Sprout, Cpu, Gift } from "lucide-react";

export function YieldStoryFlow() {
  const storySteps = [
    {
      num: "01",
      title: "Connect Web3 Wallet",
      description: "Authenticate via MetaMask or browser Web3 provider on Ethereum Sepolia.",
      icon: Wallet,
    },
    {
      num: "02",
      title: "Mint YFT Capital",
      description: "Acquire testnet YieldForge Tokens (YFT) to participate in staking pools.",
      icon: Coins,
    },
    {
      num: "03",
      title: "Lock Staking Deposit",
      description: "Deposit YFT tokens into YieldForgeStaking with reentrancy-guarded state security.",
      icon: Sprout,
    },
    {
      num: "04",
      title: "Engine Compounding",
      description: "The protocol processes TVL allocations and calculates block-by-block yield metrics.",
      icon: Cpu,
    },
    {
      num: "05",
      title: "Claim Protocol Yield",
      description: "Withdraw accrued yield directly back into your wallet or compound positions.",
      icon: Gift,
    },
  ];

  return (
    <section className="py-28 bg-[#030712] relative border-t border-white/10">
      <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        
        {/* Section Header */}
        <div className="max-w-xl mb-20 space-y-3">
          <span className="text-xs font-mono uppercase tracking-widest text-violet-400">
            System Architecture
          </span>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
            Every transaction has motion.
          </h2>
          <p className="text-sm text-slate-400 font-normal">
            A transparent 5-step pipeline linking your wallet to automated protocol compounding.
          </p>
        </div>

        {/* Workflow Pipeline */}
        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
          {storySteps.map((item, idx) => {
            const Icon = item.icon;
            return (
              <motion.div
                key={item.num}
                initial={{ opacity: 0, y: 15 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: idx * 0.08 }}
                viewport={{ once: true }}
                className="p-6 rounded-2xl bg-white/[0.02] border border-white/10 hover:border-white/20 transition-all flex flex-col justify-between group"
              >
                <div>
                  <div className="flex items-center justify-between mb-6">
                    <span className="text-xs font-mono font-bold text-violet-400">{item.num}</span>
                    <div className="p-2.5 rounded-xl bg-white/[0.04] border border-white/10 text-slate-300 group-hover:text-white transition-colors">
                      <Icon className="w-4 h-4" />
                    </div>
                  </div>
                  <h3 className="text-sm font-bold text-white mb-2">{item.title}</h3>
                  <p className="text-xs text-slate-400 leading-relaxed font-normal">{item.description}</p>
                </div>
              </motion.div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
