"use client";

import Link from "next/link";
import { Code2, Globe, MessageSquare, BookOpen } from "lucide-react";

export function InstitutionalFooter() {
  return (
    <footer className="py-12 px-6 sm:px-10 lg:px-16 border-t border-[rgba(212,175,55,0.08)] bg-[#080808] text-[#A1A1AA] text-xs">
      <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
        
        {/* Brand Mark */}
        <div className="flex items-center gap-3">
          <div className="w-7 h-7 rounded-lg bg-[#161616] border border-[rgba(212,175,55,0.2)] flex items-center justify-center">
            <span className="font-mono text-[10px] font-bold text-[#D4AF37]">YF</span>
          </div>
          <div className="flex flex-col">
            <span className="text-sm font-semibold tracking-tight text-[#F4F4F4]">
              YieldForge Protocol
            </span>
            <span className="text-[10px] font-mono text-[#A1A1AA]">
              Modular Institutional Infrastructure
            </span>
          </div>
        </div>

        {/* Links */}
        <div className="flex items-center gap-6 font-mono text-[11px]">
          <a
            href="https://github.com"
            target="_blank"
            rel="noreferrer"
            className="hover:text-[#F5E6B8] transition-colors flex items-center gap-1.5"
          >
            <Code2 className="w-3.5 h-3.5 text-[#D4AF37]" />
            <span>GitHub</span>
          </a>

          <a
            href="#documentation"
            className="hover:text-[#F5E6B8] transition-colors flex items-center gap-1.5"
          >
            <BookOpen className="w-3.5 h-3.5 text-[#D4AF37]" />
            <span>Docs</span>
          </a>

          <a
            href="https://twitter.com"
            target="_blank"
            rel="noreferrer"
            className="hover:text-[#F5E6B8] transition-colors flex items-center gap-1.5"
          >
            <Globe className="w-3.5 h-3.5 text-[#D4AF37]" />
            <span>X / Twitter</span>
          </a>

          <a
            href="https://discord.com"
            target="_blank"
            rel="noreferrer"
            className="hover:text-[#F5E6B8] transition-colors flex items-center gap-1.5"
          >
            <MessageSquare className="w-3.5 h-3.5 text-[#D4AF37]" />
            <span>Community</span>
          </a>
        </div>

        {/* Copyright */}
        <div className="font-mono text-[11px] text-[#A1A1AA]">
          © {new Date().getFullYear()} YieldForge Protocol. All rights reserved.
        </div>

      </div>
    </footer>
  );
}
