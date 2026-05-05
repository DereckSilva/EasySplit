<?php

namespace App\Http\Controllers\Api;

use App\Service\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class NotificationController
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function read(string $id): JsonResponse {
        $notification = $this->notificationService->readNotification($id);
        return response()->json($notification, Response::HTTP_OK);
    }

}
