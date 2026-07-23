// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

/**
 * @title SampleCheck
 * @dev Baseline contract for Phase 1 verification
 */
contract SampleCheck {
    string public message;

    constructor(string memory _message) {
        message = _message;
    }

    function setMessage(string memory _newMessage) external {
        message = _newMessage;
    }
}
