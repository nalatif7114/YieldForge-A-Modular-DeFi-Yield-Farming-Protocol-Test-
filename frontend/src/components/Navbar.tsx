"use client";

import Link from "next/link";
import { ConnectButton } from "./ConnectButton";

export function Navbar() {
  return (
    <header className="fixed top-0 left-0 right-0 z-50 backdrop-blur-xl bg-[#030712]/80 border-b border-white/10">
      <div className="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 h-20 flex items-center justify-between">
        
        {/* Brand Logo */}
        <Link href="/" className="flex items-center gap-3 group">
          <div className="w-9 h-9 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-center text-lg group-hover:border-violet-500/40 transition-colors">
            🌾
          </div>
          <div>
            <div className="flex items-center gap-2">
              <span className="text-base font-bold text-white tracking-tight">YieldForge</span>
              <span className="px-2 py-0.5 text-[10px] font-mono rounded-full bg-violet-500/10 text-violet-400 border border-violet-500/20">
                Sepolia
              </span>
            </div>
          </div>
        </Link>

        {/* Navigation Links & Wallet Action */}
        <div className="flex items-center gap-6">
          <Link href="/dashboard" className="text-xs font-medium text-slate-300 hover:text-white transition-colors">
            Dashboard
          </Link>
          <a href="#architecture" className="text-xs font-medium text-slate-300 hover:text-white transition-colors hidden sm:block">
            Architecture
          </a>
          <ConnectButton />
        </div>

      </div>
    </header>
  );
}
