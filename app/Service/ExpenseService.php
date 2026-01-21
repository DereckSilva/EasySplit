<?php

namespace App\Service;

use App\Repository\ExpenseRepository;
use App\Repository\Interfaces\ExpenseInterfaceRepository;
use App\Repository\Interfaces\UserInterfaceRepository;
use App\Repository\UserRepository;

class ExpenseService
{

    public function __construct(
        private ExpenseInterfaceRepository $expenseInterfaceRepository,
        private UserInterfaceRepository    $userInterfaceRepository
    ){}

    public function createExpense(array $expense) {



        return array('user' => $this->userInterfaceRepository->all(), 'expense' => $this->expenseInterfaceRepository->all());
    }

}
