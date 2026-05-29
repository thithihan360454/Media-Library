<?php

declare(strict_types=1);

namespace App\Controllers;

class BaseController
{
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function withErrors(array $errors, array $old, string $url): void
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;

        $this->redirect($url);
    }

    protected function withSuccess(string $message, string $url): void
    {
        $_SESSION['success'] = $message;

        $this->redirect($url);
    }

    protected function loginUser(object $user): void
    {
        $_SESSION['userid'] = $user->userid;
        $_SESSION['username'] = $user->username;
    }

    /**
     * Protect pages
     */
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['userid'])) {

            $_SESSION['auth_error'] = 'Please login first!';

            header('Location: index.php?page=login');
            exit;
        }
    }
}
