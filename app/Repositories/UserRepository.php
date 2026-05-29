<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Mappers\UserMapper;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $spGetAll = 'sp_get_users';
    protected string $spGetById = 'sp_get_user_by_id';
    protected string $spCreate = 'sp_create_user';
    protected string $spUpdate = 'sp_update_user';
    protected string $spDelete = 'sp_delete_user';

    /*
    |--------------------------------------------------------------------------
    | FIND BY EMAIL (SP VERSION)
    |--------------------------------------------------------------------------
    */
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("CALL sp_find_user_by_email(?)");
        $stmt->execute([$email]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data ? UserMapper::fromArray($data) : null;
    }
}
