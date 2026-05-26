<?php

namespace App\Services;

class ValidatorService
{
    private array $errors = [];

    /**
     * Validate data
     */
    public function validate(
        array $data,
        array $rules
    ): bool {

        $this->errors = []; // IMPORTANT FIX

        foreach ($rules as $field => $fieldRules) {

            $value = trim($data[$field] ?? '');

            foreach ($fieldRules as $rule) {

                /*
                |--------------------------------------------------------------------------
                | REQUIRED
                |--------------------------------------------------------------------------
                */
                if ($rule === 'required') {

                    if ($value === '') {

                        $this->errors[$field]
                            = ucfirst($field)
                            . ' is required';

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */
                if ($rule === 'email') {

                    if (
                        !filter_var(
                            $value,
                            FILTER_VALIDATE_EMAIL
                        )
                    ) {

                        $this->errors[$field]
                            = 'Invalid email format';

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MIN LENGTH
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($rule, 'min:')) {

                    $min = (int) explode(':', $rule)[1];

                    if (strlen($value) < $min) {

                        $this->errors[$field]
                            = ucfirst($field)
                            . " must be at least {$min} characters";

                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MAX LENGTH
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($rule, 'max:')) {

                    $max = (int) explode(':', $rule)[1];

                    if (strlen($value) > $max) {

                        $this->errors[$field]
                            = ucfirst($field)
                            . " must not exceed {$max} characters";

                        break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get errors
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
