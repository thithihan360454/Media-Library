<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\User;

use App\DTO\UserDTO;
use App\DTO\LoginUserDTO;
use App\DTO\RegisterUserDTO;

class UserMapper
{
    /*
    |--------------------------------------------------------------------------
    | MODEL → RESPONSE DTO
    |--------------------------------------------------------------------------
    */
    public static function toDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getUserId(),
            $user->getUsername(),
            $user->getEmail()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ARRAY → LOGIN DTO
    |--------------------------------------------------------------------------
    */
    public static function toLoginDTO(array $data): LoginUserDTO
    {
        return new LoginUserDTO(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ARRAY → REGISTER DTO
    |--------------------------------------------------------------------------
    */
    public static function toRegisterDTO(array $data): RegisterUserDTO
    {
        return new RegisterUserDTO(
            $data['username'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['confirm_password'] ?? ''
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ARRAY → USER MODEL
    |--------------------------------------------------------------------------
    */
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
