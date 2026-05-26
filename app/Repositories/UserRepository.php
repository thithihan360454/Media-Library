<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository
extends BaseRepository
implements UserRepositoryInterface
{
    public function getAll(
        ?int $limit = null,
        int $offset = 0
    ): array {

        return $this->fetchAll(
            "CALL sp_get_users(:limit, :offset)",
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );
    }

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

    public function create(
        mixed $data
    ): bool {

        return $this->execute(
            "CALL sp_create_user(
            :username,
            :email,
            :password
        )",
            [
                ':username' => $data->username,
                ':email' => $data->email,
                ':password' => $data->password
            ]
        );
    }

    public function update(
        int $id,
        mixed $data
    ): bool {

        return $this->execute(
            "CALL sp_update_user(
            :id,
            :username,
            :email
        )",
            [
                ':id' => $id,
                ':username' => $data->username,
                ':email' => $data->email
            ]
        );
    }

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

    private function mapUser(
        array $data
    ): User {

        $user = new User();

        $user->user_id = $data['user_id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        return $user;
    }
}
