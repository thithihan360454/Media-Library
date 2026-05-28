<?php

namespace App\Models;

class User
{
    private int $user_id;

    private string $username;

    private string $email;

    private string $password;

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    | Ensure object is always in valid state
    */
    public function __construct(
        string $username,
        string $email,
        string $password,
        ?int $user_id = null
    ) {
        if ($user_id !== null) {
            $this->user_id = $user_id;
        }

        $this->username = trim($username);
        $this->email = trim($email);
        $this->password = $password;
    }

    // ===== GETTERS =====

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    // ===== SETTERS =====

    public function setUserId(int $id): void
    {
        $this->user_id = $id;
    }

    public function setUsername(string $username): void
    {
        $this->username = trim($username);
    }

    public function setEmail(string $email): void
    {
        $this->email = trim($email);
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    // ===== RESPONSE FORMAT =====

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail()
        ];
    }
}
