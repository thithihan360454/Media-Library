<?php

declare(strict_types=1);

namespace App\Exceptions;

class DatabaseException extends AppException
{
    protected int $statusCode = 500;

    public function __construct(
        string $message = 'Database error'
    ) {
        parent::__construct($message);
    }
}
