<?php

namespace App\Repository\Interfaces;

interface NotificationInterfaceRepository
{
    public function findNotification(int $id): array | bool;

    public function findNotificationFromUser(int $user): array | bool;

    public function readNotification(int $id): array | bool;
}
