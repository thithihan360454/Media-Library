<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\UserService;

class AuthApiController
{
    private UserService $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER API
    |--------------------------------------------------------------------------
    */
    public function register(): void
    {
        $this->jsonHeader();

        $data = $this->getJsonInput();

        $result = $this->userService->register([
            'username' => trim($data['username'] ?? ''),
            'email'    => trim($data['email'] ?? ''),
            'password' => trim($data['password'] ?? '')
        ]);

        echo json_encode($result);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN API
    |--------------------------------------------------------------------------
    */
    public function login(): void
    {
        $this->jsonHeader();

        $data = $this->getJsonInput();

        $result = $this->userService->login([
            'email'    => trim($data['email'] ?? ''),
            'password' => trim($data['password'] ?? '')
        ]);

        echo json_encode($result);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT API
    |--------------------------------------------------------------------------
    */
    public function logout(): void
    {
        $this->jsonHeader();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | JSON HEADER
    |--------------------------------------------------------------------------
    */
    private function jsonHeader(): void
    {
        header('Content-Type: application/json');
    }

    /*
    |--------------------------------------------------------------------------
    | GET JSON INPUT
    |--------------------------------------------------------------------------
    */
    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');

        return json_decode($input, true) ?? [];
    }
}
