<?php

namespace App\Validation;

class Validator
{
    private array $errors = [];

    /**
     * Validate data
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {

            $value = trim($data[$field] ?? '');

            // if (is_string($value)) {
            //     $value = trim($value);
            // }

            foreach ($fieldRules as $rule) {

                /*
                |--------------------------------------------------------------------------
                | REQUIRED
                |--------------------------------------------------------------------------
                */
                if ($rule === 'required') {

                    if ($value === '') {
                        $this->errors[$field] =
                            ucfirst($field) . ' is required';
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */
                if ($rule === 'email' && $value !== '') {

                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field] = 'Invalid email format';
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
                        $this->errors[$field] =
                            ucfirst($field) . " must be at least {$min} characters";
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
                        $this->errors[$field] =
                            ucfirst($field) . " must not exceed {$max} characters";
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | PASSWORD STRENGTH
                |--------------------------------------------------------------------------
                */
                if ($rule === 'password_strength' && $value !== '') {

                    $hasUppercase = preg_match('/[A-Z]/', $value);
                    $hasLowercase = preg_match('/[a-z]/', $value);
                    $hasNumber    = preg_match('/[0-9]/', $value);
                    $hasSpecial   = preg_match('/[\W_]/', $value);

                    if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial) {
                        $this->errors[$field] =
                            'Password must contain uppercase, lowercase, number and special character';
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SAME FIELD VALIDATION (CONFIRM PASSWORD)
                |--------------------------------------------------------------------------
                | usage: same:password
                |--------------------------------------------------------------------------
                */
                if (str_starts_with($rule, 'same:')) {

                    $otherField = explode(':', $rule)[1] ?? null;

                    if (
                        $otherField &&
                        ($data[$field] ?? '') !== ($data[$otherField] ?? '')
                    ) {
                        $this->errors[$field] =
                            ucfirst($field) . " does not match";
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
