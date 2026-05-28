<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
extends BaseRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */

    public function findByEmail(
        string $email
    ): ?User;
}
