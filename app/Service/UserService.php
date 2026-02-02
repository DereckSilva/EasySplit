<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Repository\Interfaces\LogInterfaceRepository;
use App\Repository\Interfaces\UserInterfaceRepository;
use App\Repository\NotificationRepository;
use App\Trait\ResponseHttp;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserService extends BaseService
{

    use ResponseHttp;

    public function __construct(
        protected UserInterfaceRepository $userInterfaceRepository,
        protected NotificationRepository $notificationRepository,
        protected LogInterfaceRepository $logInterfaceRepository
    ){}

    public function createUser(UserDTO $user): array | HttpResponseException {
        $user->password  = bcrypt($user->password);
        $user->birthDate = date('Y-m-d', strtotime($user->birthDate));
        $userCreate      = $this->userInterfaceRepository->create($user);

        if (!is_array($userCreate)) {
            return $this->retornoExceptionErroRequest(false, 'Houve um erro ao criar o usuário: ', 400, []);
        }

        $this->logInterfaceRepository->gravaLog($userCreate['id'], "Usuário Email: {$userCreate['email']} e Nome: {$userCreate['name']} criado com sucesso!");

        return $userCreate;
    }

    public function updateUser(int $id, array $user): array | HttpResponseException {

        $userUp                  = $this->userInterfaceRepository->update($id, $user);
        $userUp['notifications'] = $this->notificationRepository->findNotificationFromUser($id);

        $this->logInterfaceRepository->gravaLog($userUp['id'], "Usuário {$userUp['email']} foi atualizado!");

        return $userUp;
    }

    public function updatePassword(array $user): array | HttpResponseException {
        $user = $this->userInterfaceRepository->updatePassword(['email' => $user['email'], 'password' => $user['password']]);

        if (!is_array($user)) {
            return $this->retornoExceptionErroRequest(false, 'Houve um erro ao atualizar a senha do usuário', 404, []);
        }

        $this->logInterfaceRepository->gravaLog($user['id'], "Usuário {$user['email']} teve a sua senha alterada com sucesso!");

        return $user;
    }

    public function findByEmail(string $email): array | HttpResponseException {
        return $this->userInterfaceRepository->find($email, 'email');
    }

    public function findById(int $id): array | HttpResponseException {
        return $this->userInterfaceRepository->find($id);
    }

    public function delete(int $id): bool {
        $this->userInterfaceRepository->delete($id);
        return true;
    }

    public function beforeCreate(array $data): array
    {
        return $data;
    }

    public function afterCreate(array $data): array
    {
        return $data;
    }

    public function beforeUpdate(array $data): array
    {
        return $data;
    }

    public function afterUpdate(array $data): array
    {
        return $data;
    }

    public function beforeDelete(array $data): array
    {
        return $data;
    }

    public function afterDelete(array $data): array
    {
        return $data;
    }

    public function afterFind(array $data): array
    {
        return $data;
    }

    public function formatResponse(array $data): array
    {
        return [];
    }
}
