<?php

namespace App\Http\Controllers\Api;

use App\DTO\UserDTO;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdatedRequest;
use App\Jobs\EnviaEmail;
use App\Repository\Interfaces\LogInterfaceRepository;
use App\Repository\LogRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected LogInterfaceRepository $logInterfaceRepository
    ) {}

    public function create (UserRequest $request): JsonResponse {
        $user      = $request->all();
        $birthdate = Date::parse($user['birthdate']);
        $userDTO   = new UserDTO($user['name'], $user['email'], $user['password'], $birthdate, $user['phone_number']);
        $user      = $this->userService->createUser($userDTO);

        //EnviaEmail::dispatchSync($user['name'], $user['email']);
        $this->logInterfaceRepository->gravaLog($user['id'], "Usuário Email: {$user['email']} e Nome: {$user['name']} criado com sucesso!");

        return response()->json([
            'status' => true,
            'message' => 'Usuário criado com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])],
        201);
    }

    public function updated(UserUpdatedRequest $request): JsonResponse {

        $user    = $request->only('id', 'name', 'email', 'phone_number', 'birthdate');
        $user    = $this->userService->updateUser($user['id'], $user);
        $userDTO = new UserDTO($user['name'], $user['email'], '', $user['birthdate'], $user['phone_number']);
        return response()->json([
            'status' => true,
            'message' => 'Usuário atulizado com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])]);
    }

    public function updatePassword (UserPasswordRequest $userPasswordRequest): JsonResponse {

        $user = $userPasswordRequest->only('id', 'email', 'password');
        $user = isset($user['id']) && !empty($user['id']) ? $this->userService->findById($user['id']) : $this->userService->findByEmail($user['email']);
        $userDTO = new UserDTO($user['name'], $user['email'], $userPasswordRequest->only('password'), $user['birthdate'], $user['phone_number']);
        $user    = $this->userService->updatePassword(array_merge($user, $userDTO->password));

        if (empty($user)) {
            return response()->json([
                'status'     => false,
                'message'    => 'Houve um erro ao tentar atualizar a senha do usuário',
            ], 400);
        }

        //EnviaEmail::dispatchSync($user['data']['name'], $user['data']['email'], true);

        $this->logInterfaceRepository->gravaLog($user['id'], "Usuário {$user['email']} teve a sua senha alterada com sucesso!");

        return response()->json([
            'status' => true,
            'message' => 'A senha do usuário foi atualizada com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])]
        );
    }

    public function show(int $id): JsonResponse {
        $user = $this->userService->findById($id);
        $userDTO = new UserDTO($user['name'], $user['email'], '', $user['birthdate'], $user['phone_number']);
        return response()->json([
            'status' => true,
            'message' => 'Usuário encontrado com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])
        ]);
    }

    public function updatePhoneNumber(): JsonResponse {
        return response()->json();
    }
}
