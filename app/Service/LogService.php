<?php

namespace App\Service;

use App\LogActions;
use App\Repository\Interfaces\LogInterfaceRepository;

class LogService
{

    protected string $oldValue = '';

    public function __construct(
        private LogInterfaceRepository $logInterfaceRepository
    ){}

    public function gravaLog(int $user, string $description, LogActions $action, string $oldValue = '', string $newValue = ''): void {
        $this->logInterfaceRepository->gravaLog($user, $description, $action, $oldValue, $newValue);
        $this->setOldValue('');
    }

    public function setOldValue(string $oldValue): void {
        $this->oldValue = $oldValue;
    }

    public function getOldValue(): string {
        return $this->oldValue;
    }

}
