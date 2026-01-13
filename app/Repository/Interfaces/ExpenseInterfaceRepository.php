<?php

namespace App\Repository\Interfaces;

use App\Models\Expense;
use Illuminate\Http\Exceptions\HttpResponseException;

interface ExpenseInterfaceRepository
{
    public function all(): array;

    public function find(int $id): Expense| HttpResponseException;

    public function create(array $data): array | HttpResponseException;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;
}
