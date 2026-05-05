<?php

namespace App\Service;

use App\LogActions;
use App\Repository\Interfaces\LogInterfaceRepository;
use App\Trait\ResponseHttp;

class LogService
{

    use ResponseHttp;

    protected string $oldValue = '';

    public function __construct(
        private LogInterfaceRepository $logInterfaceRepository
    ){}

    public function gravaLog(int $user, string $description, LogActions $action, string $oldValue = '', string $newValue = ''): void {
        $log = $this->logInterfaceRepository->gravaLog($user, $description, $action, $oldValue, $newValue);

        if (!is_array($log)) {
            $this->returnExceptionErrorRequest(false, 'Houve um erro ao gravar o log', 404, []);
        }

        $this->setOldValue('');
    }

    public function setOldValue(string $oldValue): void {
        $this->oldValue = $oldValue;
    }

    public function getOldValue(): string {
        return $this->oldValue;
    }

}
