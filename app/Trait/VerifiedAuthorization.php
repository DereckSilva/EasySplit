<?php

namespace App\Trait;

use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

trait VerifiedAuthorization
{

    use ResponseHttp;

    public function verifiedAuth(string $ability, Model $model): bool {
        try {
            Gate::authorize($ability, $model);
        } catch (\Exception $e) {
            $this->retornoExceptionErroRequest(false, 'Este usuário não possui permissão para realizar essa ação.', Response::HTTP_UNAUTHORIZED, []);
        }
        return true;
    }

}
