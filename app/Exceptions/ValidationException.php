<?php

namespace App\Exceptions;

class ValidationException extends AppException
{
    protected int $statusCode = 422;

    public function __construct(array $errors = [])
    {
        parent::__construct("Validation failed", $errors);
    }
}
