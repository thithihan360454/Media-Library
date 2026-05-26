<?php

namespace App\Services;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use App\Services\ValidatorService;

class UserService extends BaseService
{
    private UserRepositoryInterface $userRepo;
    private ValidatorService $validator;

    public function __construct(
        UserRepositoryInterface $userRepo,
        ValidatorService $validator
    ) {
        $this->userRepo = $userRepo;
        $this->validator = $validator;
    }

    /**
     * Register User
     */
    public function register(array $data): array
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION (SRP kept inside ValidatorService)
        |--------------------------------------------------------------------------
        */
        if (
            !$this->validator->validate($data, User::rules())
        ) {
            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | BUSINESS RULE: EMAIL EXISTS
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
        | CREATE USER ENTITY
        |--------------------------------------------------------------------------
        */
        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
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
    | VALIDATE LOGIN DATA
    |--------------------------------------------------------------------------
    */
        $isValid = $this->validator->validate(
            $data,
            User::loginRules()
        );

        if (!$isValid) {

            return [
                'success' => false,
                'errors' => $this->validator->errors()
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */
        $user = $this->userRepo
            ->findByEmail($data['email']);

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
    | VERIFY PASSWORD
    |--------------------------------------------------------------------------
    */
        if (
            !password_verify(
                $data['password'],
                $user->password
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
    | LOGIN SUCCESS
    |--------------------------------------------------------------------------
    */
        return [
            'success' => true,
            'user' => $user
        ];
    }
}
