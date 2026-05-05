<?php

namespace App\Service;

use App\LogActions;
use App\Repository\Interfaces\IntermediaryInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Trait\ResponseHttp;


class IntermediaryService
{

    use ResponseHttp;

    public function __construct(
        private IntermediaryInterfaceRepository $intermediaryInterfaceRepository,
        private LogService $logService
    ){}

    public function createIntermediary(array $intermediary): array | HttpResponseException {
        $intermediary = $this->intermediaryInterfaceRepository->create($intermediary);
        if (!is_array($intermediary)) {
            return $this->returnExceptionErrorRequest(false, 'Houve um erro ao criar o intermediário', 404, []);
        }
        $this->logService->gravaLog(Auth::user()->id, 'Intermediário criado com sucesso.', LogActions::CREATE, '', json_encode($intermediary));
        return $intermediary;
    }

    public function findIntermediary(string $column, string | int $value): array | HttpResponseException {
        $intermediary = $this->intermediaryInterfaceRepository->find($column, $value);

        if (empty($intermediary)) {
            return $this->returnExceptionErrorRequest(false, 'O intermediário não existe. Necessário realizar o cadastro.', 404, []);
        }

        return $intermediary;
    }

}
