<?php

namespace App\Repository;

use App\Models\Expense;
use App\Notifications\ExpenseNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDOException;

class ExpenseRepository {

  protected $model = 'Expenses';

  public function create(array $expense): array {
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

      // validar os intermediarys_id
      $intermediarios = [];
      $people         = count($expense['intermediarys_id']) + 1;
      if (!empty($expense['intermediarys_id']) && is_array($expense['intermediarys_id'])) {
        $expense['intermediarys_id'] = collect($expense['intermediarys_id'])->each(function ($identifier) use ($userRepository) {
          $intermediary = $userRepository->find($identifier['email'], 'email')->email;
          return !empty($intermediary);
        })
        // passa pelos intermediarios e acrescenta as informacoes de notificação e valor da conta
        ->map(function ($identifier) use ($expense, $people) {
          $identifier['totalAmount']  = (float)$expense['priceTotal'] / $people;
          $identifier['notification'] = $expense['receiveNotification'];
          return $identifier;
        })->toJson();

        // ajuste de intermediários que querem receber notificação
        $intermediarios = collect($expense['intermediarys_id'])->filter(function ($data, $key) {
          $dataInterm = json_decode($data);
          return $dataInterm[$key]->notification;
        })
        ->map(function ($data, $key) {
          $dataInterm = json_decode($data);
          return $dataInterm[$key]->email;
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
      return [
        'status'     => false,
        'data'       => [],
        'message'    => $exception->getMessage(),
        'statusCode' => 500
      ];
    }
  }

  public function find(int $id): Expense|null {
    $expense = Expense::find($id);
    return !empty($expense) ? $expense->first() : null;
  }

  public function findAll(array $ids = []): array {
    return Expense::findMany(!empty($ids) ?? $ids)->toArray();
  }

  public function remove(int $id): bool {
    return Expense::destroy($id);
  }

  public function expenseNotification(array $expenseNot): array {
    DB::beginTransaction();
    try {

      if (!empty($expenseNot['owner_expense'])) {
        // atualiza recebimento de notificação da conta
        $expense = $this->find($expenseNot['owner_expense']['expense']);
        $expense->receiveNotification = $expenseNot['owner_expense']['notification'];
        $expense->save();
      }

      if (isset($expenseNot['intermediary_expense']) && !empty($expenseNot['intermediary_expense'])) {
        // regra
      }

      DB::commit();
      return [];
    } catch (PDOException $exception) {
      DB::rollBack();
      return [
        'status'  => false,
        'message' => 'Erro: ' . $exception->getMessage(),
        'data'    => []
      ];
    }
  }
}
