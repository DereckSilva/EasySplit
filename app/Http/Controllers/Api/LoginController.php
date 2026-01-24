<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Trait\ResponseHttp;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController
{

    use ResponseHttp;

    public function auth(LoginRequest $request): JsonResponse | HttpResponseException {

        $user = $request->only('email', 'password');
        if (!Auth::attempt($user)) {
            $this->retornoExceptionErroRequest(false, 'Usuário não autorizado', 403, []);
        }

        $datTime = Carbon::now();
        $token = $request->user()->createToken('Token Usuario', ['*'], $datTime->addDays())->plainTextToken;

        return response()->json([
            'success'    => true,
            'token'      => $token,
            'message'    => 'Usuário autenticado com sucesso'
        ]);
    }

    public function logout(Request $request): JsonResponse {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

}
