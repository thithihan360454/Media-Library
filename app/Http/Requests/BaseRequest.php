<?php

namespace App\Http\Requests;

use App\Validation\Validator;
use App\Interfaces\RequestInterface;

abstract class BaseRequest

{
    /*
    |--------------------------------------------------------------------------
    | REQUEST DATA
    |--------------------------------------------------------------------------
    */
    protected array $data;

    /*
    |--------------------------------------------------------------------------
    | VALIDATION ERRORS
    |--------------------------------------------------------------------------
    */
    protected array $errors = [];

    /*
    |--------------------------------------------------------------------------
    | VALIDATOR
    |--------------------------------------------------------------------------
    */
    protected Validator $validator;

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */
    public function __construct(
        array $data,
        Validator $validator
    ) {
        $this->data = $data;

        $this->validator = $validator;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE REQUEST
    |--------------------------------------------------------------------------
    */
    public function validate(): bool
    {
        $valid = $this->validator->validate(
            $this->data,
            static::rules()
        );

        $this->errors = $this->validator->errors();

        return $valid;
    }

    /*
    |--------------------------------------------------------------------------
    | GET ERRORS
    |--------------------------------------------------------------------------
    */
    public function errors(): array
    {
        return $this->errors;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATED DATA
    |--------------------------------------------------------------------------
    */
    public function validated(): array
    {
        return $this->data;
    }

    /*
    |--------------------------------------------------------------------------
    | RULES
    |--------------------------------------------------------------------------
    */
    abstract public static function rules(): array;
}
