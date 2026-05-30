<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTO\UserDTO;
use App\Mappers\UserMapper;
use App\Services\UserService;

use App\Exceptions\ValidationException;
use App\Exceptions\AuthenticationException;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
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
    | REGISTER USER
    |--------------------------------------------------------------------------
    */
    public function register(): void
    {
        $data = $this->post();

        try {

            $dto = UserMapper::toRegisterDTO($data);

            $this->userService->register($dto);

            $this->withSuccess(
                'Registration successful',
                BASE_URL . '/Public/index.php?page=login'
            );
        } catch (ValidationException $e) {

            $this->withErrors(
                $e->getErrors(),
                [
                    'username' => $data['username'] ?? '',
                    'email'    => $data['email'] ?? '',
                ],
                BASE_URL . '/Public/index.php?page=register'
            );
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
    | LOGIN USER
    |--------------------------------------------------------------------------
    */
    public function login(): void
    {
        $data = $this->post();

        try {

            $dto = UserMapper::toLoginDTO($data);

            /** @var UserDTO $user */
            $user = $this->userService->login($dto);

            $this->loginUser($user);

            session_regenerate_id(true);

            $this->redirect(
                BASE_URL . '/Public/index.php?page=home'
            );
        } catch (ValidationException $e) {

            $this->withErrors(
                $e->getErrors(),
                [
                    'email' => $data['email'] ?? '',
                ],
                BASE_URL . '/Public/index.php?page=login'
            );
        } catch (AuthenticationException $e) {

            $this->withErrors(
                ['auth' => $e->getMessage()],
                [
                    'email' => $data['email'] ?? '',
                ],
                BASE_URL . '/Public/index.php?page=login'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT USER
    |--------------------------------------------------------------------------
    */
    public function logout(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->redirect(
            BASE_URL . '/Public/index.php?page=login'
        );
    }
}
