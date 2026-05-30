<?php

namespace App\Core;

use Throwable;
use App\Exceptions\AppException;

class ExceptionHandler
{
    public static function handle(\Throwable $e): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Store debug info (for your error.php)
        $_SESSION['debug_error'] = [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        // If it's a known exception → handle normally
        if ($e instanceof \App\Exceptions\ValidationException) {
            $_SESSION['errors'] = $e->getErrors();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if ($e instanceof \App\Exceptions\AuthenticationException) {
            $_SESSION['errors'] = ['auth' => $e->getMessage()];
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if ($e instanceof \App\Exceptions\UnauthorizedException) {
            $_SESSION['errors'] = ['auth' => $e->getMessage()];
            header('Location: ' . BASE_URL . '/Public/index.php?page=login');
            exit;
        }

        // ❌ UNKNOWN ERROR → SHOW ERROR PAGE
        http_response_code(500);
        require BASE_PATH . '/view/errors/error.php';
        exit;
    }
}
