<?php

namespace App\Models;

class User
{
    public int $user_id;

    public string $username;

    public string $email;

    public string $password;

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [

            'username' => [
                'required',
                'min:3',
                'max:50'
            ],

            'email' => [
                'required',
                'email'
            ],

            'password' => [
                'required',
                'min:6'
            ]

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN VALIDATION RULES
    |--------------------------------------------------------------------------
    */
    public static function loginRules(): array
    {
        return [

            'email' => [
                'required',
                'email'
            ],

            'password' => [
                'required',
                'min:6'
            ]
        ];
    }
}
