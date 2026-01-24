<?php

namespace App\Service;

use App\Repository\Interfaces\IntermediaryInterfaceRepository;
use App\Repository\IntermediaryRepository;

class IntermediaryService
{

    public function __construct(
        private IntermediaryInterfaceRepository $intermediaryInterfaceRepository
    ){}

    public function createIntermediary(array $intermediary): array {
        $intermediary = $this->intermediaryInterfaceRepository->create($intermediary);
        if (!is_array($intermediary)) {
            return [];
        }
        return $intermediary;
    }

    public function findIntermediary(string $column, string | int $value): array {
        return $this->intermediaryInterfaceRepository->find($column, $value);
    }

}
