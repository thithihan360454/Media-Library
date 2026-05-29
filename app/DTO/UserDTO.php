<?php

namespace App\DTO;

class UserDTO
{
    public function __construct(
        public int $userid,
        public string $username,
        public string $email
    ) {}
}
