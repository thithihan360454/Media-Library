<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTO\UserDTO;

class BaseController
{
    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR - AUTO START SESSION
    |--------------------------------------------------------------------------
    */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAFE POST DATA
    |--------------------------------------------------------------------------
    */
    protected function post(): array
    {
        return array_map(
            static fn($value) =>
            is_string($value)
                ? trim($value)
                : $value,
            $_POST
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAFE REDIRECT
    |--------------------------------------------------------------------------
    */
    protected function redirect(string $url): void
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }

        echo "<script>window.location.href='{$url}';</script>";
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | FLASH ERRORS + OLD INPUT
    |--------------------------------------------------------------------------
    */
    protected function withErrors(
        array $errors,
        array $old,
        string $url
    ): void {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;

        $this->redirect($url);
    }

    /*
    |--------------------------------------------------------------------------
    | FLASH SUCCESS MESSAGE
    |--------------------------------------------------------------------------
    */
    protected function withSuccess(
        string $message,
        string $url
    ): void {
        $_SESSION['success'] = $message;

        $this->redirect($url);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN USER SESSION
    |--------------------------------------------------------------------------
    */
    protected function loginUser(UserDTO $user): void
    {
        $_SESSION['userid'] = $user->userid;
        $_SESSION['username'] = $user->username;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH GUARD
    |--------------------------------------------------------------------------
    */
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['userid'])) {

            $_SESSION['auth_error'] =
                'Please login first!';

            $this->redirect(
                BASE_URL . '/Public/index.php?page=login'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET AUTH USER ID
    |--------------------------------------------------------------------------
    */
    protected function userId(): ?int
    {
        return $_SESSION['userid'] ?? null;
    }
}
