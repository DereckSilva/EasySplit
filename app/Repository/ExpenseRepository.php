<?php

namespace App\Repository;

use App\Models\Expense;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Trait\VerifiedAuthorization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDOException;

class ExpenseRepository implements ExpenseInterfaceRepository {

  use VerifiedAuthorization;

  protected $model = 'Expenses';

  public function create(array $data): array | bool {
    DB::beginTransaction();
    try {
        $expense = Expense::create($data);
        $this->verifiedAuth('create', $expense);
        $expense->save();
        DB::commit();
        return $expense->toArray();
    } catch (PDOException $exception) {
        DB::rollBack();
        return false;
    }
  }

  public function update(int $id, array $data): array | bool {
    DB::beginTransaction();
    try {
      $expense = Expense::find($id);
      $this->verifiedAuth('update', $expense);
      $expense->update($data);
      DB::commit();
      return $expense->toArray();
    } catch (PDOException $exception) {
      DB::rollback();
      return false;
    }
  }

  public function find(int $id): array {
    $expense = Expense::find($id);
    if(empty($expense)) {
      return [];
    }

    $this->verifiedAuth('view', $expense);

    return $expense->toArray();
  }

  public function allExpenseFromOwner(int $idUser): array {
    $expensesOwner = array();
    Expense::chunk(100, function (Collection $expenses) use ($idUser, &$expensesOwner) {
        $expensesOwner = $expenses->where('payer_id', $idUser)->toArray();
    });

    return $expensesOwner;
  }

  public function allExpenseFromIntermediary(int $idIntermediary): array {
    $expensesIntermediary = array();
    Expense::where('intermediary', true)
        ->whereJsonContains('intermediaries', ['id' => $idIntermediary])
        ->chunk(100, function (Collection $expenses) use (&$expensesIntermediary) {
            $expensesIntermediary = collect($expenses->map(function($expense) {
                return $expense->intermediaries;
            }))->toArray();
        });

    return $expensesIntermediary;
  }

  public function updateAllExpenseFromOwner(string $column, string | int $emailOrId, array $attributes): bool {
    DB::beginTransaction();
    try {
      Expense::where($column, $emailOrId)->update($attributes);
      DB::commit();
      return true;
    } catch (PDOException $exception) {
      DB::rollBack();
      return false;
    }
  }

  public function updateAllRegistersFromIntermediary(string $column, int|string $emailOrId, array $attributes): bool
  {
    return false;
  }

    public function delete(int $id): bool {
        DB::beginTransaction();
        try {
            $expense = Expense::find($id);
            $this->verifiedAuth('delete', $expense);

            $expense->delete();
            DB::commit();
            return true;
        } catch (PDOException $exception) {
            DB::rollBack();
            return false;
        }
    }
}
