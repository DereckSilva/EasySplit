<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class Controller {

    public function __construct() {
        $this->verifiedActionMethod(request());
    }

    public function verifiedActionMethod(Request $request) {
        $methods = ['create', 'update', 'delete', 'show'];

        $method = $request->route()->getActionMethod();
        $beforeMethod = 'before' . ucfirst($method);

        if (in_array($method, $methods)) {
            app()->call([$this, $beforeMethod]);
        }

        return app()->call([$this, $method]);
    }

    public function beforeCreate(Request $request): Request {
        if (method_exists($this, 'validatedData')) {
            return app()->call([$this, 'validatedData']);
        }
        return app()->call([$this, 'create']);
    }

    public function beforeUpdate(array $data): array {
        return app()->call([$this, 'update']);
    }

    public function beforeDelete(Request $request): Request {
        return app()->call([$this, 'delete']);
    }

    public function beforeShow(Request $request): Request {
        return app()->call([$this, 'show']);
    }

}
