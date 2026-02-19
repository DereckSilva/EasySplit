<?php

namespace App\Service;

use App\DTO\ExpenseDTO;
use App\DTO\IntermediaryDTO;
use App\DTO\UserDTO;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Repository\Interfaces\LogInterfaceRepository;
use App\Repository\Interfaces\UserInterfaceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpenseService extends BaseService
{

    public function __construct(
        private ExpenseInterfaceRepository $expenseInterfaceRepository,
        private UserInterfaceRepository    $userInterfaceRepository,
        private IntermediaryService $intermediaryService,
        private LogInterfaceRepository $logInterfaceRepository
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
        $expense = $this->expenseInterfaceRepository->create($expense);
        return $this->afterCreate($expense);
    }

    public function updateExpense(ExpenseDTO $expense): array {
        $expense->paymentDate = Carbon::parse($expense->paymentDate)->toDateString();
        $expense->maturity    = Carbon::parse($expense->maturity)->toDateString();

        $expense = $this->beforeUpdate($expense->toArray());
        $expense = $this->expenseInterfaceRepository->update($expense['id'], $expense);
        return $this->afterUpdate($expense);
    }

    public function findExpense(int $id): array {
        $expense = $this->expenseInterfaceRepository->find($id);
        return empty($expense) ? [] : $this->afterFind($expense);
    }

    public function findAll($intermediary = false): array {
        return collect($this->expenseInterfaceRepository->all(Auth::user()->id, $intermediary))->map(function ($expense) {
            return $this->formatResponse($expense);
        })->toArray();
    }

    public function delete(int $id): bool {
        $removeExp = $this->expenseInterfaceRepository->delete($id);
        if (!$removeExp) {
            return false;
        }
        $this->logInterfaceRepository->gravaLog(Auth::user()->id, "Conta removida com sucesso pelo usuário " . Auth::user()->name);
        return true;
    }

    public function createIntermediaryFromExpense(IntermediaryDTO $intermediaryDTO): array {
        return $this->intermediaryService->createIntermediary($intermediaryDTO->toArray());
    }

    public function expenseNotification(array $data): bool {

        $owner         = isset($data['owner']) ? $data['owner'] : [];
        $intermediary  = isset($data['intermediary']) ? $data['intermediary'] : [];

        $this->updateNotificationOwnerIntermediary($intermediary, true);
        $this->updateNotificationOwnerIntermediary($owner);
        return true;
    }

    public function formatResponse(array $data): array
    {
        $expense        = new ExpenseDTO($data);
        $intermediaries = json_decode($expense->intermediaries, true);
        $user           = $this->userInterfaceRepository->find($expense->payerId, 'id');
        $payer          = new UserDTO($user['name'], $user['email'], '', $user['birthdate'], $user['phone_number']);

        return array_merge($expense->toResponse(), [
            'payer'          => $payer->toResponse($user['id'], $user['created_at'], $user['updated_at']),
            'intermediaries' => $intermediaries
        ]);
    }

    public function beforeCreate(array $data): array
    {
        return $data;
    }

    public function afterCreate(array $data): array
    {
        $this->logInterfaceRepository->gravaLog(Auth::user()->id, "Conta criada com sucesso para o usuário " . Auth::user()->name);
        return $this->formatResponse($data);
    }

    public function beforeUpdate(array $data): array
    {
        return $data;
    }

    public function afterUpdate(array $data): array
    {
        $this->logInterfaceRepository->gravaLog(Auth::user()->id, "Conta atualizada com sucesso para o usuário " . Auth::user()->name);
        return $this->formatResponse($data);
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
        return $this->formatResponse($data);
    }

    private function updateNotificationOwnerIntermediary(array $data, $intermediary = false): void {

        if (empty($data)) {
            return;
        }

        $dataUpdate       = !$intermediary ? ['receive_notification' => $data['notification']] : ['data->notification' => $data['notification']];
        $columnIdentifier = !$intermediary ? 'id' : 'data->id';
        $this->expenseInterfaceRepository->updateAllRegistersFromUser($columnIdentifier, Auth::user()->id, $dataUpdate);

    }
}
