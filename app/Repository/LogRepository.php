<?php

namespace App\Repository;

use App\Models\Log;
use DateTime;
use Illuminate\Support\Facades\DB;
use PDOException;

class LogRepository {

  protected $model = 'Logs';

  public function gravaLog(int $user, string $description): void {
    DB::beginTransaction();
    try {
      $log = Log::create(['user_id' => $user, 'description' => $description]);
      $log->save();
      DB::commit();
    } catch (PDOException $exception) {
      DB::rollBack();
    }
  }

}
