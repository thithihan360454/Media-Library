<?php

namespace App\Http\Response;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null
        ];
    }

    public static function error(array $errors = null, string $message = 'Error'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ];
    }
}
