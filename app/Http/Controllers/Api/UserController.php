<?php

namespace App\Http\Controllers\Api;

use App\DTO\UserDTO;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdatedRequest;
use App\Jobs\EnviaEmail;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function create (UserRequest $request): JsonResponse {
        $user      = $request->all();
        $birthdate = Date::parse($user['birthdate']);
        $userDTO   = new UserDTO($user['name'], $user['email'], $user['password'], $birthdate, $user['phone_number']);
        $user      = $this->userService->createUser($userDTO);

        //EnviaEmail::dispatchSync($user['name'], $user['email']);

        return response()->json([
            'status' => true,
            'message' => 'Usuário criado com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])],
        Response::HTTP_CREATED);
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
        $user = !empty($user['id']) ? $this->userService->findById($user['id']) : $this->userService->findByEmail($user['email']);
        $userDTO = new UserDTO($user['name'], $user['email'], $userPasswordRequest->only('password'), $user['birthdate'], $user['phone_number']);
        $user    = $this->userService->updatePassword(array_merge($user, $userDTO->password));

        if (empty($user)) {
            return response()->json([
                'status'     => false,
                'message'    => 'Houve um erro ao tentar atualizar a senha do usuário',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        //EnviaEmail::dispatchSync($user['data']['name'], $user['data']['email'], true);


        return response()->json([
            'status' => true,
            'message' => 'A senha do usuário foi atualizada com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])]
        );
    }

    public function delete(UserDeleteRequest $deleteRequest): JsonResponse {
        $user = $deleteRequest->only('id', 'email');
        $user = !empty($user['id']) ? $this->userService->findById($user['id']) : $this->userService->findByEmail($user['email']);
        $this->userService->delete($user['id']);

        return response()->json([
            'status'  => true,
            'message' => 'Usuário excluído com sucesso!',
            'data'    => []
        ], Response::HTTP_NO_CONTENT);
    }

    public function show(int $id): JsonResponse {
        $user = $this->userService->findById($id);

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Usuário não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $userDTO = new UserDTO($user['name'], $user['email'], '', $user['birthdate'], $user['phone_number']);
        return response()->json([
            'status' => true,
            'message' => 'Usuário encontrado com sucesso!',
            'data' => $userDTO->toResponse($user['id'], $user['updated_at'], $user['created_at'])
        ], Response::HTTP_FOUND);
    }

    public function updatePhoneNumber(): JsonResponse {
        return response()->json();
    }
}
