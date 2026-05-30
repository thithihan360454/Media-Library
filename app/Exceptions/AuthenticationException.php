<?php

declare(strict_types=1);

namespace App\Exceptions;

class AuthenticationException extends AppException
{
    protected int $statusCode = 401;

    public function __construct(
        string $message = 'Invalid credentials'
    ) {
        parent::__construct($message);
    }
}
