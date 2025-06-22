<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Jobs\EnviaEmail;
use App\Repository\LogRepository;
use App\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;

class UserController extends Controller
{
    protected $userRepository;

    protected $logRepository;

    public function __construct(UserRepository $userRepository, LogRepository $logRepository) {
        $this->userRepository = $userRepository;
        $this->logRepository  = $logRepository;
    }

    public function create (UserRequest $request): JsonResponse {
        $user      = $request->all();
        $birthdate = Date::parse($user['birthdate']);

        if ((Date::now()->year - $birthdate->year) < 18) {
            return response()->json([
                'status'     => false,
                'message'    => 'O usuário precisa ser maior de idade para realizar um cadastro',
                'statusCode' => 400
            ], 400);
        }

        $user = $this->userRepository->create($user);
        if ((int)$user['statusCode'] === 200) {
            EnviaEmail::dispatchSync($user['data']['name'], $user['data']['email']);
        }

        $this->logRepository->gravaLog($user['data']['id'], "Usuário Email: {$user['data']['email']} e Nome: {$user['data']['name']} criado com sucesso!");

        return response()->json($user, $user['statusCode']);
    }

    public function updatePassword (UserPasswordRequest $userPasswordRequest): JsonResponse {

        $user = $userPasswordRequest->only('email', 'password', 'current_password');
        $user = $this->userRepository->updatePassword($user);
        
        if (empty($user)) {
            return response()->json([
                'status'     => false,
                'message'    => 'Houve um erro ao tentar atualizar a senha do usuário',
                'statusCode' => 400,
            ], 400);
        }

        if ((int)$user['statusCode'] === 200) {
            EnviaEmail::dispatchSync($user['data']['name'], $user['data']['email'], true);
        }

        $this->logRepository->gravaLog($user['data']['id'], "Usuário {$user['data']['email']} teve a sua senha alterada com sucesso!");

        return response()->json([
            $user
        ], $user['statusCode']);
    }
}
