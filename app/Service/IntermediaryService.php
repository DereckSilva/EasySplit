<?php

namespace App\Service;

use App\Repository\IntermediaryRepository;

class IntermediaryService
{

    public function __construct(
        private IntermediaryRepository $intermediaryRepository
    ){}

    public function createIntermediary(array $intermediary) {

        dd($intermediary);
        return $this->intermediaryRepository->create($intermediary);
    }

    public function findIntermediary(string $column, string | int $value) {
        return $this->intermediaryRepository->find($column, $value);
    }

}
