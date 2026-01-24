<?php

namespace App\Repository\Interfaces;

interface IntermediaryInterfaceRepository
{
    public function all(): array;
    public function find(string $column, string | int $value): array;
    public function create(array $data): array | bool;
}
