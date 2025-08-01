<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseNotificationRequest;
use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\ExpenseRequestUpdate;
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
        if (!isset($expense['intermediarys'])) {
            $expense['intermediarys'] = json_encode([]);
        }
        
        $expense = $this->expenseRepository->create($expense);
        return response()->json($expense, $expense['statusCode']);
    }

    public function show(int $id): JsonResponse {
        // revisar
        $expense = $this->expenseRepository->find($id);
        return response()->json(!empty($expense) ? $expense->toArray() : [], 200);
    }

    public function expenseNotification(ExpenseNotificationRequest $expenseRequest): JsonResponse {
        $expenseNot = $expenseRequest->all();
        $expense = $this->expenseRepository->expenseNotification($expenseNot);
        return response()->json($expense, 200);
    }

    public function update(ExpenseRequestUpdate $expenseRequestUpdate): JsonResponse {
        $expense = $expenseRequestUpdate->all();
        $expense = $this->expenseRepository->update($expense);
        return response()->json($expense, 200);
    }

    public function remove (int $id): JsonResponse {
        $this->expenseRepository->remove($id);
        return response()->json([
            'status'  => true,
            'message' => 'Conta excluída com sucesso',
            'data'    => []
        ], 200);
    }
}
