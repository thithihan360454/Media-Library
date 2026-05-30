<?php

declare(strict_types=1);

namespace App\DTO;

class RegisterUserDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $confirmPassword
    ) {}
}
