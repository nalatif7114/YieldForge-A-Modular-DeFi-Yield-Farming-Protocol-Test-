"use client";

import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { ConnectButton } from "@/components/ConnectButton";

const NAV_LINKS = [
  { label: "Protocol", href: "#hero" },
  { label: "Architecture", href: "#architecture" },
  { label: "Features", href: "#features" },
  { label: "Security", href: "#security" },
  { label: "Documentation", href: "#" },
];

export function InstitutionalNavbar() {
  return (
    <header className="fixed top-0 left-0 right-0 z-50 h-16 px-6 sm:px-10 lg:px-16 flex items-center justify-between backdrop-blur-xl bg-[#080808]/80 border-b border-[rgba(212,175,55,0.08)]">
      {/* ── Left: Brand Mark ── */}
      <Link href="/" className="flex items-center gap-3 group">
        <div className="w-8 h-8 rounded-lg bg-[#161616] border border-[rgba(212,175,55,0.2)] flex items-center justify-center group-hover:border-[#D4AF37] transition-colors">
          <span className="font-mono text-xs font-bold text-[#D4AF37]">YF</span>
        </div>
        <div className="flex flex-col">
          <span className="text-sm font-semibold tracking-tight text-[#F4F4F4] group-hover:text-[#F5E6B8] transition-colors">
            YieldForge
          </span>
          <span className="text-[9px] font-mono text-[#A1A1AA] uppercase tracking-widest">
            Institutional
          </span>
        </div>
      </Link>

      {/* ── Center: Nav Links ── */}
      <nav className="hidden md:flex items-center gap-1">
        {NAV_LINKS.map((link) => (
          <a
            key={link.label}
            href={link.href}
            className="px-4 py-1.5 text-xs text-[#A1A1AA] hover:text-[#F4F4F4] transition-colors duration-200 rounded-md hover:bg-white/[0.03]"
          >
            {link.label}
          </a>
        ))}
      </nav>

      {/* ── Right: Launch App + Connect Wallet ── */}
      <div className="flex items-center gap-3">
        <Link
          href="/dashboard"
          className="gold-button-primary px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5"
        >
          <span>Launch App</span>
          <ArrowUpRight className="w-3.5 h-3.5" />
        </Link>
        <ConnectButton />
      </div>
    </header>
  );
}
