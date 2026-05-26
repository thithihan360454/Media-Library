<?php

namespace App\Services;

use App\Interfaces\CatalogRepositoryInterface;

class CatalogService extends BaseService
{
    private CatalogRepositoryInterface $repo;

    public function __construct(
        CatalogRepositoryInterface $repo
    ) {
        $this->repo = $repo;
    }

    public function getHomePageData(): array
    {
        return [
            'random' => $this->repo->getRandomCatalog(),
            'pageTitle' => 'Personal Media Library',
            'section' => 'catalog'
        ];
    }

    public function getCatalogPage(
        array $queryParams
    ): array {

        $section = $this->getCategory($queryParams);

        $search = $this->getSearchTerm($queryParams);

        $currentPage = $this->getCurrentPage($queryParams);

        $totalItems = $this->repo->count([
            'category' => $section,
            'search' => $search
        ]);

        $pagination = $this->buildPagination(
            $totalItems,
            $currentPage
        );

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
            'queryString' => $this->buildQueryString(
                $section,
                $search
            )
        ];
    }

    private function getCategory(
        array $params
    ): ?string {

        $category = $params['cat'] ?? null;

        $allowed = ['books', 'movies', 'music'];

        return (
            $category !== null
            && in_array($category, $allowed, true)
        )
            ? $category
            : null;
    }

    private function getSearchTerm(
        array $params
    ): ?string {

        $search = trim($params['s'] ?? '');

        return $search !== ''
            ? $search
            : null;
    }

    private function loadCatalogData(
        ?string $section,
        ?string $search,
        int $limit,
        int $offset
    ): array {

        if ($search !== null && $section !== null) {
            return $this->repo->getSearchCatalog(
                $search,
                $section,
                $limit,
                $offset
            );
        }

        if ($search !== null) {
            return $this->repo->getSearchCatalog(
                $search,
                null,
                $limit,
                $offset
            );
        }

        if ($section !== null) {
            return $this->repo->getCategoryCatalog(
                $section,
                $limit,
                $offset
            );
        }

        return $this->repo->getAll(
            $limit,
            $offset
        );
    }

    private function buildPageTitle(
        ?string $section
    ): string {

        return $section
            ? ucfirst($section)
            : 'Full Catalog';
    }

    public function getById(
        int $id
    ): ?array {

        return $this->repo->getById($id);
    }
}
