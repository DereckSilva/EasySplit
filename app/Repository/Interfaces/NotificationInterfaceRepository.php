<?php

namespace App\Repository\Interfaces;

interface NotificationInterfaceRepository
{
    public function findNotification(int $id): array;

    public function findNotificationFromUser(int $user): array;

    public function readNotification(int $id): array | bool;
}
