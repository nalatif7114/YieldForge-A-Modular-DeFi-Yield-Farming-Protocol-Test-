import { Navbar } from "@/components/Navbar";
import { HeroSection } from "@/components/landing/HeroSection";
import { YieldStoryFlow } from "@/components/landing/YieldStoryFlow";
import { ProtocolFeatures } from "@/components/landing/ProtocolFeatures";

export default function LandingPage() {
  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 selection:bg-emerald-500/30 selection:text-emerald-300">
      <Navbar />
      <main>
        <HeroSection />
        <YieldStoryFlow />
        <ProtocolFeatures />
      </main>
      <footer className="border-t border-slate-800/80 py-8 bg-slate-950 text-center text-xs text-slate-500">
        <p>YieldForge — A Modular DeFi Yield Farming Protocol Simulation</p>
      </footer>
    </div>
  );
}
