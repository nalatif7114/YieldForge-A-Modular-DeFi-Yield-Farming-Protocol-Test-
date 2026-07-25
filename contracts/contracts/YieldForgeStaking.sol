// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "@openzeppelin/contracts/token/ERC20/IERC20.sol";
import "@openzeppelin/contracts/token/ERC20/utils/SafeERC20.sol";
import "@openzeppelin/contracts/utils/ReentrancyGuard.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

/**
 * @title YieldForgeStaking
 * @notice Core staking contract for YieldForge DeFi simulation protocol.
 * @dev Enables users to stake YFT ERC-20 tokens, withdraw staked balances, and tracks total pool TVL.
 *      Uses SafeERC20 for safe token transfers and ReentrancyGuard for security.
 */
contract YieldForgeStaking is ReentrancyGuard, Pausable, Ownable {
    using SafeERC20 for IERC20;

    /// @notice The ERC-20 staking token (YieldForgeToken / YFT)
    IERC20 public immutable stakingToken;

    /// @notice Total amount of YFT tokens currently staked in the contract
    uint256 public totalStaked;

    /// @notice Tracks staked YFT token balance per user address
    mapping(address account => uint256) private _stakedBalances;

    // Custom Errors for Gas Efficiency
    /// @dev Thrown when an amount parameter is 0.
    error AmountMustBeGreaterThanZero();

    /// @dev Thrown when a user attempts to withdraw more than their staked balance.
    error InsufficientStakedBalance(uint256 requested, uint256 available);

    /// @dev Thrown when zero address is passed.
    error ZeroAddressNotAllowed();

    // Events
    /// @notice Emitted when a user stakes YFT tokens.
    /// @param user The address of the staker.
    /// @param amount The amount of YFT tokens staked.
    event Staked(address indexed user, uint256 amount);

    /// @notice Emitted when a user withdraws staked YFT tokens.
    /// @param user The address of the staker withdrawing tokens.
    /// @param amount The amount of YFT tokens withdrawn.
    event Withdrawn(address indexed user, uint256 amount);

    /**
     * @notice Initializes the YieldForgeStaking contract.
     * @param _stakingToken The address of the YFT ERC-20 token contract.
     * @param initialOwner The address assigned as contract owner/admin.
     */
    constructor(IERC20 _stakingToken, address initialOwner) Ownable(initialOwner) {
        if (address(_stakingToken) == address(0) || initialOwner == address(0)) {
            revert ZeroAddressNotAllowed();
        }
        stakingToken = _stakingToken;
    }

    /**
     * @notice Returns the staked YFT token balance of a specific user.
     * @param account The address to query.
     */
    function balanceOf(address account) external view returns (uint256) {
        return _stakedBalances[account];
    }

    /**
     * @notice Stakes a specified amount of YFT tokens into the contract.
     * @dev User must approve this contract to spend `amount` of YFT tokens before calling.
     * @param amount The amount of YFT tokens to stake (18 decimals).
     */
    function stake(uint256 amount) external nonReentrant whenNotPaused {
        if (amount == 0) revert AmountMustBeGreaterThanZero();

        // Increase balances before external transfer (Checks-Effects-Interactions pattern)
        _stakedBalances[msg.sender] += amount;
        totalStaked += amount;

        // Transfer YFT tokens from staker's wallet into this contract
        stakingToken.safeTransferFrom(msg.sender, address(this), amount);

        emit Staked(msg.sender, amount);
    }

    /**
     * @notice Withdraws a specified amount of previously staked YFT tokens back to the user's wallet.
     * @param amount The amount of YFT tokens to withdraw.
     */
    function withdraw(uint256 amount) external nonReentrant whenNotPaused {
        if (amount == 0) revert AmountMustBeGreaterThanZero();
        uint256 currentBalance = _stakedBalances[msg.sender];
        if (amount > currentBalance) {
            revert InsufficientStakedBalance(amount, currentBalance);
        }

        // Decrease balances before external transfer
        _stakedBalances[msg.sender] -= amount;
        totalStaked -= amount;

        // Transfer YFT tokens from contract back to user's wallet
        stakingToken.safeTransfer(msg.sender, amount);

        emit Withdrawn(msg.sender, amount);
    }

    /**
     * @notice Emergency pause for staking contract.
     */
    function pause() external onlyOwner {
        _pause();
    }

    /**
     * @notice Unpause staking contract.
     */
    function unpause() external onlyOwner {
        _unpause();
    }
}
