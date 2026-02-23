<?php

namespace App\Service;

use App\LogActions;
use App\Repository\Interfaces\IntermediaryInterfaceRepository;
use Illuminate\Support\Facades\Auth;

class IntermediaryService
{

    public function __construct(
        private IntermediaryInterfaceRepository $intermediaryInterfaceRepository,
        private LogService $logService
    ){}

    public function createIntermediary(array $intermediary): array {
        $intermediary = $this->intermediaryInterfaceRepository->create($intermediary);
        if (!is_array($intermediary)) {
            return [];
        }
        $this->logService->gravaLog(Auth::user()->id, 'Intermediário criado com sucesso.', LogActions::CREATE, '', json_encode($intermediary));
        return $intermediary;
    }

    public function findIntermediary(string $column, string | int $value): array {
        return $this->intermediaryInterfaceRepository->find($column, $value);
    }

}
