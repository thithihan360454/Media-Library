<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

use App\Models\User;

use App\Mappers\UserMapper;

use App\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | STORED PROCEDURES
    |--------------------------------------------------------------------------
    */
    protected string $spGetAll      = 'sp_get_users';

    protected string $spGetById     = 'sp_get_user_by_id';

    protected string $spCreate      = 'sp_create_user';

    protected string $spUpdate      = 'sp_update_user';

    protected string $spDelete      = 'sp_delete_user';

    protected string $spFindByEmail = 'sp_find_user_by_email';

    /*
    |--------------------------------------------------------------------------
    | FIND USER BY EMAIL
    |--------------------------------------------------------------------------
    */
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare(
            "CALL {$this->spFindByEmail}(?)"
        );

        $stmt->execute([$email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        if (!$data) {
            return null;
        }

        return UserMapper::fromArray($data);
    }
}
