<?php

namespace App\Models;

class User
{
    /*
    |--------------------------------------------------------------------------
    | PROPERTIES
    |--------------------------------------------------------------------------
    */
    private ?int $userid = null;

    private string $username;

    private string $email;

    private string $password;

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */
    public function __construct(
        string $username,
        string $email,
        string $password,
        ?int $userid = null
    ) {
        $this->userid = $userid;

        $this->username = trim($username);

        $this->email = trim($email);

        $this->password = $password;
    }

    /*
    |--------------------------------------------------------------------------
    | GETTERS
    |--------------------------------------------------------------------------
    */

    public function getUserId(): ?int
    {
        return $this->userid;
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

    /*
    |--------------------------------------------------------------------------
    | SETTERS
    |--------------------------------------------------------------------------
    */

    public function setUserId(int $id): void
    {
        $this->userid = $id;
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

    /*
    |--------------------------------------------------------------------------
    | CONVERT OBJECT TO ARRAY
    |--------------------------------------------------------------------------
    */
    public function toArray(): array
    {
        return [
            'username' => $this->getUsername(),

            'email' => $this->getEmail(),

            'password' => $this->getPassword()
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SAFE RESPONSE ARRAY
    |--------------------------------------------------------------------------
    */
    public function toResponseArray(): array
    {
        return [
            'userid' => $this->getUserId(),

            'username' => $this->getUsername(),

            'email' => $this->getEmail()
        ];
    }
}
