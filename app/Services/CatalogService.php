<?php

namespace App\Services;

use App\Interfaces\CatalogRepositoryInterface;
use App\Exceptions\ValidationException;
use App\Exceptions\DatabaseException;

class CatalogService extends BaseService
{
    private CatalogRepositoryInterface $repo;

    public function __construct(CatalogRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /*
    |--------------------------------------------------------------------------
    | HOME PAGE DATA
    |--------------------------------------------------------------------------
    */
    public function getHomePageData(): array
    {
        return [
            'random' => $this->repo->getRandomCatalog(),
            'pageTitle' => 'Personal Media Library',
            'section' => 'catalog'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CATALOG PAGE
    |--------------------------------------------------------------------------
    */
    public function getCatalogPage(array $queryParams): array
    {
        $section = $this->getCategory($queryParams);
        $search = $this->getSearchTerm($queryParams);
        $currentPage = $this->getCurrentPage($queryParams);

        $totalItems = $this->repo->count([
            'category' => $section,
            'search' => $search
        ]);

        if ($totalItems === false) {
            throw new DatabaseException("Failed to count catalog items");
        }

        $pagination = $this->buildPagination($totalItems, $currentPage);

        $catalog = $this->loadCatalogData(
            $section,
            $search,
            $pagination['limit'],
            $pagination['offset']
        );

        return [
            'catalog' => $catalog,
            'section' => $section,
            'search' => $search,
            'currentPage' => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'pageTitle' => $this->buildPageTitle($section),
            'queryString' => $this->buildQueryString($section, $search)
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY VALIDATION (NOW STRICT)
    |--------------------------------------------------------------------------
    */
    private function getCategory(array $params): ?string
    {
        $category = $params['cat'] ?? null;

        if ($category === null) {
            return null;
        }

        $allowed = ['books', 'movies', 'music'];

        if (!in_array($category, $allowed, true)) {
            throw new ValidationException([
                'cat' => 'Invalid category selected'
            ]);
        }

        return $category;
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */
    private function getSearchTerm(array $params): ?string
    {
        $search = trim($params['s'] ?? '');

        return $search !== '' ? $search : null;
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD DATA
    |--------------------------------------------------------------------------
    */
    private function loadCatalogData(
        ?string $section,
        ?string $search,
        int $limit,
        int $offset
    ): array {

        $catalog = null;

        if ($search !== null && $section !== null) {
            $catalog = $this->repo->getSearchCatalog($search, $section, $limit, $offset);
        } elseif ($search !== null) {
            $catalog = $this->repo->getSearchCatalog($search, null, $limit, $offset);
        } elseif ($section !== null) {
            $catalog = $this->repo->getCategoryCatalog($section, $limit, $offset);
        } else {
            $catalog = $this->repo->getAll($limit, $offset);
        }

        if ($catalog === false) {
            throw new DatabaseException("Failed to load catalog data");
        }

        return $catalog;
    }

    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */
    private function buildPageTitle(?string $section): string
    {
        return $section ? ucfirst($section) : 'Full Catalog';
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */
    public function getById(int $id): ?array
    {
        if ($id <= 0) {
            throw new ValidationException([
                'id' => 'Invalid catalog ID'
            ]);
        }

        $data = $this->repo->getById($id);

        if (!$data) {
            throw new ValidationException([
                'catalog' => 'Item not found'
            ]);
        }

        return $data;
    }
}
