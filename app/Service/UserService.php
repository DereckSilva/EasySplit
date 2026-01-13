<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Repository\Interfaces\UserInterfaceRepository;
use App\Repository\NotificationRepository;
use App\Trait\ResponseHttp;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserService
{

    use ResponseHttp;

    public function __construct(
        protected UserInterfaceRepository $userInterfaceRepository,
        protected NotificationRepository $notificationRepository
    ){}

    public function createUser(UserDTO $user): array | HttpResponseException {
        $user->password  = bcrypt($user->password);
        $user->birthDate = date('Y-m-d', strtotime($user->birthDate));
        $userCreate = $this->userInterfaceRepository->create($user);

        if (!is_array($userCreate)) {
            return $this->retornoExceptionErroRequest(false, 'Houve um erro ao criar o usuário: ', 400, []);
        }

        return $userCreate;
    }

    public function findUser(string $email): array | HttpResponseException {
        return $this->userInterfaceRepository->find('email', $email);
    }

    public function findById(int $id): array | HttpResponseException {
        return $this->userInterfaceRepository->find($id);
    }

    public function updateUser(int $id, array $user): array | HttpResponseException {

        // validar dados antes de realizar o update

        $userUp                  = $this->userInterfaceRepository->update($id, $user);
        $userUp['notifications'] = $this->notificationRepository->findNotifications($id);

        return $userUp;
    }

}
