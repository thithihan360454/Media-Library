<?php

namespace  App\Services;

use App\Core\Database;
use App\Interfaces\CatalogRepositoryInterface;
use App\Repository\BaseRepository;
use App\Repositories\CatalogRepository;
// use inc\Database;
use App\Services\BaseService;
/*
 * Handles catalog business logic and orchestration.
 * Does NOT directly talk to DB.
 * Only uses Repository.
 */

class CatalogService extends BaseService
{
    private CatalogRepositoryInterface $repo;

    /*
     * Constructor with dependency injection (defensive fallback included)
     */
    public function __construct(?CatalogRepositoryInterface $repo = null)
    {
        if ($repo === null) {
            $db = Database::getConnection();
            $repo = new CatalogRepository($db);
        }

        $this->repo = $repo;
    }

    /* =========================================================
     * HOME PAGE DATA
     * ========================================================= */
    public function getHomePageData(): array
    {
        return [
            'random' => $this->repo->getRandomCatalog(),
            'pageTitle' => 'Personal Media Library',
            'section' => 'catalog'
        ];
    }

    /* =========================================================
     * MAIN CATALOG PAGE ORCHESTRATION
     * ========================================================= */
    public function getCatalogPage(array $queryParams): array
    {
        $section = $this->getCategory($queryParams);
        $search = $this->getSearchTerm($queryParams);
        $currentPage = $this->getCurrentPage($queryParams);

        $totalItems = $this->repo->count([
            'category' => $section,
            'search' => $search
        ]);

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

    /* =========================================================
     * CATEGORY VALIDATION
     * ========================================================= */
    private function getCategory(array $params): ?string
    {
        $category = $params['cat'] ?? null;

        $allowed = ['books', 'movies', 'music'];

        return ($category !== null && in_array($category, $allowed, true))
            ? $category
            : null;
    }

    /* =========================================================
     * SEARCH HANDLING
     * ========================================================= */
    private function getSearchTerm(array $params): ?string
    {
        $search = trim($params['s'] ?? '');

        return $search !== '' ? $search : null;
    }

    // /* =========================================================
    //  * PAGINATION INPUT
    //  * ========================================================= */
    // private function getCurrentPage(array $params): int
    // {
    //     $page = filter_var($params['pg'] ?? 1, FILTER_VALIDATE_INT);

    //     return ($page === false || $page < 1) ? 1 : $page;
    // }

    // /* =========================================================
    //  * PAGINATION LOGIC
    //  * ========================================================= */
    // private function buildPagination(int $totalItems, int $currentPage): array
    // {
    //     $itemsPerPage = 8;

    //     $totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));

    //     if ($currentPage > $totalPages) {
    //         $currentPage = $totalPages;
    //     }

    //     $offset = ($currentPage - 1) * $itemsPerPage;
    //     return [
    //         'limit' => $itemsPerPage,
    //         'offset' => $offset,
    //         'currentPage' => $currentPage,
    //         'totalPages' => $totalPages
    //     ];
    // }
    /* =========================================================
     * DATA ORCHESTRATION (CHOOSING REPOSITORY METHOD)
     * ========================================================= */
    private function loadCatalogData(
        ?string $section,
        ?string $search,
        int $limit,
        int $offset
    ): array {
        if ($search !== null && $section !== null) {
            return $this->repo->getSearchCatalog($search, $section, $limit, $offset);
        }

        if ($search !== null) {
            return $this->repo->getSearchCatalog($search, null, $limit, $offset);
        }

        if ($section !== null) {
            return $this->repo->getCategoryCatalog($section, $limit, $offset);
        }

        return $this->repo->getAll($limit, $offset);
    }

    /* =========================================================
     * UI HELPERS
     * ========================================================= */
    private function buildPageTitle(?string $section): string
    {
        return $section ? ucfirst($section) : 'Full Catalog';
    }

    // private function buildQueryString(?string $section, ?string $search): string
    // {
    //     $params = [];

    //     if ($section !== null) {
    //         $params[] = 'cat=' . urlencode($section);
    //     }

    //     if ($search !== null) {
    //         $params[] = 's=' . urlencode($search);
    //     }

    //     return implode('&', $params);
    // }

    /* =========================================================
     * SINGLE ITEM (OPTIONAL BUSINESS LOGIC LAYER)
     * ========================================================= */
    public function getById(int $id): array
    {
        return $this->repo->getById($id);
    }
}
