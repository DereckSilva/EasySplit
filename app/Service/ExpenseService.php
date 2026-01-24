<?php

namespace App\Service;

use App\DTO\ExpenseDTO;
use App\DTO\IntermediaryDTO;
use App\DTO\UserDTO;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Repository\Interfaces\UserInterfaceRepository;
use Carbon\Carbon;

class ExpenseService extends BaseService
{

    public function __construct(
        private ExpenseInterfaceRepository $expenseInterfaceRepository,
        private UserInterfaceRepository    $userInterfaceRepository,
        private IntermediaryService $intermediaryService
    ){}

    public function createExpense(ExpenseDTO $expense): array {

        // seta a data de vencimento
        $paymentDate          = Carbon::parse($expense->paymentDate);
        $month                = $paymentDate->month + $expense->parcels;
        $expense->maturity    = Carbon::parse("{$paymentDate->year}-{$month}-{$paymentDate->day}")->toDateString();
        $expense->paymentDate = $paymentDate->toDateString();

        // intermediários
        $intermediaries = json_decode($expense->intermediaries, true);
        if ($expense->intermediary && !empty($intermediaries)) {
            collect($intermediaries)->each(function ($intermediary, $key) use ($expense, &$intermediaries) {
                $keys = array_keys($intermediary);
                $idIntermediary = count($keys) == 1
                    ? $this->intermediaryService->findIntermediary($keys[0], $intermediary[$keys[0]])['id']
                    : $this->createIntermediaryFromExpense(new IntermediaryDTO($intermediary['email'], $intermediary['phone_number']))['id'];


                $intermediaries[$key] = [
                    'id'           => $idIntermediary,
                    'notification' => $expense->receiveNotification,
                    'paid'         => false,
                    'totalAmount'  => number_format($expense->priceTotal / (count($intermediaries) + 1),2)
                ];
            });
        }

        $expense->intermediaries = json_encode($intermediaries);
        $expense = $this->beforeCreate($expense->toArray());
        $this->expenseInterfaceRepository->create($expense);
        return $this->afterCreate($expense);
    }

    public function createIntermediaryFromExpense(IntermediaryDTO $intermediaryDTO) {
        return $this->intermediaryService->createIntermediary($intermediaryDTO->toArray());
    }

    public function beforeCreate(array $data): array
    {
        return $data;
    }

    public function afterCreate(array $data): array
    {
        $expense        = new ExpenseDTO($data);
        $intermediaries = json_decode($expense->intermediaries, true);
        $user           = $this->userInterfaceRepository->find($expense->payerId, 'id');
        $payer          = new UserDTO($user['name'], $user['email'], '', $user['birthdate'], $user['phone_number']);

        return [
            'description'          => $expense->description,
            'price_total'          => $expense->priceTotal,
            'parcels'              => $expense->parcels,
            'payment_date'         => $expense->paymentDate,
            'intermediary'         => $expense->intermediary,
            'maturity'             => $expense->maturity,
            'receive_notification' => $expense->receiveNotification,

            'payer'          => $payer->toResponse($user['id'], $user['created_at'], $user['updated_at']),
            'intermediaries' => $intermediaries
        ];
    }

    public function beforeUpdate(array $data): array
    {
        return $data;
    }

    public function afterUpdate(array $data): array
    {
        return $data;
    }

    public function beforeDelete(array $data): array
    {
        return $data;
    }

    public function afterDelete(array $data): array
    {
        return $data;
    }

    public function afterFind(array $data): array
    {
        return $data;
    }
}
