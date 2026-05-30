<?php

declare(strict_types=1);

namespace App\Core;

use ErrorException;

class ErrorHandler
{
    public static function handle(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | CONVERT PHP ERRORS TO EXCEPTIONS
        |--------------------------------------------------------------------------
        */
        throw new ErrorException(
            $message,
            0,
            $severity,
            $file,
            $line
        );
    }
}
