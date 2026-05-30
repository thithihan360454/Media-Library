<?php

namespace App\Validation;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

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
                        $this->addError($field, ucfirst($field) . ' is required');
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */ elseif ($rule === 'email') {

                    if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($field, 'Invalid email format');
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MIN LENGTH
                |--------------------------------------------------------------------------
                */ elseif (str_starts_with($rule, 'min:')) {

                    $min = (int) explode(':', $rule)[1];

                    if (strlen($value) < $min) {
                        $this->addError($field, ucfirst($field) . " must be at least {$min} characters");
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MAX LENGTH
                |--------------------------------------------------------------------------
                */ elseif (str_starts_with($rule, 'max:')) {

                    $max = (int) explode(':', $rule)[1];

                    if (strlen($value) > $max) {
                        $this->addError($field, ucfirst($field) . " must not exceed {$max} characters");
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | PASSWORD STRENGTH
                |--------------------------------------------------------------------------
                */ elseif ($rule === 'password_strength') {

                    if ($value === '') {
                        break;
                    }

                    $hasUppercase = preg_match('/[A-Z]/', $value);
                    $hasLowercase = preg_match('/[a-z]/', $value);
                    $hasNumber    = preg_match('/[0-9]/', $value);
                    $hasSpecial   = preg_match('/[\W_]/', $value);

                    if (!$hasUppercase || !$hasLowercase || !$hasNumber || !$hasSpecial) {
                        $this->addError(
                            $field,
                            'Password must contain uppercase, lowercase, number and special character'
                        );
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SAME FIELD VALIDATION
                |--------------------------------------------------------------------------
                */ elseif (str_starts_with($rule, 'same:')) {

                    $otherField = explode(':', $rule)[1] ?? null;

                    if (
                        $otherField &&
                        $value !== trim($data[$otherField] ?? '')
                    ) {
                        $this->addError($field, ucfirst($field) . " does not match");
                        break;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
