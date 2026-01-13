<?php

namespace App\Repository\Interfaces;

use App\DTO\UserDTO;

interface UserInterfaceRepository
{
    public function all(): array;

    public function find(int $identifier, string $column): array;

    public function create(UserDTO $data): array | bool;

    public function update($id, array $data): array | bool;

    public function delete(int $id): bool;
}
