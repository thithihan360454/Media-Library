<?php

namespace App\Controllers\Api;

use App\Services\UserService;

class AuthApiController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * REGISTER API
     */
    public function register(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        $username = trim($data['username'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (!$username || !$email || !$password) {
            echo json_encode([
                "status" => "error",
                "message" => "All fields are required"
            ]);
            return;
        }

        $success = $this->userService->register(
            $username,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        );

        if ($success) {
            echo json_encode([
                "status" => "success",
                "message" => "User registered successfully"
            ]);
            return;
        }

        echo json_encode([
            "status" => "error",
            "message" => "Email already exists"
        ]);
    }

    /**
     * LOGIN API
     */
    public function login(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (!$email || !$password) {
            echo json_encode([
                "status" => "error",
                "message" => "Email and password required"
            ]);
            return;
        }

        $user = $this->userService->login($email, $password);

        if ($user) {
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "data" => [
                    "user_id" => $user->user_id,
                    "username" => $user->username,
                    "email" => $user->email
                ]
            ]);
            return;
        }

        echo json_encode([
            "status" => "error",
            "message" => "Invalid credentials"
        ]);
    }

    public function logout(): void
    {
        header('Content-Type: application/json');

        session_start();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        echo json_encode([
            "status" => "success",
            "message" => "Logged out successfully"
        ]);
    }
}
