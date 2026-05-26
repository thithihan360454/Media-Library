<?php

namespace App\Controllers;

/**
 * Base Controller
 * Shared helper methods for all controllers
 */
abstract class BaseController
{
    /**
     * Render a view file
     */
    protected function view(
        string $path,
        array $data = []
    ): void {

        extract($data);

        require BASE_PATH . '/view/' . $path . '.php';
    }

    /**
     * Redirect helper
     */
    protected function redirect(
        string $url
    ): void {

        header("Location: $url");
        exit;
    }

    /**
     * JSON response helper (for APIs)
     */
    protected function json(
        array $data,
        int $code = 200
    ): void {

        http_response_code($code);

        header('Content-Type: application/json');

        echo json_encode(
            $data,
            JSON_PRETTY_PRINT
        );

        exit;
    }

    /**
     * Get GET parameter safely
     */
    protected function get(
        string $key,
        $default = null
    ) {

        return $_GET[$key] ?? $default;
    }

    /**
     * Get POST parameter safely
     */
    protected function post(
        string $key,
        $default = null
    ) {

        return $_POST[$key] ?? $default;
    }

    /**
     * Check if user logged in
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Protect pages
     */
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {

            $_SESSION['auth_error'] = 'Please login first!';

            header('Location: index.php?page=login');
            exit;
        }
    }
}
