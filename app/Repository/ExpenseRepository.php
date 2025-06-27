<?php

namespace App\Repository;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseNotification;
use App\Trait\Request;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use PDOException;

class ExpenseRepository {

  use Request;

  protected $model = 'Expenses';

  public function create(array $expense): array | HttpResponseException {
    DB::beginTransaction();
    try {
      $userRepository = app('App\Repository\UserRepository');

      // validar vencimento da conta
      $currentDate = Carbon::now();
      $datePayment = Carbon::create($expense['datePayment']);
      $month       = $currentDate->month;

      // seta data para o vencimento da conta
      $currentDate->setDay($datePayment->day);
      $currentDate->setMonth($month + $expense['parcels']);
      $expense['maturity'] = $currentDate->format('Y-m-d');

      // validar o payee_id
      $payee = $userRepository->find($expense['payee_id']);
      if (isset($payee['status']) && !$payee['status']) {
        return $payee;
      }

      // validar os intermediarys
      $intermediarios = [];
      if (!empty($expense['intermediarys']) && is_array($expense['intermediarys'])) {
        $expense['intermediarys'] = collect($expense['intermediarys'])->filter(function ($identifier) use ($userRepository) {
          $user = $userRepository->find($identifier['email'], 'email');
          $intermediary = $user instanceof User ? $intermediary = $user->email : [];
          return !empty($intermediary);
        })->toArray();

        sort($expense['intermediarys']);

        // passa pelos intermediarios e acrescenta as informacoes de notificação e valor da conta
        $expense['intermediarys'] = collect($expense['intermediarys'])->map(function ($identifier) use ($expense) {
          $people = count($expense['intermediarys']) + 1;
          return ['email' => $identifier['email'], 'totalAmount' => (float)$expense['priceTotal'] / $people, 'notification' => $expense['receiveNotification']];
        })->toJson();

        // ajuste de intermediários que querem receber notificação
        $intermediarios = collect($expense['intermediarys'])->filter(function ($data, $key) {
          $dataInterm = json_decode($data, true);
          return !empty($dataInterm) && isset($dataInterm[$key]) ? $dataInterm[$key]['notification'] : [];
        })
        ->map(function ($data, $key) {
          $dataInterm = json_decode($data, true);
          return !empty($dataInterm) && isset($dataInterm[$key]) ? $dataInterm[$key]['email'] : [];
        });
      }
      
      $expense = Expense::create($expense);
      
      // dispara a notificação
      if (!empty($intermediarios)) {
        collect($intermediarios)->each(function ($id) use ($userRepository, $expense) {
          $user = $userRepository->find($id, 'email');
            $expense->notify(new ExpenseNotification($user, $expense));
        });
      }

      $expense->save();
      DB::commit();
      return [
        'status'     => true,
        'data'       => $expense->toArray(),
        'message'    => 'Conta criada com sucesso',
        'statusCode' => 201
      ];
    } catch (PDOException $exception) {
      DB::rollBack();
      return $this->retornoExceptionErroRequest(false, $exception->getMessage(), 400, []);
    }
  }

  public function find(int $id): Expense|null {
    $expense = Expense::find($id);
    return !empty($expense) ? $expense : null;
  }

  public function findAll(array $ids = []): array {
    return Expense::findMany(!empty($ids) ?? $ids)->toArray();
  }

  public function remove(int $id): bool {
    return Expense::destroy($id);
  }

  public function expenseNotification(array $expenseNot): array | HttpResponseException {
    DB::beginTransaction();
    try {

      if (isset($expenseNot['owner_expense']) && !empty($expenseNot['owner_expense'])) {
        // atualiza recebimento de notificação da conta
        $expense = $this->find($expenseNot['owner_expense']['expense']);
        $expense->receiveNotification = $expenseNot['owner_expense']['notification'];
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
}
