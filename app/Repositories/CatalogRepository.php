<?php

namespace App\Repositories;

use App\Interfaces\CatalogRepositoryInterface;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    protected string $spGetAll = 'sp_get_full_catalog';
    protected string $spGetById = 'sp_get_item_full_detail';

    protected bool $hasMultiRowset = true;

    /*
    |--------------------------------------------------------------------------
    | COUNT (SP)
    |--------------------------------------------------------------------------
    */
    public function count(array $filters = []): int
    {
        $search = $filters['search'] ?? null;
        $category = $filters['category'] ?? null;

        $stmt = $this->db->prepare("CALL sp_search_catalog_count(?, ?)");
        $stmt->execute([$search, $category]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return (int) ($result['total'] ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY CATALOG (SP)
    |--------------------------------------------------------------------------
    */
    public function getCategoryCatalog(
        string $category,
        ?int $limit = null,
        int $offset = 0
    ): array {
        return $this->fetchAllSP('sp_get_catalog', [
            $category,
            $limit,
            $offset
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH CATALOG (SP)
    |--------------------------------------------------------------------------
    */
    public function getSearchCatalog(
        ?string $search,
        ?string $category = null,
        ?int $limit = null,
        int $offset = 0
    ): array {
        return $this->fetchAllSP('sp_search_catalog', [
            $search,
            $category,
            $limit,
            $offset
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RANDOM CATALOG (SP)
    |--------------------------------------------------------------------------
    */
    public function getRandomCatalog(): array
    {
        return $this->fetchAllSP('sp_get_random_catalog');
    }
}
