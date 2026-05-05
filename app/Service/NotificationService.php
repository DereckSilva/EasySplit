<?php

namespace App\Service;

use App\Repository\Interfaces\NotificationInterfaceRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Trait\ResponseHttp;

class NotificationService
{

    use ResponseHttp;

    public function __construct(
        private NotificationInterfaceRepository $notificationInterfaceRepository
    ){}

    public function readNotification(int $id): array | HttpResponseException {
        $notification = $this->notificationInterfaceRepository->readNotification($id);

        if (!is_array($notification)) {
            return $this->returnExceptionErrorRequest(false, 'Houve um erro ao realizar a leitura da notificação', 404, []);
        }

        return $notification;
    }

    public function findNotificationFromUser(int $userId): array {
        return $this->notificationInterfaceRepository->findNotificationFromUser($userId);
    }

    public function findNotification(int $id): array {
        return $this->notificationInterfaceRepository->findNotification($id);
    }
}
