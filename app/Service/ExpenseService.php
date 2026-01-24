<?php

namespace App\Service;

use App\DTO\IntermediaryDTO;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Repository\Interfaces\UserInterfaceRepository;

class ExpenseService extends BaseService
{

    public function __construct(
        private ExpenseInterfaceRepository $expenseInterfaceRepository,
        private UserInterfaceRepository    $userInterfaceRepository,
        private IntermediaryService $intermediaryService
    ){}

    public function createExpense(array $expense) {
        return array('user' => $this->userInterfaceRepository->all(), 'expense' => $this->expenseInterfaceRepository->all());
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
        return $data;
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
