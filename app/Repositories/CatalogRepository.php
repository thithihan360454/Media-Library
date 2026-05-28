<?php

namespace App\Repositories;

use PDO;
use App\Interfaces\CatalogRepositoryInterface;

class CatalogRepository
extends BaseRepository
implements CatalogRepositoryInterface
{
    protected string $table = 'view_catalog';
    protected string $primaryKey = 'media_id';
    protected array $columns = [
        'media_id',
        'title',
        'img',
        'format',
        'year',
        'genre',
        'category'
    ];

    /*
    |--------------------------------------------------------------------------
    | COUNT CATALOG
    |--------------------------------------------------------------------------
    */

    public function count(array $filters = []): int
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = "title LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category'])) {
            $conditions[] = "category = :category";
            $params[':category'] = $filters['category'];
        }

        $where = $conditions
            ? 'WHERE ' . implode(' AND ', $conditions)
            : '';

        $sql = "
            SELECT COUNT(*) 
            FROM {$this->table}
            {$where}
        ";

        $stmt = $this->query($sql, $params);

        return (int) $stmt->fetchColumn();
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY CATALOG
    |--------------------------------------------------------------------------
    */

    public function getCategoryCatalog(
        string $category,
        ?int $limit = null,
        int $offset = 0
    ): array {

        $sql = "
            SELECT {$this->getColumnList()}
            FROM {$this->table}
            WHERE category = :category
        ";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':category', $category, PDO::PARAM_STR);

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH CATALOG
    |--------------------------------------------------------------------------
    */

    public function getSearchCatalog(
        ?string $search,
        ?string $category = null,
        ?int $limit = null,
        int $offset = 0
    ): array {

        $conditions = [];
        $params = [];

        if ($search !== null && $search !== '') {
            $conditions[] = "title LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        if ($category !== null) {
            $conditions[] = "category = :category";
            $params[':category'] = $category;
        }

        $where = $conditions
            ? 'WHERE ' . implode(' AND ', $conditions)
            : '';

        $sql = "
            SELECT {$this->getColumnList()}
            FROM {$this->table}
            {$where}
        ";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | RANDOM CATALOG
    |--------------------------------------------------------------------------
    */

    public function getRandomCatalog(): array
    {
        return $this->fetchAll(
            "CALL sp_get_random_catalog()"
        );
    }
}
