<?php

namespace App\Http\Requests;

class LoginRequest extends BaseRequest
{
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'password_strength']
        ];
    }
}
