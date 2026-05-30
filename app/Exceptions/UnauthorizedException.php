<?php

declare(strict_types=1);

namespace App\Exceptions;

class UnauthorizedException extends AppException
{
    protected int $statusCode = 403;

    public function __construct(
        string $message = 'Unauthorized access'
    ) {
        parent::__construct($message);
    }
}
