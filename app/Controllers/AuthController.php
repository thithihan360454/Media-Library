<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\ExceptionHandler;
use App\DTO\UserDTO;
use App\Services\UserService;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW REGISTER PAGE
    |--------------------------------------------------------------------------
    */
    public function showRegister(): void
    {
        require BASE_PATH . '/view/auth/register.php';
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */
    public function register(): void
    {
        try {

            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'confirm_password' => trim($_POST['confirm_password'] ?? ''),
            ];

            // register user
            $this->userService->register($data);

            // SUCCESS FLOW (IMPORTANT FIX)
            $this->withSuccess(
                'Registration successful',
                BASE_URL . '/Public/index.php?page=login'
            );
        } catch (\Throwable $e) {
            ExceptionHandler::handle($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW LOGIN PAGE
    |--------------------------------------------------------------------------
    */
    public function showLogin(): void
    {
        require BASE_PATH . '/view/auth/login.php';
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(): void
    {
        $data = [
            'email'    => trim($_POST['email'] ?? ''),
            'password' => trim($_POST['password'] ?? '')
        ];

        /** @var UserDTO $user */
        $user = $this->userService->login($data);
        // var_dump($user);
        // die;
        // store session (using your existing naming style)
        $_SESSION['userid']   = $user->userid;
        $_SESSION['username'] = $user->username;

        $this->redirect(BASE_URL . '/Public/index.php?page=home');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout(): void
    {
        session_destroy();

        $this->redirect(
            BASE_URL . '/Public/index.php?page=login'
        );
    }
}
