<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository
extends BaseRepository
implements UserRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL USERS
    |--------------------------------------------------------------------------
    */

    public function getAll(
        ?int $limit = null,
        int $offset = 0
    ): array {

        $results = $this->fetchAll(
            "CALL sp_get_users(:limit, :offset)",
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        $users = [];

        foreach ($results as $row) {
            $users[] = $this->mapUser($row);
        }

        return $users;
    }

    /*
    |--------------------------------------------------------------------------
    | GET USER BY ID
    |--------------------------------------------------------------------------
    */

    public function getById(
        int $id
    ): ?User {

        $data = $this->fetchOne(
            "CALL sp_get_user_by_id(:id)",
            [
                ':id' => $id
            ]
        );

        if (!$data) {
            return null;
        }

        return $this->mapUser($data);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {

        return $this->execute(
            "CALL sp_create_user(
                :username,
                :email,
                :password
            )",
            [
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword()
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        User $user
    ): bool {

        return $this->execute(
            "CALL sp_update_user(
                :id,
                :username,
                :email
            )",
            [
                ':id' => $id,
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail()
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): bool {

        return $this->execute(
            "CALL sp_delete_user(:id)",
            [
                ':id' => $id
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */

    public function findByEmail(
        string $email
    ): ?User {

        $data = $this->fetchOne(
            "CALL sp_find_user_by_email(:email)",
            [
                ':email' => $email
            ]
        );

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
