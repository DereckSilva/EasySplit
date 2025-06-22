<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseRequest;
use App\Repository\ExpenseRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{

    protected $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository) {
        $this->expenseRepository = $expenseRepository;
    }

    public function create(ExpenseRequest $expenseRequest): JsonResponse {
        $expense = $expenseRequest->all();

        //valida data de pagamento
        $datePayment = $expense['datePayment'];
        $currentDate = Carbon::now();
        if (strtotime($currentDate) > $datePayment) {
            return response()->json([
                'status' => false,
                'message' => 'Data de pagamento precisa ser igual ou maior que a data atual',
            ], 400);
        }

        // verifica campo de intermediarios
        if (!isset($expense['intermediarys_id'])) {
            $expense['intermediarys_id'] = json_encode([]);
        }
        
        $expense = $this->expenseRepository->create($expense);
        return response()->json($expense, $expense['statusCode']);
    }

    public function show(int $id): JsonResponse {
        $expense = $this->expenseRepository->find($id);
        return response()->json($expense, 200);
    }
}
