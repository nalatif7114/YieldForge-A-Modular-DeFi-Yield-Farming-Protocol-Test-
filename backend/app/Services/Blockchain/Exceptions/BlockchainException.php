<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Exceptions;

use Exception;
use Throwable;

class BlockchainException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = 'Blockchain communication error', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
