<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function showRegister(): void
    {
        require BASE_PATH . '/view/auth/register.php';
    }

    public function register(): void
    {
        $data = [
            'username'         => trim($_POST['username'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'password'         => trim($_POST['password'] ?? ''),
            'confirm_password' => trim($_POST['confirm_password'] ?? ''),
        ];

        $result = $this->userService->register($data);

        if (!$result['success']) {
            $this->withErrors(
                $result['errors'],
                $data,
                BASE_URL . '/Public/index.php?page=register'
            );
        }

        $this->withSuccess(
            'Registration successful',
            BASE_URL . '/Public/index.php?page=login'
        );
    }

    public function showLogin(): void
    {
        require BASE_PATH . '/view/auth/login.php';
    }

    public function login(): void
    {
        $data = [
            'email'    => trim($_POST['email'] ?? ''),
            'password' => trim($_POST['password'] ?? '')
        ];

        $result = $this->userService->login($data);

        if (!$result['success']) {
            $this->withErrors(
                $result['errors'],
                $data,
                BASE_URL . '/Public/index.php?page=login'
            );
        }

        $this->loginUser($result['data']);

        $this->redirect(BASE_URL . '/Public/index.php?page=home');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect(BASE_URL . '/Public/index.php?page=login');
    }
}
