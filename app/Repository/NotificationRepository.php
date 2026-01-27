<?php

namespace App\Repository;

use App\Models\Notification;
use App\Repository\Interfaces\NotificationInterfaceRepository;
use App\Trait\ResponseHttp;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDOException;

class NotificationRepository implements NotificationInterfaceRepository {

  use ResponseHttp;

  protected $model = 'notifications';

  public function readNotification (int $id): array {
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
      return false;
    }
  }

  public function findNotificationFromUser(int $user): array {
    return Notification::where('data->user_id', '=', $user)
      ->whereNull('read_at')
      ->get(['data'])->toArray();
  }

  public function findNotification(int $id): array {
      $notifications = Notification::find($id);
      return empty($notifications) ? false : $notifications->toArray();
  }

}
