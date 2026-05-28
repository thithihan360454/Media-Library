<?php

declare(strict_types=1);

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

    public function create(
        mixed $entity
    ): bool;

    public function update(
        int $id,
        mixed $entity
    ): bool;

    public function delete(
        int $id
    ): bool;
}
