<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

use App\DTO\UserDTO;
use App\DTO\LoginUserDTO;
use App\DTO\RegisterUserDTO;

use App\Mappers\UserMapper;

use App\Validation\Validator;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use App\Interfaces\UserRepositoryInterface;

use App\Exceptions\ValidationException;
use App\Exceptions\DatabaseException;
use App\Exceptions\AuthenticationException;

class UserService extends BaseService
{
    /*
    |--------------------------------------------------------------------------
    | DEPENDENCIES
    |--------------------------------------------------------------------------
    */
    private UserRepositoryInterface $userRepo;
    private Validator $validator;

    public function __construct(
        UserRepositoryInterface $userRepo,
        Validator $validator
    ) {
        $this->userRepo = $userRepo;
        $this->validator = $validator;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER USER
    |--------------------------------------------------------------------------
    */
    public function register(RegisterUserDTO $dto): bool
    {
        // convert DTO → array for validation
        $data = [
            'username'         => $dto->username,
            'email'            => $dto->email,
            'password'         => $dto->password,
            'confirm_password' => $dto->confirmPassword,
        ];

        /*
        |--------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------
        */
        if (!$this->validator->validate($data, RegisterRequest::rules())) {
            throw new ValidationException($this->validator->errors());
        }

        /*
        |--------------------------------------------------------------
        | DUPLICATE EMAIL CHECK
        |--------------------------------------------------------------
        */
        if ($this->userRepo->findByEmail($dto->email)) {
            throw new ValidationException([
                'email' => 'Email already exists'
            ]);
        }

        /*
        |--------------------------------------------------------------
        | CREATE USER ENTITY
        |--------------------------------------------------------------
        */
        $user = new User(
            $dto->username,
            $dto->email,
            password_hash($dto->password, PASSWORD_DEFAULT)
        );

        /*
        |--------------------------------------------------------------
        | SAVE TO DATABASE
        |--------------------------------------------------------------
        */
        $created = $this->userRepo->create($user->toArray());

        if (!$created) {
            throw new DatabaseException('Failed to create user');
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN USER
    |--------------------------------------------------------------------------
    */
    public function login(LoginUserDTO $dto): UserDTO
    {
        // convert DTO → array for validation
        $data = [
            'email'    => $dto->email,
            'password' => $dto->password,
        ];

        /*
        |--------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------
        */
        if (!$this->validator->validate($data, LoginRequest::rules())) {
            throw new ValidationException($this->validator->errors());
        }

        /*
        |--------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------
        */
        $user = $this->userRepo->findByEmail($dto->email);

        if (!$user) {
            throw new AuthenticationException('Invalid email or password');
        }

        /*
        |--------------------------------------------------------------
        | PASSWORD CHECK
        |--------------------------------------------------------------
        */
        if (!password_verify($dto->password, $user->getPassword())) {
            throw new AuthenticationException('Invalid email or password');
        }

        /*
        |--------------------------------------------------------------
        | RETURN SAFE DTO
        |--------------------------------------------------------------
        */
        return UserMapper::toDTO($user);
    }
}
