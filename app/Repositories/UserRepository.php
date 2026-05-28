<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository
extends BaseRepository
implements UserRepositoryInterface
{
    protected string $table = 'users';

    protected string $primaryKey = 'user_id';

    protected array $columns = [
        'user_id',
        'username',
        'email',
        'password'
    ];

    /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */

    public function findByEmail(
        string $email
    ): ?User {

        $sql = "
            SELECT {$this->getColumnList()}
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ";

        $data = $this->fetchOne($sql, [
            ':email' => $email
        ]);

        if (!$data) {
            return null;
        }

        return $this->mapUser($data);
    }


    /*
    |--------------------------------------------------------------------------
    | MAP USER
    |--------------------------------------------------------------------------
    */

    // private function mapUser(
    //     array $data
    // ): User {

    //     $user = new User();

    //     $user->setUserId(
    //         (int) $data['user_id']
    //     );

    //     $user->setUsername(
    //         $data['username']
    //     );

    //     $user->setEmail(
    //         $data['email']
    //     );

    //     $user->setPassword(
    //         $data['password']
    //     );

    //     return $user;
    // }

    private function mapUser(array $data): User
    {
        return new User(
            $data['username'],
            $data['email'],
            $data['password'],
            (int) $data['user_id']
        );
    }
}
