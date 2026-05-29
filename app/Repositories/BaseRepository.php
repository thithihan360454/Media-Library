<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use PDO;

class BaseRepository implements BaseRepositoryInterface
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /*
    |--------------------------------------------------------------------------
    | STORED PROCEDURES (DEFINED IN CHILD)
    |--------------------------------------------------------------------------
    */
    protected string $spGetAll = '';
    protected string $spGetById = '';
    protected string $spCreate = '';
    protected string $spUpdate = '';
    protected string $spDelete = '';

    protected bool $hasMultiRowset = false;

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    |--------------------------------------------------------------------------
    */
    public function getAll(int $limit = 10, int $offset = 0): array
    {
        $this->guard($this->spGetAll);

        return $this->fetchAllSP($this->spGetAll, [$limit, $offset]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET BY ID
    |--------------------------------------------------------------------------
    */
    public function getById(int $id): ?array
    {
        $this->guard($this->spGetById);

        $stmt = $this->db->prepare(
            "CALL {$this->spGetById}(?)"
        );

        $stmt->execute([$id]);

        /*
    |--------------------------------------------------------------------------
    | FIRST RESULT SET
    |--------------------------------------------------------------------------
    */
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $stmt->closeCursor();
            return null;
        }

        /*
    |--------------------------------------------------------------------------
    | MULTI RESULT SET SUPPORT
    |--------------------------------------------------------------------------
    */
        if ($this->hasMultiRowset) {

            $stmt->nextRowset();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $role = strtolower(
                    trim($row['role'] ?? '')
                );

                if (!empty($role)) {

                    if ($role === 'direct') {
                        $role = 'director';
                    }

                    $item[$role][] = $row['fullname'];
                }
            }
        }

        $stmt->closeCursor();

        return $item;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): bool
    {
        $this->guard($this->spCreate);

        return $this->executeSP($this->spCreate, array_values($data));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(int $id, array $data): bool
    {
        $this->guard($this->spUpdate);

        return $this->executeSP(
            $this->spUpdate,
            array_merge([$id], array_values($data))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(int $id): bool
    {
        $this->guard($this->spDelete);

        return $this->executeSP($this->spDelete, [$id]);
    }

    /*
    |--------------------------------------------------------------------------
    | SP FETCH ONE
    |--------------------------------------------------------------------------
    */
    protected function fetchOneSP(string $sp, array $params = []): ?array
    {
        $stmt = $this->db->prepare($this->buildCall($sp, $params));
        $this->bind($stmt, $params);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | SP FETCH ALL
    |--------------------------------------------------------------------------
    */
    protected function fetchAllSP(string $sp, array $params = []): array
    {
        $stmt = $this->db->prepare($this->buildCall($sp, $params));
        $this->bind($stmt, $params);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | SP EXECUTE (NO RETURN)
    |--------------------------------------------------------------------------
    */
    protected function executeSP(string $sp, array $params = []): bool
    {
        $stmt = $this->db->prepare($this->buildCall($sp, $params));
        $this->bind($stmt, $params);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD CALL STRING
    |--------------------------------------------------------------------------
    */
    private function buildCall(string $sp, array $params): string
    {
        return "CALL {$sp}(" . $this->placeholders($params) . ")";
    }

    /*
    |--------------------------------------------------------------------------
    | GUARD CLAUSE (IMPORTANT)
    |--------------------------------------------------------------------------
    */
    private function guard(string $sp): void
    {
        if ($sp === '') {
            throw new \RuntimeException('Stored procedure not defined in repository.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PLACEHOLDERS
    |--------------------------------------------------------------------------
    */
    private function placeholders(array $params): string
    {
        return count($params)
            ? implode(',', array_fill(0, count($params), '?'))
            : '';
    }

    /*
    |--------------------------------------------------------------------------
    | BIND PARAMETERS
    |--------------------------------------------------------------------------
    */
    private function bind($stmt, array $params): void
    {
        foreach (array_values($params) as $i => $value) {
            $stmt->bindValue($i + 1, $value);
        }
    }
}
