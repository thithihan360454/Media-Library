<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTO\UserDTO;
use App\Exceptions\UnauthorizedException;

class BaseController
{
    /*
    |--------------------------------------------------------------------------
    | REDIRECT (SAFE)
    |--------------------------------------------------------------------------
    */
    protected function redirect(string $url): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }

        // fallback if headers already sent
        echo "<script>window.location.href='{$url}';</script>";
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | FLASH ERRORS + OLD INPUT
    |--------------------------------------------------------------------------
    */
    protected function withErrors(array $errors, array $old, string $url): void
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;

        $this->redirect($url);
    }

    /*
    |--------------------------------------------------------------------------
    | FLASH SUCCESS
    |--------------------------------------------------------------------------
    */
    protected function withSuccess(string $message, string $url): void
    {
        $_SESSION['success'] = $message;

        $this->redirect($url);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN SESSION
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
            throw new UnauthorizedException('Please login first!');
        }
    }
}
