<?php

namespace App\Repository\Interfaces;

interface LogInterfaceRepository
{

    public function gravaLog(int $user, string $description): void;

}
