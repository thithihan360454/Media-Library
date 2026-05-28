<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use App\Validation\Validator;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

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
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        if (
            !$this->validator->validate(
                $data,
                RegisterRequest::rules()
            )
        ) {
            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK EMAIL EXISTS
        |--------------------------------------------------------------------------
        */
        if ($this->userRepo->findByEmail($data['email'])) {
            return [
                'success' => false,
                'errors' => [
                    'email' => 'Email already exists'
                ]
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE USER ENTITY (ENCAPSULATION SAFE)
        |--------------------------------------------------------------------------
        */
        $user = new User();

        $user->setUsername($data['username']);

        $user->setEmail($data['email']);

        $user->setPassword(
            password_hash($data['password'], PASSWORD_DEFAULT)
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE USER
        |--------------------------------------------------------------------------
        */
        if (!$this->userRepo->create($user)) {
            return [
                'success' => false,
                'errors' => [
                    'general' => 'Registration failed'
                ]
            ];
        }

        return [
            'success' => true
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN USER
    |--------------------------------------------------------------------------
    */
    public function login(array $data): array
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        if (
            !$this->validator->validate(
                $data,
                LoginRequest::rules()
            )
        ) {
            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */
        $user = $this->userRepo->findByEmail($data['email']);

        if (!$user) {
            return [
                'success' => false,
                'errors' => [
                    'email' => 'Email not found'
                ]
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY PASSWORD (ENCAPSULATION FIX)
        |--------------------------------------------------------------------------
        */
        if (
            !password_verify(
                $data['password'],
                $user->getPassword()
            )
        ) {
            return [
                'success' => false,
                'errors' => [
                    'password' => 'Incorrect password'
                ]
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */
        return [
            'success' => true,
            'user' => $user
        ];
    }
}
