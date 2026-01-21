<?php

namespace App\Http\Controllers\Api;

abstract class Controller {

    abstract public function beforeCreate(array $data): array;

}
