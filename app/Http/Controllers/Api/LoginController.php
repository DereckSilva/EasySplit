<?php

namespace App\Http\Controllers\Api;

use App\Trait\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    use Request;

    public function auth(HttpRequest $request): JsonResponse | HttpResponseException {

        // verifica se está autenticado dentro do sistema
        if (!in_array($request->url(), [url('/api/register/new-password')])) {
            if (!Auth::attempt($request->only('email', 'password'))) {
                $this->retornoExceptionErroRequest(false, 'Usuário não autorizado', 403, []);
            }
        } else {
            $user             = $request->only('email', 'current_password');
            $user['password'] = $user['current_password'];
            unset($user['current_password']);
            if (!Auth::attempt($user)) {
                $this->retornoExceptionErroRequest(false, 'Usuário não autorizado', 403, []);
            }
        }
        $token = $request->user()->createToken('Token Usuario')->plainTextToken;

        return response()->json([
            'success'    => true,
            'token'      => $token,
            'statusCode' => 200,
            'message'    => 'Usuário autenticado com sucesso'
        ]);
    }

}
