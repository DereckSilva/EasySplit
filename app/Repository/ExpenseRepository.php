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

    public function all(int $idUser, bool $intermediary = false): array {
      // VALIDAR AQUI
      return Expense::chunk(100, function (Collection $expenses) use ($idUser, $intermediary) {
            return $expenses->filter(function ($expense) use ($idUser, $intermediary) {
                return !$intermediary
                    ? $expense->payer_id == $idUser
                    : collect(json_decode($expense->intermediaries, true))->filter(function ($intermediary) use ($idUser) {
                        return $intermediary['id'] == $idUser;
                    });
        });
    })->toArray();
  }

  public function updateAllRegistersFromUser(string $column, string | int $emailOrId, array $attributes): bool {
      DB::beginTransaction();
      try {
          // validar para o owner e intermediario (whereJsonContains)
          Expense::where($column, $emailOrId)->update($attributes);
          DB::commit();
          return true;
      } catch (PDOException $exception) {
          DB::rollBack();
          return false;
      }
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
