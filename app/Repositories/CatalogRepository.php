<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\CatalogRepositoryInterface;

class CatalogRepository
extends BaseRepository
implements CatalogRepositoryInterface
{
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

        $catalog = $result->fetchAll(PDO::FETCH_ASSOC);

        $result->closeCursor();

        return $catalog;
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

    public function getCategoryCatalog(
        string $category,
        ?int $limit = null,
        int $offset = 0
    ): array {

        $result = $this->db->prepare(
            "CALL sp_get_catalog(?, ?, ?)"
        );

        $result->bindParam(
            1,
            $category,
            PDO::PARAM_STR
        );

        $result->bindParam(
            2,
            $limit,
            $limit === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_INT
        );

        $result->bindParam(
            3,
            $offset,
            PDO::PARAM_INT
        );

        $result->execute();

        $catalog = $result->fetchAll(PDO::FETCH_ASSOC);

        $result->closeCursor();

        return $catalog;
    }

    public function getSearchCatalog(
        ?string $search,
        ?string $category = null,
        ?int $limit = null,
        int $offset = 0
    ): array {

        $result = $this->db->prepare(
            "CALL sp_search_catalog(?, ?, ?, ?)"
        );

        $result->bindValue(
            1,
            $search,
            $search === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_STR
        );

        $result->bindValue(
            2,
            $category,
            $category === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_STR
        );

        $result->bindValue(
            3,
            $limit,
            $limit === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_INT
        );

        $result->bindValue(
            4,
            $offset,
            PDO::PARAM_INT
        );

        $result->execute();

        $catalog = $result->fetchAll(PDO::FETCH_ASSOC);

        $result->nextRowset();
        $result->closeCursor();

        return $catalog;
    }

    public function getRandomCatalog(): array
    {
        return $this->fetchAll(
            "CALL sp_get_random_catalog()"
        );
    }

    public function getById(
        int $id
    ): ?array {

        $result = $this->db->prepare(
            "CALL sp_get_item_full_detail(?)"
        );

        $result->bindParam(
            1,
            $id,
            PDO::PARAM_INT
        );

        $result->execute();

        $item = $result->fetch(PDO::FETCH_ASSOC);

        if ($item === false) {
            $result->closeCursor();
            return null;
        }

        $result->nextRowset();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $role = strtolower($row['role']);

            $item[$role][] = $row['fullname'];
        }

        $result->closeCursor();

        return $item;
    }
}
