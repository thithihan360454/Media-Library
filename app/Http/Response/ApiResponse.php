<?php

declare(strict_types=1);

namespace App\Http\Response;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): void {
        http_response_code($code);

        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    public static function error(
        string $message = 'Error',
        array $errors = [],
        int $code = 400
    ): void {
        http_response_code($code);

        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}
