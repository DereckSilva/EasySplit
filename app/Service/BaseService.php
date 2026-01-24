<?php

namespace App\Service;

abstract class BaseService
{
    abstract public function beforeCreate(array $data): array;

    abstract public function afterCreate(array $data): array;

    abstract public function beforeUpdate(array $data): array;

    abstract public function afterUpdate(array $data): array;

    abstract public function beforeDelete(array $data): array;

    abstract public function afterDelete(array $data): array;

    abstract public function afterFind(array $data): array;
}
