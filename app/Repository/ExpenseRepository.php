<?php

namespace App\Repository;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use PDOException;

class ExpenseRepository {

  protected $model = 'Expenses';

  public function create(array $expense): array {
    DB::beginTransaction();
    try {
      $userRepository = app('App\Repository\UserRepository');

      // validar data de pagamento (deve ser maior ou igual ao dia atual)

      // validar os intermediarys_id
      if (!empty($expense['intermediarys_id'])) {
        $expense['intermediarys_id'] = collect($expense['intermediarys_id'])->each(function ($id) use ($userRepository) {
          $teste = $userRepository->find($id);
          return !empty($teste);
        })->toJson();
      }

      $expense = Expense::create($expense);

      //DB::commit();
      return $expense->toArray();
    } catch (PDOException $exception) {
      DB::rollBack();
      return [];
    }
  }

  public function find(int $id): array {
    return Expense::find($id)->toArray();
  }

  public function findAll(array $ids = []): array {
    return Expense::findMany(!empty($ids) ?? $ids)->toArray();
  }

  public function remvoe(int $id): bool {
    return Expense::destroy($id);
  }
}
