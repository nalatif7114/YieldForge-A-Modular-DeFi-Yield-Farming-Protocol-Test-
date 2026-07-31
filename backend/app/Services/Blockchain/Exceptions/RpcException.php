<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Exceptions;

use Throwable;

class RpcException extends BlockchainException
{
    public function __construct(string $message = 'RPC communication error', int $code = 502, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
