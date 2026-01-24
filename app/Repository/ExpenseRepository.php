<?php

namespace App\Repository;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseNotification;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Trait\ResponseHttp;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use PDOException;

class ExpenseRepository implements ExpenseInterfaceRepository {

  use ResponseHttp;

  protected $model = 'Expenses';

  public function create(array $data): array | bool {
    DB::beginTransaction();
    try {
      $expense = Expense::create($data);

      // dispara a notificação -> validar posteriormente
      /*if (!empty($intermediarios)) {
        collect($intermediarios)->each(function ($id) use ($userRepository, $expense) {
          $user = $userRepository->find($id, 'email');
            $expense->notify(new ExpenseNotification($user, $expense, 'Conta criada pelo usuário: '));
        });
      }*/

      $expense->save();
      DB::commit();
      return $expense->toArray();
    } catch (PDOException $exception) {
        dd($exception->getMessage());
      DB::rollBack();
      return false;
    }
  }

  public function update(int $id, array $data): array | bool {
    DB::beginTransaction();
    try {

      Expense::where('id', $id)->update($data);
      $expense = Expense::find($id)->toArray();

      DB::commit();
      return $expense;
    } catch (PDOException $exception) {
      DB::rollback();
      return false;
    }
  }

  public function find(int $id): array| bool {
    $expense = Expense::find($id);
    if(empty($expense)) {
      return false;
    }
    return $expense->toArray();
  }

  public function findAll(): array {
    return Expense::all()->toArray();
  }

  public function findMany(array $ids = []): array {
    return Expense::findMany($ids)->toArray();
  }

  public function remove(int $id): bool | HttpResponseException {
    $expense = $this->find($id);

    if (empty($expense)) {
      return $this->retornoExceptionErroRequest(false, 'Conta não cadastrada.', 400, []);
    }

    return Expense::destroy($id);
  }

  public function expenseNotification(array $expenseNot): array | HttpResponseException {
    DB::beginTransaction();
    try {

        // refatorar

      if (isset($expenseNot['owner_expense']) && !empty($expenseNot['owner_expense'])) {
        // atualiza recebimento de notificação da conta
        $expense = $this->find($expenseNot['owner_expense']['expense']);
        $expense->receive_notification = $expenseNot['owner_expense']['notification'];
        $expense->save();
      }

      if (isset($expenseNot['intermediary_expense']) && !empty($expenseNot['intermediary_expense'])) {

        $errors = array();
        collect($expenseNot['intermediary_expense']['expenses'])->each(function ($expense) use ($expenseNot, &$errors) {
          $exp = $this->find($expense['id']);

          if (empty($exp) || !$exp->intermediary) {
            $message = empty($exp) ? 'Despesa não encontrada.' : "A despesa {$exp->id} não possui intermediários.";
            return $this->retornoExceptionErroRequest(false, $message, 400, []);
          }

          $intermediarys  = json_decode($exp->intermediarys, true);
          $notFoundInterm = collect($intermediarys)->filter(function ($intermediary) use ($expenseNot) {
            return $intermediary['email'] == $expenseNot['intermediary_expense']['email'];
          })->toArray();

          if (empty($notFoundInterm)) {
            return $this->retornoExceptionErroRequest(false, 'Intermediário não encontrado.', 400, []);
          }

          $intermediarys = collect($intermediarys)->map(function ($intermediary) use ($expense, $expenseNot) {
            if (isset($intermediary['notification']) && $intermediary['email'] == $expenseNot['intermediary_expense']['email']) {
              $intermediary['notification'] = $expense['notification'];
            }
            return $intermediary;
          })->toJson();
          $exp->intermediarys = $intermediarys;
          $exp->save();
        });
      }

      if (!empty($errors)) {
        return $errors;
      }

      DB::commit();
      return [
        'status'  => true,
        'message' => 'Notificação atualizada com sucesso',
        'data'    => []
      ];
    } catch (PDOException $exception) {
      DB::rollBack();
      return $this->retornoExceptionErroRequest(false, 'Erro: ' . $exception->getMessage(), 400, []);
    }
  }

    public function all(): array
    {
        return [];
    }

    public function delete(int $id): bool
    {
        return false;
    }
}
