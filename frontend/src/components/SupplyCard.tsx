"use client";

import { useWallet } from "@/hooks/useWallet";
import { useTokenBalance } from "@/hooks/useTokenBalance";
import { TOKEN_ADDRESS } from "@/lib/web3/contracts";

export function SupplyCard() {
  const { account } = useWallet();
  const { formattedTotalSupply, isLoading } = useTokenBalance(account);

  if (isLoading) {
    return (
      <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm animate-pulse">
        <div className="h-4 w-32 bg-slate-800 rounded mb-4"></div>
        <div className="h-9 w-44 bg-slate-800 rounded mb-2"></div>
        <div className="h-3 w-28 bg-slate-800/60 rounded"></div>
      </div>
    );
  }

  return (
    <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm relative overflow-hidden group hover:border-teal-500/40 transition-all">
      <div className="absolute -right-8 -top-8 w-28 h-28 bg-teal-500/10 rounded-full blur-2xl group-hover:bg-teal-500/20 transition-all pointer-events-none"></div>

      <div className="flex items-center justify-between">
        <span className="text-xs font-medium uppercase tracking-wider text-slate-400">Total YFT Supply</span>
        <span className="px-2 py-0.5 text-[10px] font-medium rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/20">
          ERC-20
        </span>
      </div>

      <div className="mt-4">
        <div className="flex items-baseline gap-2">
          <span className="text-3xl font-extrabold text-white tracking-tight">
            {formattedTotalSupply}
          </span>
          <span className="text-sm font-semibold text-teal-400">YFT</span>
        </div>
        <p className="text-xs text-slate-500 mt-1 break-all">
          Contract: <span className="font-mono text-slate-400">{TOKEN_ADDRESS.substring(0, 8)}...{TOKEN_ADDRESS.substring(TOKEN_ADDRESS.length - 6)}</span>
        </p>
      </div>
    </div>
  );
}
