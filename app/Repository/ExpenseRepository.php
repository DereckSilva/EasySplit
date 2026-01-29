<?php

namespace App\Repository;

use App\Models\Expense;
use App\Notifications\ExpenseNotification;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Trait\VerifiedAuthorization;
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

      Expense::where('id', $id)->update($data);
      $expense = Expense::find($id);
      $this->verifiedAuth('update', $expense);

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

  public function all(int $idUser): array {
    return Expense::all()->where('payer_id', '=', $idUser)->toArray();
  }

  public function delete(int $id): bool {
    DB::beginTransaction();
    try {
        $expense = Expense::find($id);
        $this->verifiedAuth('delete', $expense);

        $expense->notifications()->delete();
        $expense->delete();
        DB::commit();
        return true;
    } catch (PDOException $exception) {
        DB::rollBack();
        return false;
    }
  }

  public function expenseNotification(array $expenseNot): array | bool {
    DB::beginTransaction();
    try {

      // REFATORAR
      // atualiza recebimento de notificação da conta
      $expense = $this->find($expenseNot['owner_expense']['expense']);
      $expense->receive_notification = $expenseNot['owner_expense']['notification'];

      if (!empty($expenseNot['intermediary_expense'])) {

        // REFATORAR
        $errors = array();
        collect($expenseNot['intermediary_expense']['expenses'])->each(function ($expense) use ($expenseNot, &$errors) {
          $exp = $this->find($expense['id']);

          if (empty($exp) || !$exp->intermediary) {
            $message = empty($exp) ? 'Despesa não encontrada.' : "A despesa {$exp->id} não possui intermediários.";
            return $this->retornoExceptionErroRequest(false, $message, 400, []);
          }

          $intermediaries  = json_decode($exp->intermediaries, true);
          $notFoundInterm = collect($intermediaries)->filter(function ($intermediary) use ($expenseNot) {
            return $intermediary['email'] == $expenseNot['intermediary_expense']['email'];
          })->toArray();

          if (empty($notFoundInterm)) {
            return $this->retornoExceptionErroRequest(false, 'Intermediário não encontrado.', 400, []);
          }

          $intermediaries = collect($intermediaries)->map(function ($intermediary) use ($expense, $expenseNot) {
            if (isset($intermediary['notification']) && $intermediary['email'] == $expenseNot['intermediary_expense']['email']) {
              $intermediary['notification'] = $expense['notification'];
            }
            return $intermediary;
          })->toJson();
          $exp->intermediaries = $intermediaries;
        });
      }

      if (!empty($errors)) {
        return $errors;
      }

      $expense->save();
      DB::commit();
      return $expense->toArray();
    } catch (PDOException $exception) {
      DB::rollBack();
      return false;
    }
  }
}
