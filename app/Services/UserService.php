<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use App\Validation\Validator;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Response\ApiResponse;
use App\Mappers\UserMapper;

class UserService extends BaseService
{
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
    public function register(array $data): array
    {
        /*
        | Validation
        */
        if (!$this->validator->validate($data, RegisterRequest::rules())) {
            return ApiResponse::error(
                $this->validator->errors(),
                'Validation failed'
            );
        }

        /*
        | Check email exists
        */
        if ($this->userRepo->findByEmail($data['email'])) {
            return ApiResponse::error(
                ['email' => 'Email already exists'],
                'Duplicate email'
            );
        }

        /*
        | Create User Entity
        */
        $user = new User(
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        );

        /*
        | Save user
        */
        if (!$this->userRepo->create($user->toArray())) {
            return ApiResponse::error(
                ['general' => 'Registration failed'],
                'Database error'
            );
        }

        /*
        | Success response (NO USER RETURNED FOR SECURITY)
        */
        return ApiResponse::success(
            null,
            'User registered successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN USER
    |--------------------------------------------------------------------------
    */
    public function login(array $data): array
    {
        /*
        | Validation
        */
        if (!$this->validator->validate($data, LoginRequest::rules())) {
            return ApiResponse::error(
                $this->validator->errors(),
                'Validation failed'
            );
        }

        /*
        | Find user
        */
        $user = $this->userRepo->findByEmail($data['email']);

        if (!$user) {
            return ApiResponse::error(
                ['email' => 'Email not found'],
                'Authentication failed'
            );
        }

        /*
        | Verify password
        */
        if (!password_verify($data['password'], $user->getPassword())) {
            return ApiResponse::error(
                ['password' => 'Incorrect password'],
                'Authentication failed'
            );
        }

        /*
        | Convert Model → DTO
        */
        $userDTO = UserMapper::toDTO($user);

        /*
        | Success response (SAFE DTO ONLY)
        */
        return ApiResponse::success(
            $userDTO,
            'Login successful'
        );
    }
}
