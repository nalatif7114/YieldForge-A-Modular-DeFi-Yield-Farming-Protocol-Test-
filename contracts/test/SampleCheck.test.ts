import { expect } from "chai";
import { ethers } from "hardhat";

describe("SampleCheck Contract (Phase 1 Baseline Test)", function () {
  it("Should set and update the initial message correctly", async function () {
    const SampleCheck = await ethers.getContractFactory("SampleCheck");
    const sampleCheck = await SampleCheck.deploy("YieldForge Phase 1 Ready");

    expect(await sampleCheck.message()).to.equal("YieldForge Phase 1 Ready");

    await sampleCheck.setMessage("Hardhat Configured Successfully");
    expect(await sampleCheck.message()).to.equal("Hardhat Configured Successfully");
  });
});
