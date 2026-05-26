<?php

namespace App\Interfaces;

interface BaseRepositoryInterface
{
    public function getAll(
        ?int $limit = null,
        int $offset = 0
    ): array;

    public function getById(
        int $id
    ): mixed;
}
