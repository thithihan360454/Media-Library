<?php

namespace App\Interfaces;

interface CatalogRepositoryInterface
extends BaseRepositoryInterface
{
    public function count(
        array $filters = []
    ): int;

    public function getCategoryCatalog(
        string $category,
        ?int $limit = null,
        int $offset = 0
    ): array;

    public function getSearchCatalog(
        ?string $search,
        ?string $category = null,
        ?int $limit = null,
        int $offset = 0
    ): array;

    public function getRandomCatalog(): array;
}
