<?php

namespace App\Repository\Interfaces;

interface ExpenseInterfaceRepository
{
    public function all(int $idUser, bool $intermediary = false): array;

    public function find(int $id): array;

    public function create(array $data): array | bool;

    public function update(int $id, array $data): array | bool;

    public function delete(int $id): bool;

    public function updateAllRegistersFromUser(string $column, string | int $emailOrId, array $attributes): bool;
}
