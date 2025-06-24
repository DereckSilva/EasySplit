<?php

namespace App\Http\Controllers\Api;

use App\Repository\NotificationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController
{
    protected $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository) {
        $this->notificationRepository = $notificationRepository;
    }

    public function read(string $id): JsonResponse {
        $notification = $this->notificationRepository->readNotification($id);
        return response()->json($notification, $notification['statusCode']);
    }

}
