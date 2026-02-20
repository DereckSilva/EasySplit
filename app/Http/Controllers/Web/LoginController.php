<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Trait\ResponseHttp as RequestTrait;

class LoginController {

  use RequestTrait;

  public function login(Request $request) {

    $user = $request->only('email', 'password');

    if (!Auth::attempt($user)) {
      return $this->returnExceptionErrorRequest(false, 'Usuário não existe ou credencias inválidas', 404, []);
    }

    return redirect('/');
  }

  public function logout (Request $request): JsonResponse {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    return $request->response()->json([
        'status' => true,
        'message' => 'Logout realizado com sucesso',
        'statusCode' => 200,
    ]);
  }
}
