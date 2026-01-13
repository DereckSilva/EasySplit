<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseNotificationRequest;
use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\ExpenseRequestUpdate;
use App\Http\Requests\ImportExpenseRequest;
use App\Repository\ExpenseRepository;
use App\Service\ExpenseService;
use App\Trait\ImportCSV;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{

    use ImportCSV;

    public function __construct(
        protected ExpenseService $expenseService
    ){}

    public function create(ExpenseRequest $expenseRequest): JsonResponse {
        $expense = $expenseRequest->all();

        //valida data de pagamento
        $payment_date = $expense['payment_date'];
        $currentDate  = Carbon::now();
        if (strtotime($currentDate) > $payment_date) {
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

        Gate::authorize('view', $expense);

        return response()->json(!empty($expense) ? $expense->toArray() : []);
    }

    public function expenseNotification(ExpenseNotificationRequest $expenseRequest): JsonResponse {
        $expenseNot = $expenseRequest->all();
        $expense = $this->expenseRepository->expenseNotification($expenseNot);
        return response()->json($expense);
    }

    public function update(ExpenseRequestUpdate $expenseRequestUpdate): JsonResponse {

        $expense = $expenseRequestUpdate->all();

        Gate::authorize('update', $expense);

        $expense = $this->expenseRepository->update($expense);
        return response()->json($expense);
    }

    public function remove (int $id): JsonResponse {

        $expense = $this->expenseRepository->find($id);

        Gate::authorize('delete', $expense);

        $this->expenseRepository->remove($id);
        return response()->json([
            'status'  => true,
            'message' => 'Conta excluída com sucesso',
            'data'    => []
        ]);
    }

    public function importExpenseFromCSV(ImportExpenseRequest $request): JsonResponse {

        $teste = $this->expenseService->createExpense();
        return response()->json(['message' => 'salve', 'data' => $teste], 201);
        // montar qual o cabeçalho para validação dos dados da despesa

        $this->import(array(), $request->file('expenseCSV')->getFilename(), $request->file('expenseCSV')->getContent(), ';', 26);

        //

        return response()->json(['message' => 'salve'], 201);
    }


    private function validatedRow(array $row): void {

    }

}
