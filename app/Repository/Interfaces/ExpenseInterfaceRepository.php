<?php

namespace App\Repository\Interfaces;

use App\Models\Expense;
use Illuminate\Http\Exceptions\HttpResponseException;

interface ExpenseInterfaceRepository
{
    public function all(): array;

    public function find(int $id): array;

    public function create(array $data): array | bool;

    public function update(int $id, array $data): array | bool;

    public function delete(int $id): bool;
}
