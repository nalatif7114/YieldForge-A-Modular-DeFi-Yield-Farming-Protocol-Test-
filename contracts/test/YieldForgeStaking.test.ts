import { expect } from "chai";
import { ethers } from "hardhat";
import { YieldForgeToken, YieldForgeStaking } from "../typechain-types";
import { SignerWithAddress } from "@nomicfoundation/hardhat-ethers/signers";

describe("YieldForgeStaking - Phase 4 Unit Tests", function () {
  let token: YieldForgeToken;
  let staking: YieldForgeStaking;
  let owner: SignerWithAddress;
  let user1: SignerWithAddress;
  let user2: SignerWithAddress;

  const INITIAL_MINT = ethers.parseEther("10000"); // 10,000 YFT
  const STAKE_AMOUNT_1 = ethers.parseEther("1000"); // 1,000 YFT
  const STAKE_AMOUNT_2 = ethers.parseEther("2500"); // 2,500 YFT
  const WITHDRAW_AMOUNT = ethers.parseEther("400"); // 400 YFT

  beforeEach(async function () {
    [owner, user1, user2] = await ethers.getSigners();

    // Deploy YieldForgeToken
    const YieldForgeTokenFactory = await ethers.getContractFactory("YieldForgeToken");
    token = (await YieldForgeTokenFactory.deploy(owner.address)) as YieldForgeToken;
    await token.waitForDeployment();

    // Deploy YieldForgeStaking
    const YieldForgeStakingFactory = await ethers.getContractFactory("YieldForgeStaking");
    staking = (await YieldForgeStakingFactory.deploy(
      await token.getAddress(),
      owner.address
    )) as YieldForgeStaking;
    await staking.waitForDeployment();

    // Mint test tokens to user1 and user2
    await token.connect(owner).mint(user1.address, INITIAL_MINT);
    await token.connect(owner).mint(user2.address, INITIAL_MINT);
  });

  describe("Deployment", function () {
    it("Should configure correct staking token and owner", async function () {
      expect(await staking.stakingToken()).to.equal(await token.getAddress());
      expect(await staking.owner()).to.equal(owner.address);
    });

    it("Should start with 0 totalStaked and 0 user balance", async function () {
      expect(await staking.totalStaked()).to.equal(0);
      expect(await staking.balanceOf(user1.address)).to.equal(0);
    });

    it("Should revert if deployed with zero address", async function () {
      const YieldForgeStakingFactory = await ethers.getContractFactory("YieldForgeStaking");
      await expect(
        YieldForgeStakingFactory.deploy(ethers.ZeroAddress, owner.address)
      ).to.be.revertedWithCustomError(staking, "ZeroAddressNotAllowed");
    });
  });

  describe("Staking Mechanics", function () {
    it("Should allow user to stake YFT tokens after approving allowance", async function () {
      const stakingAddress = await staking.getAddress();
      await token.connect(user1).approve(stakingAddress, STAKE_AMOUNT_1);

      await expect(staking.connect(user1).stake(STAKE_AMOUNT_1))
        .to.emit(staking, "Staked")
        .withArgs(user1.address, STAKE_AMOUNT_1);

      expect(await staking.balanceOf(user1.address)).to.equal(STAKE_AMOUNT_1);
      expect(await staking.totalStaked()).to.equal(STAKE_AMOUNT_1);

      // Contract holds YFT tokens
      expect(await token.balanceOf(stakingAddress)).to.equal(STAKE_AMOUNT_1);
      // User wallet YFT decreases
      expect(await token.balanceOf(user1.address)).to.equal(INITIAL_MINT - STAKE_AMOUNT_1);
    });

    it("Should revert if user attempts to stake 0 tokens", async function () {
      await expect(
        staking.connect(user1).stake(0)
      ).to.be.revertedWithCustomError(staking, "AmountMustBeGreaterThanZero");
    });

    it("Should revert if user has not approved enough allowance", async function () {
      await expect(
        staking.connect(user1).stake(STAKE_AMOUNT_1)
      ).to.be.revertedWithCustomError(token, "ERC20InsufficientAllowance");
    });
  });

  describe("Withdrawal Mechanics", function () {
    beforeEach(async function () {
      const stakingAddress = await staking.getAddress();
      await token.connect(user1).approve(stakingAddress, STAKE_AMOUNT_1);
      await staking.connect(user1).stake(STAKE_AMOUNT_1);
    });

    it("Should allow user to withdraw staked tokens back to wallet", async function () {
      await expect(staking.connect(user1).withdraw(WITHDRAW_AMOUNT))
        .to.emit(staking, "Withdrawn")
        .withArgs(user1.address, WITHDRAW_AMOUNT);

      const expectedStaked = STAKE_AMOUNT_1 - WITHDRAW_AMOUNT;
      expect(await staking.balanceOf(user1.address)).to.equal(expectedStaked);
      expect(await staking.totalStaked()).to.equal(expectedStaked);

      // User wallet YFT increases
      expect(await token.balanceOf(user1.address)).to.equal(INITIAL_MINT - expectedStaked);
    });

    it("Should revert if user attempts to withdraw 0 tokens", async function () {
      await expect(
        staking.connect(user1).withdraw(0)
      ).to.be.revertedWithCustomError(staking, "AmountMustBeGreaterThanZero");
    });

    it("Should revert if user attempts to withdraw more than staked balance", async function () {
      const EXCEEDED_WITHDRAW = STAKE_AMOUNT_1 + ethers.parseEther("1");
      await expect(
        staking.connect(user1).withdraw(EXCEEDED_WITHDRAW)
      ).to.be.revertedWithCustomError(staking, "InsufficientStakedBalance")
       .withArgs(EXCEEDED_WITHDRAW, STAKE_AMOUNT_1);
    });
  });

  describe("Multiple Stakers Integration", function () {
    it("Should isolate balances correctly between multiple stakers", async function () {
      const stakingAddress = await staking.getAddress();

      // User 1 stakes 1,000 YFT
      await token.connect(user1).approve(stakingAddress, STAKE_AMOUNT_1);
      await staking.connect(user1).stake(STAKE_AMOUNT_1);

      // User 2 stakes 2,500 YFT
      await token.connect(user2).approve(stakingAddress, STAKE_AMOUNT_2);
      await staking.connect(user2).stake(STAKE_AMOUNT_2);

      // Check total TVL
      const expectedTotal = STAKE_AMOUNT_1 + STAKE_AMOUNT_2;
      expect(await staking.totalStaked()).to.equal(expectedTotal);
      expect(await token.balanceOf(stakingAddress)).to.equal(expectedTotal);

      // User 1 withdraws
      await staking.connect(user1).withdraw(WITHDRAW_AMOUNT);
      expect(await staking.balanceOf(user1.address)).to.equal(STAKE_AMOUNT_1 - WITHDRAW_AMOUNT);
      // User 2 balance remains unchanged
      expect(await staking.balanceOf(user2.address)).to.equal(STAKE_AMOUNT_2);
    });
  });

  describe("Pausable Circuit Breaker", function () {
    it("Should prevent staking and withdrawing when contract is paused", async function () {
      const stakingAddress = await staking.getAddress();
      await token.connect(user1).approve(stakingAddress, STAKE_AMOUNT_1);

      await staking.connect(owner).pause();

      await expect(
        staking.connect(user1).stake(STAKE_AMOUNT_1)
      ).to.be.revertedWithCustomError(staking, "EnforcedPause");

      await staking.connect(owner).unpause();
      await staking.connect(user1).stake(STAKE_AMOUNT_1);
      expect(await staking.balanceOf(user1.address)).to.equal(STAKE_AMOUNT_1);
    });
  });
});
