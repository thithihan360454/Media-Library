<?php

namespace App\Services;

/**
 * Common service logic
 */
abstract class BaseService
{
    /**
     * Pagination helper
     */
    protected function buildPagination(
        int $totalItems,
        int $currentPage,
        int $itemsPerPage = 8
    ): array {
        $totalPages = max(
            1,
            (int) ceil($totalItems / $itemsPerPage)
        );

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $itemsPerPage;

        return [
            'limit' => $itemsPerPage,
            'offset' => $offset,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
    }

    /**
     * Validate current page
     */
    protected function getCurrentPage(array $params): int
    {
        $page = filter_var(
            $params['pg'] ?? 1,
            FILTER_VALIDATE_INT
        );

        return ($page === false || $page < 1)
            ? 1
            : $page;
    }

    /**
     * Build query string
     */
    protected function buildQueryString(
        ?string $section,
        ?string $search
    ): string {
        $params = [];

        if ($section !== null) {
            $params[] = 'cat=' . urlencode($section);
        }

        if ($search !== null) {
            $params[] = 's=' . urlencode($search);
        }

        return implode('&', $params);
    }
}
