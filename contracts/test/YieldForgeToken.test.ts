import { expect } from "chai";
import { ethers } from "hardhat";
import { YieldForgeToken } from "../typechain-types";
import { SignerWithAddress } from "@nomicfoundation/hardhat-ethers/signers";

describe("YieldForgeToken (YFT) - Refined Unit Tests", function () {
  let token: YieldForgeToken;
  let owner: SignerWithAddress;
  let user1: SignerWithAddress;
  let user2: SignerWithAddress;

  const MINT_AMOUNT = ethers.parseEther("1000"); // 1,000 YFT
  const TRANSFER_AMOUNT = ethers.parseEther("250"); // 250 YFT
  const BURN_AMOUNT = ethers.parseEther("100"); // 100 YFT

  beforeEach(async function () {
    [owner, user1, user2] = await ethers.getSigners();

    const YieldForgeTokenFactory = await ethers.getContractFactory("YieldForgeToken");
    token = (await YieldForgeTokenFactory.deploy(owner.address)) as YieldForgeToken;
    await token.waitForDeployment();
  });

  describe("Deployment", function () {
    it("Should deploy with correct Name, Symbol, and Decimals", async function () {
      expect(await token.name()).to.equal("YieldForge Token");
      expect(await token.symbol()).to.equal("YFT");
      expect(await token.decimals()).to.equal(18);
    });

    it("Should start with an initial total supply of 0", async function () {
      expect(await token.totalSupply()).to.equal(0);
    });

    it("Should set the correct owner address", async function () {
      expect(await token.owner()).to.equal(owner.address);
    });

    it("Should revert deployment if initialOwner is zero address", async function () {
      const YieldForgeTokenFactory = await ethers.getContractFactory("YieldForgeToken");
      await expect(
        YieldForgeTokenFactory.deploy(ethers.ZeroAddress)
      ).to.be.revertedWithCustomError(token, "OwnableInvalidOwner")
       .withArgs(ethers.ZeroAddress);
    });
  });

  describe("Minting Functionality & Custom Errors", function () {
    it("Should allow the owner to mint new YFT tokens", async function () {
      await expect(token.connect(owner).mint(user1.address, MINT_AMOUNT))
        .to.emit(token, "TokensMinted")
        .withArgs(user1.address, MINT_AMOUNT)
        .and.to.emit(token, "Transfer")
        .withArgs(ethers.ZeroAddress, user1.address, MINT_AMOUNT);

      expect(await token.balanceOf(user1.address)).to.equal(MINT_AMOUNT);
      expect(await token.totalSupply()).to.equal(MINT_AMOUNT);
    });

    it("Should revert if a non-owner attempts to mint tokens", async function () {
      await expect(
        token.connect(user1).mint(user2.address, MINT_AMOUNT)
      ).to.be.revertedWithCustomError(token, "OwnableUnauthorizedAccount")
       .withArgs(user1.address);

      expect(await token.balanceOf(user2.address)).to.equal(0);
    });

    it("Should revert with custom error ZeroAddressNotAllowed when minting to zero address", async function () {
      await expect(
        token.connect(owner).mint(ethers.ZeroAddress, MINT_AMOUNT)
      ).to.be.revertedWithCustomError(token, "ZeroAddressNotAllowed");
    });

    it("Should revert with custom error AmountMustBeGreaterThanZero when minting zero amount", async function () {
      await expect(
        token.connect(owner).mint(user1.address, 0)
      ).to.be.revertedWithCustomError(token, "AmountMustBeGreaterThanZero");
    });
  });

  describe("Transfers & Balance Checks", function () {
    beforeEach(async function () {
      await token.connect(owner).mint(user1.address, MINT_AMOUNT);
    });

    it("Should transfer YFT tokens between accounts", async function () {
      await expect(token.connect(user1).transfer(user2.address, TRANSFER_AMOUNT))
        .to.emit(token, "Transfer")
        .withArgs(user1.address, user2.address, TRANSFER_AMOUNT);

      expect(await token.balanceOf(user1.address)).to.equal(MINT_AMOUNT - TRANSFER_AMOUNT);
      expect(await token.balanceOf(user2.address)).to.equal(TRANSFER_AMOUNT);
    });

    it("Should fail if sender does not have enough balance", async function () {
      const EXCEEDED_AMOUNT = ethers.parseEther("2000");
      await expect(
        token.connect(user1).transfer(user2.address, EXCEEDED_AMOUNT)
      ).to.be.revertedWithCustomError(token, "ERC20InsufficientBalance");
    });
  });

  describe("Burning Functionality", function () {
    beforeEach(async function () {
      await token.connect(owner).mint(user1.address, MINT_AMOUNT);
    });

    it("Should allow token holder to burn their own tokens", async function () {
      await expect(token.connect(user1).burn(BURN_AMOUNT))
        .to.emit(token, "TokensBurned")
        .withArgs(user1.address, BURN_AMOUNT)
        .and.to.emit(token, "Transfer")
        .withArgs(user1.address, ethers.ZeroAddress, BURN_AMOUNT);

      expect(await token.balanceOf(user1.address)).to.equal(MINT_AMOUNT - BURN_AMOUNT);
      expect(await token.totalSupply()).to.equal(MINT_AMOUNT - BURN_AMOUNT);
    });

    it("Should allow burning tokens using allowance via burnFrom", async function () {
      await token.connect(user1).approve(user2.address, BURN_AMOUNT);
      
      await expect(token.connect(user2).burnFrom(user1.address, BURN_AMOUNT))
        .to.emit(token, "TokensBurned")
        .withArgs(user1.address, BURN_AMOUNT);

      expect(await token.balanceOf(user1.address)).to.equal(MINT_AMOUNT - BURN_AMOUNT);
    });
  });

  describe("Pausable Emergency Controls", function () {
    beforeEach(async function () {
      await token.connect(owner).mint(user1.address, MINT_AMOUNT);
    });

    it("Should allow owner to pause and unpause the contract", async function () {
      await token.connect(owner).pause();
      expect(await token.paused()).to.equal(true);

      await token.connect(owner).unpause();
      expect(await token.paused()).to.equal(false);
    });

    it("Should prevent transfers, minting, and burning when paused", async function () {
      await token.connect(owner).pause();

      // Transfers should revert
      await expect(
        token.connect(user1).transfer(user2.address, TRANSFER_AMOUNT)
      ).to.be.revertedWithCustomError(token, "EnforcedPause");

      // Minting should revert
      await expect(
        token.connect(owner).mint(user2.address, MINT_AMOUNT)
      ).to.be.revertedWithCustomError(token, "EnforcedPause");

      // Burning should revert
      await expect(
        token.connect(user1).burn(BURN_AMOUNT)
      ).to.be.revertedWithCustomError(token, "EnforcedPause");
    });

    it("Should allow transfers again after unpausing", async function () {
      await token.connect(owner).pause();
      await token.connect(owner).unpause();

      await token.connect(user1).transfer(user2.address, TRANSFER_AMOUNT);
      expect(await token.balanceOf(user2.address)).to.equal(TRANSFER_AMOUNT);
    });

    it("Should revert if non-owner attempts to pause or unpause", async function () {
      await expect(
        token.connect(user1).pause()
      ).to.be.revertedWithCustomError(token, "OwnableUnauthorizedAccount");
    });
  });
});
