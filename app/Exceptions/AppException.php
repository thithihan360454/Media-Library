<?php

namespace App\Exceptions;

use Exception;

abstract class AppException extends Exception
{
    protected int $statusCode = 500;
    protected array $errors = [];

    public function __construct(string $message, array $errors = [], int $code = 0)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
