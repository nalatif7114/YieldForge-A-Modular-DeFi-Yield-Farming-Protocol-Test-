// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/token/ERC20/extensions/ERC20Burnable.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";

/**
 * @title YieldForgeToken (YFT)
 * @notice YieldForge Token is an ERC-20 utility token for the YieldForge DeFi platform on Ethereum Sepolia Testnet.
 * @dev Inherits OpenZeppelin's ERC20, ERC20Burnable, Ownable, and Pausable contracts.
 * 
 * Key Features:
 * - Initial total supply is 0.
 * - Only the contract owner can mint new YFT tokens.
 * - Token holders can burn their own tokens or burn tokens from approved allowances.
 * - Contract owner can pause and unpause token operations in emergency scenarios.
 * - Uses gas-efficient Solidity custom errors instead of error strings.
 */
contract YieldForgeToken is ERC20, ERC20Burnable, Ownable, Pausable {

    // Custom errors for gas efficiency
    /// @dev Thrown when trying to interact with address(0).
    error ZeroAddressNotAllowed();

    /// @dev Thrown when trying to mint or transfer 0 tokens.
    error AmountMustBeGreaterThanZero();

    /// @notice Emitted when new tokens are minted by the contract owner.
    /// @param to The recipient address receiving the minted tokens.
    /// @param amount The amount of YFT tokens minted (in 18 decimals).
    event TokensMinted(address indexed to, uint256 amount);

    /// @notice Emitted when tokens are burned by a token holder.
    /// @param from The address whose tokens were burned.
    /// @param amount The amount of YFT tokens burned.
    event TokensBurned(address indexed from, uint256 amount);

    /**
     * @notice Initializes the YieldForge Token contract.
     * @dev Sets token name to "YieldForge Token", symbol to "YFT", and assigns initialOwner as contract owner.
     *      Initial total supply is 0.
     * @param initialOwner The address to be set as the contract owner.
     */
    constructor(address initialOwner) 
        ERC20("YieldForge Token", "YFT") 
        Ownable(initialOwner) 
    {
        if (initialOwner == address(0)) revert ZeroAddressNotAllowed();
    }

    /**
     * @notice Mints new YFT tokens and assigns them to the specified address.
     * @dev Can only be called by the contract owner (`onlyOwner` modifier) when contract is not paused.
     * @param to The destination address to receive the minted tokens.
     * @param amount The amount of YFT tokens to mint (in 18 decimals).
     */
    function mint(address to, uint256 amount) external onlyOwner {
        if (to == address(0)) revert ZeroAddressNotAllowed();
        if (amount == 0) revert AmountMustBeGreaterThanZero();

        _mint(to, amount);
        emit TokensMinted(to, amount);
    }

    /**
     * @notice Burns a specific amount of YFT tokens from the caller's balance.
     * @param amount The amount of YFT tokens to burn.
     */
    function burn(uint256 amount) public override {
        if (amount == 0) revert AmountMustBeGreaterThanZero();
        super.burn(amount);
        emit TokensBurned(_msgSender(), amount);
    }

    /**
     * @notice Burns a specific amount of YFT tokens from an account using an allowance.
     * @param account The address whose tokens will be burned.
     * @param amount The amount of YFT tokens to burn.
     */
    function burnFrom(address account, uint256 amount) public override {
        if (account == address(0)) revert ZeroAddressNotAllowed();
        if (amount == 0) revert AmountMustBeGreaterThanZero();
        super.burnFrom(account, amount);
        emit TokensBurned(account, amount);
    }

    /**
     * @notice Pauses all token transfers, minting, and burning.
     * @dev Can only be called by the contract owner.
     */
    function pause() external onlyOwner {
        _pause();
    }

    /**
     * @notice Unpauses token transfers, minting, and burning.
     * @dev Can only be called by the contract owner.
     */
    function unpause() external onlyOwner {
        _unpause();
    }

    /**
     * @dev Hook that is called before any transfer of tokens, including minting and burning.
     *      Enforces the `whenNotPaused` modifier for all token state transitions.
     */
    function _update(address from, address to, uint256 value) 
        internal 
        virtual 
        override(ERC20) 
        whenNotPaused 
    {
        super._update(from, to, value);
    }
}
