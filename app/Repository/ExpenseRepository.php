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
      if (!empty($expense['intermediarys_id']) && is_array($expense['intermediarys_id'])) {
        $expense['intermediarys_id'] = collect($expense['intermediarys_id'])->each(function ($identifier) use ($userRepository) {
          $teste = $userRepository->find($identifier['email'], 'email');
          return !empty($teste);
        })->toJson();

        $intermediarios = collect($expense['intermediarys_id'])->map(function ($email, $key) {
          $email = json_decode($email);
          return $email[$key]->email;
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
