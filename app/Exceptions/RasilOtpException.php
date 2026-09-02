<?php

namespace App\Exceptions;

use RuntimeException;

class RasilOtpException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly ?string $errorCode = null,
    ) {
        parent::__construct($message, $status);
    }
}
