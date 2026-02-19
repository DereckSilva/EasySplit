<?php

namespace App\Http\Controllers\Api;

use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function read(string $id): JsonResponse {
        $notification = $this->notificationService->readNotification($id);
        return response()->json($notification, $notification['statusCode']);
    }

}
