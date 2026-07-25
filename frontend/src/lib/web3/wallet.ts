/**
 * Wallet Provider Utility
 * 
 * Interacts directly with browser Ethereum providers (e.g., MetaMask, Rabby).
 * Manages account detection, chain validation, and network switching.
 */

export interface EthereumProvider {
  isMetaMask?: boolean;
  request: (args: { method: string; params?: Array<any> }) => Promise<any>;
  on: (eventName: string, handler: (...args: any[]) => void) => void;
  removeListener: (eventName: string, handler: (...args: any[]) => void) => void;
}

declare global {
  interface Window {
    ethereum?: EthereumProvider;
  }
}

/**
 * Checks if a Web3 wallet provider (e.g., MetaMask) is injected in the browser window.
 */
export function hasWalletProvider(): boolean {
  return typeof window !== "undefined" && typeof window.ethereum !== "undefined";
}

/**
 * Requests wallet account access from the user.
 * @returns Array of connected Ethereum addresses.
 */
export async function connectWallet(): Promise<string[]> {
  if (!hasWalletProvider()) {
    throw new Error("No Web3 wallet provider detected. Please install MetaMask.");
  }
  
  const accounts: string[] = await window.ethereum!.request({
    method: "eth_requestAccounts",
  });

  return accounts;
}

/**
 * Gets currently connected Ethereum accounts without prompting a popup.
 */
export async function getConnectedAccounts(): Promise<string[]> {
  if (!hasWalletProvider()) return [];
  try {
    const accounts: string[] = await window.ethereum!.request({
      method: "eth_accounts",
    });
    return accounts;
  } catch (error) {
    console.error("Failed to fetch connected accounts:", error);
    return [];
  }
}

/**
 * Gets current chain ID in hexadecimal format from the wallet.
 */
export async function getWalletChainId(): Promise<number> {
  if (!hasWalletProvider()) return 0;
  const hexChainId: string = await window.ethereum!.request({
    method: "eth_chainId",
  });
  return parseInt(hexChainId, 16);
}

/**
 * Requests the wallet to switch to the target chain ID.
 * @param targetChainId Numeric target chain ID (e.g. 31337 for Hardhat, 11155111 for Sepolia).
 */
export async function switchNetwork(targetChainId: number): Promise<void> {
  if (!hasWalletProvider()) return;
  const hexChainId = `0x${targetChainId.toString(16)}`;
  
  try {
    await window.ethereum!.request({
      method: "wallet_switchEthereumChain",
      params: [{ chainId: hexChainId }],
    });
  } catch (switchError: any) {
    console.error("Failed to switch network:", switchError);
    throw switchError;
  }
}
