<?php

namespace App\Service;

use App\Repository\Interfaces\NotificationInterfaceRepository;

class NotificationService
{
    public function __construct(
        private NotificationInterfaceRepository $notificationInterfaceRepository
    ){}

    public function readNotification(int $id): array {
        return $this->notificationInterfaceRepository->readNotification($id);
    }

    public function findNotificationFromUser(int $userId): array {
        return $this->notificationInterfaceRepository->findNotificationFromUser($userId);
    }

    public function findNotification(int $id): array {
        return $this->notificationInterfaceRepository->findNotification($id);
    }
}
