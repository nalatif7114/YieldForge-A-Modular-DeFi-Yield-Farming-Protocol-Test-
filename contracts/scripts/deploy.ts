import { ethers, network } from "hardhat";
import * as fs from "fs";
import * as path from "path";

async function main() {
  console.log(`====================================================`);
  console.log(`Starting YieldForgeToken Deployment on network: [ ${network.name} ]`);
  console.log(`====================================================`);

  const [deployer] = await ethers.getSigners();
  console.log(`Deployer Address: ${deployer.address}`);

  const balance = await ethers.provider.getBalance(deployer.address);
  console.log(`Deployer ETH Balance: ${ethers.formatEther(balance)} ETH`);

  // Deploy YieldForgeToken contract
  const YieldForgeTokenFactory = await ethers.getContractFactory("YieldForgeToken");
  const token = await YieldForgeTokenFactory.deploy(deployer.address);
  await token.waitForDeployment();

  const tokenAddress = await token.getAddress();
  console.log(`YieldForgeToken (YFT) deployed successfully to: ${tokenAddress}`);

  // Mint initial test supply to deployer for local testing (e.g. 100,000 YFT)
  const INITIAL_MINT = ethers.parseEther("100000");
  const mintTx = await token.mint(deployer.address, INITIAL_MINT);
  await mintTx.wait();
  console.log(`Minted ${ethers.formatEther(INITIAL_MINT)} YFT test tokens to deployer: ${deployer.address}`);

  // Export Artifacts (ABI & Deployed Address) for Frontend & Record-keeping
  const artifactPath = path.join(__dirname, "../artifacts/contracts/YieldForgeToken.sol/YieldForgeToken.json");
  const artifactRaw = fs.readFileSync(artifactPath, "utf8");
  const artifact = JSON.parse(artifactRaw);

  const deploymentData = {
    network: network.name,
    chainId: network.config.chainId || 31337,
    address: tokenAddress,
    deployer: deployer.address,
    deployedAt: new Date().toISOString(),
    abi: artifact.abi,
  };

  // Save to contracts/deployments/<network>.json
  const deploymentsDir = path.join(__dirname, "../deployments");
  if (!fs.existsSync(deploymentsDir)) {
    fs.mkdirSync(deploymentsDir, { recursive: true });
  }
  const deploymentFilePath = path.join(deploymentsDir, `${network.name}.json`);
  fs.writeFileSync(deploymentFilePath, JSON.stringify(deploymentData, null, 2));
  console.log(`Saved contract deployment metadata to: ${deploymentFilePath}`);

  // Also export contract data to frontend/src/contracts/YieldForgeToken.json for seamless Web3 integration
  const frontendContractsDir = path.join(__dirname, "../../frontend/src/contracts");
  if (!fs.existsSync(frontendContractsDir)) {
    fs.mkdirSync(frontendContractsDir, { recursive: true });
  }
  fs.writeFileSync(
    path.join(frontendContractsDir, "YieldForgeToken.json"),
    JSON.stringify(deploymentData, null, 2)
  );
  console.log(`Exported ABI & Address to Frontend at: ${path.join(frontendContractsDir, "YieldForgeToken.json")}`);
  console.log(`====================================================`);
}

main().catch((error) => {
  console.error("Deployment failed with error:", error);
  process.exitCode = 1;
});
