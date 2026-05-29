<?php

namespace App\Interfaces;

interface BaseRepositoryInterface
{
    public function getAll(int $limit = 10, int $offset = 0): array;

    public function getById(int $id): ?array;

    public function create(array $data): bool;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
