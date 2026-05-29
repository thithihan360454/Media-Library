<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseRequest
{
    public static function rules(): array
    {
        return [
            'username' => ['required', 'min:3', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'password_strength'],
            'confirm_password' => ['required', 'same:password']
        ];
    }
}
