<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected PDO $db;

    protected string $table;

    protected string $primaryKey = 'id';

    protected array $columns = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    protected function getColumnList(): string
    {
        return implode(', ', $this->columns);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    |--------------------------------------------------------------------------
    */

    public function getAll(
        ?int $limit = null,
        int $offset = 0
    ): array {

        $columns = $this->getColumnList();

        $sql = "
            SELECT {$columns}
            FROM {$this->table}
        ";

        if ($limit !== null) {
            $sql .= "
                LIMIT :limit
                OFFSET :offset
            ";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {

            $stmt->bindValue(
                ':limit',
                $limit,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':offset',
                $offset,
                PDO::PARAM_INT
            );
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | GET BY ID
    |--------------------------------------------------------------------------
    */

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("CALL sp_get_item_full_detail(?)");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        // MAIN ITEM
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $stmt->closeCursor();
            return null;
        }

        // MOVE TO NEXT RESULT SET
        $stmt->nextRowset();

        // MULTI VALUE FIELDS
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $role = strtolower(trim($row['role']));

            if ($role === 'direct') {
                $role = 'director';
            }
            $item[$role][] = $row['fullname'];
        }

        $stmt->closeCursor();

        return $item;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(
        mixed $data
    ): bool {

        $fields = array_keys((array) $data);

        $columns = implode(', ', $fields);

        $placeholders = implode(
            ', ',
            array_map(
                fn($field) => ':' . $field,
                $fields
            )
        );

        $sql = "
            INSERT INTO {$this->table}
            ({$columns})
            VALUES ({$placeholders})
        ";

        return $this->execute(
            $sql,
            (array) $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        mixed $data
    ): bool {

        $fields = array_keys((array) $data);

        $setClause = implode(
            ', ',
            array_map(
                fn($field) => "{$field} = :{$field}",
                $fields
            )
        );

        $sql = "
            UPDATE {$this->table}
            SET {$setClause}
            WHERE {$this->primaryKey} = :id
        ";

        $params = (array) $data;
        $params['id'] = $id;

        return $this->execute($sql, $params);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): bool {

        $sql = "
            DELETE FROM {$this->table}
            WHERE {$this->primaryKey} = :id
        ";

        return $this->execute($sql, [
            ':id' => $id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function query(
        string $sql,
        array $params = []
    ): \PDOStatement {

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt;
    }

    protected function fetchOne(
        string $sql,
        array $params = []
    ): ?array {

        $stmt = $this->query($sql, $params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $result ?: null;
    }

    protected function fetchAll(
        string $sql,
        array $params = []
    ): array {

        $stmt = $this->query($sql, $params);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $results;
    }

    protected function execute(
        string $sql,
        array $params = []
    ): bool {

        $stmt = $this->db->prepare($sql);

        $success = $stmt->execute($params);

        $stmt->closeCursor();

        return $success;
    }
}
