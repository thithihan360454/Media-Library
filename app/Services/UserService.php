<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use App\Validation\Validator;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

use App\DTO\UserDTO;
use App\Mappers\UserMapper;

use App\Exceptions\ValidationException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\DatabaseException;

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
    public function register(array $data): bool
    {
        // Validation
        if (!$this->validator->validate($data, RegisterRequest::rules())) {
            throw new ValidationException(
                $this->validator->errors()
            );
        }

        // Duplicate email check
        if ($this->userRepo->findByEmail($data['email'])) {
            throw new ValidationException([
                'email' => 'Email already exists'
            ]);
        }

        // Create user
        $user = new User(
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        );

        // Save user
        $created = $this->userRepo->create($user->toArray());

        if (!$created) {
            throw new DatabaseException('Failed to create user');
        }

        // SUCCESS
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN USER
    |--------------------------------------------------------------------------
    */
    public function login(array $data): UserDTO
    {
        // Validation
        if (!$this->validator->validate($data, LoginRequest::rules())) {
            throw new ValidationException(
                $this->validator->errors()
            );
        }

        // Find user
        $user = $this->userRepo->findByEmail($data['email']);

        if (!$user) {
            throw new AuthenticationException(
                'Invalid email or password'
            );
        }

        // Verify password
        if (!password_verify($data['password'], $user->getPassword())) {
            throw new AuthenticationException(
                'Invalid email or password'
            );
        }

        // Return DTO
        return UserMapper::toDTO($user);
    }
}
