<?php

namespace App\Repository\Interfaces;

use App\LogActions;

interface LogInterfaceRepository
{

    public function gravaLog(int $user, string $description, LogActions $action, string $oldValue = '', string $newValue = ''): void;

}
