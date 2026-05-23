<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\CatalogRepositoryInterface;

/**
 * Handles catalog database operations
 */
class CatalogRepository
    extends BaseRepository
    implements CatalogRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    /**
     * Get catalog items by category
     */
    public function getCategoryCatalog(
        $category,
        $limit = null,
        $offset = 0
    ) {
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

        $catalog = $result->fetchAll();

        $result->closeCursor();

        return $catalog;
    }

    /**
     * Search catalog
     */
    public function getSearchCatalog(
        $search,
        $category = null,
        $limit = null,
        $offset = 0
    ) {
        $search = ($search === '' ? null : $search);
        $category = ($category === '' ? null : $category);

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
            PDO::PARAM_INT
        );

        $result->bindValue(
            4,
            $offset,
            PDO::PARAM_INT
        );

        $result->execute();

        $catalog = $result->fetchAll();

        $result->nextRowset();
        $result->closeCursor();

        return $catalog;
    }

    /**
     * Get random catalog items
     */
    public function getRandomCatalog()
    {
        $result = $this->db->query(
            "SELECT * FROM view_random"
        );

        return $result->fetchAll();
    }

    /**
     * Get single item by ID
     */
    public function getById(
    int $id
): ?array
{
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
        $item[strtolower($row['role'])][] =
            $row['fullname'];
    }

    $result->closeCursor();

    return $item;
}
}