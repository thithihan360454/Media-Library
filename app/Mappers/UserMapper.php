<?php

namespace App\Mappers;

use App\Models\User;
use App\DTO\UserDTO;

class UserMapper
{
    public static function toDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getUserId(),
            $user->getUsername(),
            $user->getEmail()
        );
    }

    public static function fromArray(array $data): User
    {
        return new User(
            $data['username'],
            $data['email'],
            $data['password'],
            (int) $data['userid']
        );
    }
}
