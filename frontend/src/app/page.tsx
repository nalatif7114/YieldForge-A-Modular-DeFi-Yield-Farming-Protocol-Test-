"use client";

import { LenisMasterTimeline } from "@/components/landing/LenisMasterTimeline";
import { InstitutionalNavbar } from "@/components/landing/InstitutionalNavbar";
import { InstitutionalHero } from "@/components/landing/InstitutionalHero";
import { SectionContinuousYield } from "@/components/landing/SectionContinuousYield";
import { SectionHowItWorks } from "@/components/landing/SectionHowItWorks";
import { SectionInteractiveArchitecture } from "@/components/landing/SectionInteractiveArchitecture";
import { SectionInstitutionalMetrics } from "@/components/landing/SectionInstitutionalMetrics";
import { SectionSecurity } from "@/components/landing/SectionSecurity";
import { SectionFinalCTA } from "@/components/landing/SectionFinalCTA";
import { InstitutionalFooter } from "@/components/landing/InstitutionalFooter";

export default function InstitutionalLandingPage() {
  return (
    <div className="flex flex-col min-h-screen bg-[#080808] text-[#F4F4F4] overflow-x-hidden selection:bg-[#D4AF37]/30 selection:text-[#F5E6B8]">
      {/* ── Lenis Smooth Scroll Timeline ── */}
      <LenisMasterTimeline />

      {/* ── Page Layout Sections ── */}
      <InstitutionalNavbar />
      <main className="flex-1">
        <InstitutionalHero />
        <SectionContinuousYield />
        <SectionHowItWorks />
        <SectionInteractiveArchitecture />
        <SectionInstitutionalMetrics />
        <SectionSecurity />
        <SectionFinalCTA />
      </main>
      <InstitutionalFooter />
    </div>
  );
}
