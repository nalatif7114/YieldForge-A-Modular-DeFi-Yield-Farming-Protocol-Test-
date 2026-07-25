import { ethers, network } from "hardhat";
import * as fs from "fs";
import * as path from "path";

async function main() {
  console.log(`====================================================`);
  console.log(`Starting YieldForge Multi-Contract Deployment: [ ${network.name} ]`);
  console.log(`====================================================`);

  const [deployer] = await ethers.getSigners();
  console.log(`Deployer Address: ${deployer.address}`);

  const balance = await ethers.provider.getBalance(deployer.address);
  console.log(`Deployer ETH Balance: ${ethers.formatEther(balance)} ETH`);

  // 1. Deploy YieldForgeToken (YFT)
  const YieldForgeTokenFactory = await ethers.getContractFactory("YieldForgeToken");
  const token = await YieldForgeTokenFactory.deploy(deployer.address);
  await token.waitForDeployment();
  const tokenAddress = await token.getAddress();
  console.log(`YieldForgeToken (YFT) deployed to: ${tokenAddress}`);

  // 2. Deploy YieldForgeStaking
  const YieldForgeStakingFactory = await ethers.getContractFactory("YieldForgeStaking");
  const staking = await YieldForgeStakingFactory.deploy(tokenAddress, deployer.address);
  await staking.waitForDeployment();
  const stakingAddress = await staking.getAddress();
  console.log(`YieldForgeStaking deployed to: ${stakingAddress}`);

  // 3. Mint initial test supply to deployer for testing (e.g. 100,000 YFT)
  const INITIAL_MINT = ethers.parseEther("100000");
  const mintTx = await token.mint(deployer.address, INITIAL_MINT);
  await mintTx.wait();
  console.log(`Minted ${ethers.formatEther(INITIAL_MINT)} YFT test tokens to deployer.`);

  // 4. Read Artifact ABIs
  const tokenArtifactPath = path.join(__dirname, "../artifacts/contracts/YieldForgeToken.sol/YieldForgeToken.json");
  const tokenArtifact = JSON.parse(fs.readFileSync(tokenArtifactPath, "utf8"));

  const stakingArtifactPath = path.join(__dirname, "../artifacts/contracts/YieldForgeStaking.sol/YieldForgeStaking.json");
  const stakingArtifact = JSON.parse(fs.readFileSync(stakingArtifactPath, "utf8"));

  const deploymentMetaData = {
    network: network.name,
    chainId: network.config.chainId || 31337,
    deployer: deployer.address,
    deployedAt: new Date().toISOString(),
    contracts: {
      YieldForgeToken: {
        address: tokenAddress,
        abi: tokenArtifact.abi,
      },
      YieldForgeStaking: {
        address: stakingAddress,
        abi: stakingArtifact.abi,
      },
    },
  };

  // 5. Save to contracts/deployments/<network>.json
  const deploymentsDir = path.join(__dirname, "../deployments");
  if (!fs.existsSync(deploymentsDir)) {
    fs.mkdirSync(deploymentsDir, { recursive: true });
  }
  const deploymentFilePath = path.join(deploymentsDir, `${network.name}.json`);
  fs.writeFileSync(deploymentFilePath, JSON.stringify(deploymentMetaData, null, 2));
  console.log(`Saved deployment log to: ${deploymentFilePath}`);

  // 6. Save individually into frontend/src/contracts/ for clean React hook access
  const frontendDir = path.join(__dirname, "../../frontend/src/contracts");
  if (!fs.existsSync(frontendDir)) {
    fs.mkdirSync(frontendDir, { recursive: true });
  }

  fs.writeFileSync(
    path.join(frontendDir, "YieldForgeToken.json"),
    JSON.stringify({ address: tokenAddress, abi: tokenArtifact.abi }, null, 2)
  );

  fs.writeFileSync(
    path.join(frontendDir, "YieldForgeStaking.json"),
    JSON.stringify({ address: stakingAddress, abi: stakingArtifact.abi }, null, 2)
  );

  fs.writeFileSync(
    path.join(frontendDir, "deployments.json"),
    JSON.stringify(deploymentMetaData, null, 2)
  );

  console.log(`Exported YFT & Staking ABIs to Frontend at: ${frontendDir}`);
  console.log(`====================================================`);
}

main().catch((error) => {
  console.error("Deployment script failed:", error);
  process.exitCode = 1;
});
