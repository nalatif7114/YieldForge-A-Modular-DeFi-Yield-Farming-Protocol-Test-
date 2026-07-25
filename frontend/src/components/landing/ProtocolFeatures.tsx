"use client";

import { motion } from "framer-motion";
import { ShieldCheck, Lock, Cpu, Code2, Server, Eye } from "lucide-react";

export function ProtocolFeatures() {
  const features = [
    {
      title: "Reentrancy Guarded",
      description: "State-changing operations follow Checks-Effects-Interactions patterns with OpenZeppelin protection.",
      icon: ShieldCheck,
      badge: "Security",
    },
    {
      title: "Emergency Pausable",
      description: "Circuit breaker controls allow instant pausing of deposits and withdrawals under emergency scenarios.",
      icon: Lock,
      badge: "Governance",
    },
    {
      title: "Custom Gas Errors",
      description: "Solidity 0.8+ custom revert errors eliminate costly string storage and optimize gas consumption.",
      icon: Cpu,
      badge: "EVM",
    },
    {
      title: "Type-Safe Viem Layer",
      description: "Precision RPC contract reads and transaction simulations integrated with Viem and React hooks.",
      icon: Code2,
      badge: "Web3",
    },
    {
      title: "Laravel 13 REST API",
      description: "Decoupled backend architecture ready for indexing, event listeners, and portfolio analytics.",
      icon: Server,
      badge: "Backend",
    },
    {
      title: "Procedural 3D Engine",
      description: "Real-time React Three Fiber visual diagnostics rendering validator nodes and core activity.",
      icon: Eye,
      badge: "Graphics",
    },
  ];

  return (
    <section id="architecture" className="py-28 bg-[#030712] relative border-t border-white/10">
      <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        
        <div className="max-w-xl mb-20 space-y-3">
          <span className="text-xs font-mono uppercase tracking-widest text-violet-400">
            Engineering Precision
          </span>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
            Protocol Architecture
          </h2>
          <p className="text-sm text-slate-400 font-normal">
            Modular smart contracts paired with high-performance Web3 client infrastructure.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, idx) => {
            const Icon = feature.icon;
            return (
              <motion.div
                key={feature.title}
                initial={{ opacity: 0, y: 15 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: idx * 0.06 }}
                viewport={{ once: true }}
                className="p-7 rounded-2xl bg-white/[0.02] border border-white/10 hover:border-white/20 transition-all group"
              >
                <div className="flex items-center justify-between mb-5">
                  <div className="p-3 rounded-xl bg-white/[0.04] border border-white/10 text-slate-200 group-hover:text-violet-400 transition-colors">
                    <Icon className="w-4 h-4" />
                  </div>
                  <span className="text-[10px] font-mono font-medium px-2.5 py-0.5 rounded-full bg-white/[0.04] text-slate-400 border border-white/10">
                    {feature.badge}
                  </span>
                </div>
                <h3 className="text-base font-bold text-white mb-2">{feature.title}</h3>
                <p className="text-xs text-slate-400 leading-relaxed font-normal">{feature.description}</p>
              </motion.div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
