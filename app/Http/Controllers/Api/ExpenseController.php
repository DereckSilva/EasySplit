<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseRequest;
use App\Repository\ExpenseRepository;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{

    protected $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository) {
        $this->expenseRepository = $expenseRepository;
    }

    public function create(ExpenseRequest $expenseRequest): JsonResponse {
        return response()->json([], 201);
    }

    public function show(int $id): JsonResponse {
        $expense = $this->expenseRepository->find($id);
        return response()->json([], 201);
    }
}
