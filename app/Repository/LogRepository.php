<?php

namespace App\Repository;

use App\LogActions;
use App\Models\Log;
use App\Repository\Interfaces\LogInterfaceRepository;
use Illuminate\Support\Facades\DB;
use PDOException;

class LogRepository implements LogInterfaceRepository {

  protected $model = 'Logs';

  public function gravaLog(int $user, string $description, LogActions $action, string $oldValue = '', string $newValue = ''): void {
    DB::beginTransaction();
    try {
      $log = Log::create(['user_id' => $user,
        'description' => $description,
        'action'      => $action,
        'old_value'   => empty($oldValue) ? 'null' : $oldValue,
        'new_value'   => empty($newValue) ? 'null' : $newValue,
      ]);
      $log->save();
      DB::commit();
    } catch (PDOException $exception) {
      DB::rollBack();
    }
  }

}
