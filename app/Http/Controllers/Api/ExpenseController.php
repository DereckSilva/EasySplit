<?php

namespace App\Http\Controllers\Api;

use App\DTO\ExpenseDTO;
use App\Http\Requests\ExpenseNotificationRequest;
use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\ExpenseRequestUpdate;
use App\Http\Requests\ImportExpenseRequest;
use App\Service\ExpenseService;
use App\Service\IntermediaryService;
use App\Trait\ImportCSV;
use App\Trait\ResponseHttp;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseController extends Controller
{

    use ImportCSV, ResponseHttp;

    public function __construct(
        protected ExpenseService $expenseService,
        protected IntermediaryService $intermediaryService
    ){}

    public function create(ExpenseRequest $expense): JsonResponse {
        $expense = $this->validatedData($expense->all());

        $expenseDto = new ExpenseDTO([
            'description'          => $expense['description'],
            'price_total'          => $expense['price_total'],
            'parcels'              => $expense['parcels'],
            'payer_id'             => $expense['payer_id'],
            'payment_date'         => Carbon::parse($expense['payment_date']),
            'intermediary'         => $expense['intermediary'],
            'intermediaries'       => json_encode($expense['intermediaries']),
            'maturity'             => Carbon::now(),
            'receive_notification' => $expense['receive_notification']
        ]);

        $expense = $this->expenseService->createExpense($expenseDto);
        return response()->json([
            'status' => true,
            'message' => 'Conta criada com sucesso',
            'data' => $expense
        ], ResponseAlias::HTTP_CREATED);
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


    private function validatedData(array $expense): array | HttpResponseException {

        // valida os intermediários presentes
        if ($expense['intermediary'] && !empty($expense['intermediaries'])) {
            collect($expense['intermediaries'])->each(function ($intermediary) {
                $keys = array_keys($intermediary);
                if (count($keys) == 1 && (end($keys) == 'id' || end($keys) == 'email')) {
                    $field = end($keys);
                    $intermediaryFNF = $this->intermediaryService->findIntermediary($field, $intermediary[$field]);
                    if (empty($intermediaryFNF)) {
                        $this->retornoExceptionErroRequest(false,
                            "O {$field} do intermediário informado ({$intermediary[$field]}) não existe. Por favor, informe o email e telefone para cadastro.",
                            404, []);
                    }
                }

                if (count($keys) == 2) {
                    $intermediaryFNF = $this->intermediaryService->findIntermediary('email', $intermediary['email']);
                    if (!empty($intermediaryFNF)) {
                        $this->retornoExceptionErroRequest(false,
                            "O email do intermediário informado ({$intermediary['email']}) já foi cadastrado. Por favor, informe apenas o id ou email para cadastro da conta.",
                            404, []);
                    }
                }

            });
        }

        return $expense;
    }
}
