<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Trait\ResponseHttp;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    use ResponseHttp;

    public function auth(LoginRequest $request): JsonResponse | HttpResponseException {

        $user = $request->only('email', 'password');
        if (!Auth::attempt($user)) {
            $this->retornoExceptionErroRequest(false, 'Usuário não autorizado', 403, []);
        }
        $token = $request->user()->createToken('Token Usuario')->plainTextToken;

        return response()->json([
            'success'    => true,
            'token'      => $token,
            'message'    => 'Usuário autenticado com sucesso'
        ]);
    }

}
