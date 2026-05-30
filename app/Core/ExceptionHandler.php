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

        $statusCode = 500;
        $title = "Internal Server Error";

        // Default debug info
        $_SESSION['debug_error'] = [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        // Validation (400)
        if ($e instanceof \App\Exceptions\ValidationException) {
            $statusCode = 400;
            $title = "Bad Request";

            $_SESSION['errors'] = $e->getErrors();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Auth failure (401)
        if ($e instanceof \App\Exceptions\AuthenticationException) {
            $statusCode = 401;
            $title = "Unauthorized";

            $_SESSION['errors'] = ['auth' => $e->getMessage()];
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Unauthorized access (403)
        if ($e instanceof \App\Exceptions\UnauthorizedException) {
            $statusCode = 403;
            $title = "Forbidden";

            $_SESSION['errors'] = ['auth' => $e->getMessage()];
            header('Location: ' . BASE_URL . '/Public/index.php?page=login');
            exit;
        }

        if ($e instanceof \App\Exceptions\NotFoundException) {

            $statusCode = 404;
            $title = "Page Not Found";

            http_response_code(404);

            $_SESSION['error_status'] = [
                'code' => 404,
                'title' => 'Page Not Found'
            ];

            require BASE_PATH . '/view/errors/error.php';
            exit;
        }

        // Set HTTP response code
        http_response_code($statusCode);

        // Pass data to view
        $_SESSION['error_status'] = [
            'code' => $statusCode,
            'title' => $title
        ];

        require BASE_PATH . '/view/errors/error.php';
        exit;
    }
}
