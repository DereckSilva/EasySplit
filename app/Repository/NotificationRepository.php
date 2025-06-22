<?php

namespace App\Repository;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDOException;

class NotificationRepository {

  protected $model = 'notifications';

  public function readNotification (string $id): array  {
    DB::beginTransaction();
    try {

      // atualização da notificação lida
      $currentDate = Carbon::now()->format('Y-m-d H:i:s');
      $notification = Notification::find($id)->select(['id', 'data', 'read_at'])->first();
      $notification->read_at = $currentDate;
      $notification->save();

      DB::commit();
      return [
        'status'     => true,
        'data'       => $notification->toArray(),
        'message'    => 'Notificação lida com sucesso',
        'statusCode' => 200
      ];
    } catch (PDOException $exception) {
      DB::rollBack();
      return [
        'status'     => false,
        'data'       => [],
        'message'    => $exception->getMessage(),
        'statusCode' => 400
      ];
    }
  }

  public function findNotifications(int $userId): array {
    return Notification::where('data->user_id', '=', $userId)
      ->whereNull('read_at')
      ->get(['data'])->toArray();
  }

}
