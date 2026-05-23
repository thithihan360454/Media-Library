<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\BaseInterface;

abstract class BaseRepository implements BaseInterface
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function count(
        array $filters = []
    ): int {

        $search = $filters['search'] ?? null;
        $category = $filters['category'] ?? null;

        $result = $this->db->prepare(
            "CALL sp_search_catalog_count(
                :search,
                :category
            )"
        );

        $result->bindValue(
            ':search',
            $search,
            $search === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_STR
        );

        $result->bindValue(
            ':category',
            $category,
            $category === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_STR
        );

        $result->execute();

        $count = $result->fetchColumn();

        $result->nextRowset();
        $result->closeCursor();

        return (int) $count;
    }

    public function getAll(
        ?int $limit = null,
        int $offset = 0
    ): array {

        $result = $this->db->prepare(
            "CALL sp_get_full_catalog(?, ?)"
        );

        $result->bindParam(
            1,
            $limit,
            $limit === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_INT
        );

        $result->bindParam(
            2,
            $offset,
            PDO::PARAM_INT
        );

        $result->execute();

        $catalog = $result->fetchAll();

        $result->closeCursor();

        return $catalog;
    }

    abstract public function getById(
        int $id
    ): ?array;
}
