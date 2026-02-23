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
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseController extends Controller
{

    use ImportCSV, ResponseHttp;

    public function __construct(
        protected ExpenseService $expenseService,
        protected IntermediaryService $intermediaryService
    ){}

    public function create(ExpenseRequest $expense): JsonResponse {
        $expense    = $this->validatedData($expense->all());
        $expenseDto = new ExpenseDTO($expense);

        $expense = $this->expenseService->createExpense($expenseDto);
        return response()->json([
            'status' => true,
            'message' => 'Conta criada com sucesso',
            'data' => $expense
        ], ResponseAlias::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse {
        $expense = $this->expenseService->findExpense($id);

        if (empty($expense)) {
            return response()->json([
                'status' => false,
                'message' => 'Conta não encontrada',
                'data' => []
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => true,
            'message' => 'Conta encontrada',
            'data' => $expense
        ], ResponseAlias::HTTP_FOUND);
    }

    public function expenseNotification(ExpenseNotificationRequest $expenseRequest): JsonResponse {
        $expenseNot = $expenseRequest->all();

        if (empty($expenseNot)) {
            return response()->json([], ResponseAlias::HTTP_NO_CONTENT);
        }

        $expense = $this->expenseService->expenseNotification($expenseNot);

        if (!$expense) {
            return response()->json([
                'status' => false,
                'message' => 'Houve um problema no processamento de atualização das notificações dos owners e/ou intermediários'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Todas as contas dos owners e/ou intermediários tiveram as notificações atualizadas com sucesso',
            'data' => []
        ]);
    }

    public function update(ExpenseRequestUpdate $expenseRequestUpdate): JsonResponse {
        $expense    = $this->validatedData($expenseRequestUpdate->all());
        $expenseDto = new ExpenseDTO($expense);
        $expense    = $this->expenseService->updateExpense($expenseDto);
        return response()->json([
            'status' => true,
            'message' => 'Conta atualizada com sucesso',
            'data' => $expense
        ]);
    }

    public function remove (int $id): JsonResponse {

        $findExpense = $this->expenseService->findExpense($id);

        if (empty($findExpense)) {
            return response()->json([
                'status' => false,
                'message' => 'Nenhuma conta encontrada com o id informado'
            ], ResponseAlias::HTTP_NOT_FOUND);
        }

        $expense = $this->expenseService->delete($id);
        if (!$expense) {
            return response()->json([
                'status' => false,
                'message' => 'Houve um erro ao tentar excluir a conta'
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Conta excluída com sucesso',
            'data'    => []
        ], ResponseAlias::HTTP_NO_CONTENT);
    }

    public function allOwner(): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => 'Lista de contas',
            'data' => $this->expenseService->findAllExpenseFromOwner()
        ], ResponseAlias::HTTP_OK);
    }

    public function allIntermediary(): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => 'Lista de contas dos intermediarios',
            'data' => $this->expenseService->findAllExpenseFromIntermediary()
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

        // valida os intermediários presentes -> REFATORAR
        if (empty($expense['intermediaries'])) {
            return $this->formatExpense($expense);
        }
        collect($expense['intermediaries'])->each(function ($intermediary) {
            $identifierIntermediary = array_keys($intermediary);

            if (count($identifierIntermediary) == 1 && in_array(end($identifierIntermediary), ['id', 'email'])) {
                $fieldIdentifier = end($identifierIntermediary);
                $intermediaryFNF = $this->intermediaryService->findIntermediary($fieldIdentifier, $intermediary[$fieldIdentifier]);

                if (empty($intermediaryFNF)) {
                    $this->returnExceptionErrorRequest(false,
                        "O {$fieldIdentifier} do intermediário informado ({$intermediary[$fieldIdentifier]}) não existe. Por favor, informe o email e telefone para cadastro.",
                        404, []);
                }
            }

            if (count($identifierIntermediary) == 2) {
                $intermediaryFNF = $this->intermediaryService->findIntermediary('email', $intermediary['email']);

                if (!empty($intermediaryFNF)) {
                    $this->returnExceptionErrorRequest(false,
                        "O email do intermediário informado ({$intermediary['email']}) já foi cadastrado. Por favor, informe apenas o id ou email para cadastro da conta.",
                        404, []);
                }
            }

        });

        return $this->formatExpense($expense);
    }

    private function formatExpense(array $expense): array {
        $expenseValidated = [
            'description'          => $expense['description'],
            'price_total'          => $expense['price_total'],
            'parcels'              => $expense['parcels'],
            'payer_id'             => $expense['payer_id'],
            'payment_date'         => $expense['payment_date'],
            'intermediary'         => $expense['intermediary'],
            'intermediaries'       => $expense['intermediaries'],
            'receive_notification' => $expense['receive_notification']
        ];
        return key_exists('id', $expense) ? array_merge(['id' => $expense['id']], $expenseValidated) : $expenseValidated;
    }
}
