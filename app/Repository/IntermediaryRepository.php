<?php

namespace App\Repository;

use App\Models\Intermediary;
use App\Repository\Interfaces\IntermediaryInterfaceRepository;
use Illuminate\Support\Facades\DB;

class IntermediaryRepository implements IntermediaryInterfaceRepository
{
    protected string $model = 'Intermediary';

    public function create(array $data): array | bool
    {
        DB::beginTransaction();
        try {
            $intermediary = Intermediary::create($data);
            $intermediary->save();
            DB::commit();
            return $intermediary->toArray();
        } catch (\PDOException $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function find(string $column, string | int $value): array
    {
        $intermediary = Intermediary::where($column, $value)->first()->toArray();
        return empty($intermediary) ? [] : $intermediary;
    }

    public function all(): array
    {
        $allIntermediaries = [];
        Intermediary::chunk(100, function ($intermediary) use (&$allIntermediaries) {
            $allIntermediaries = $intermediary->toArray();
        });
        return empty($allIntermediaries) ? [] : $allIntermediaries;
    }
}
