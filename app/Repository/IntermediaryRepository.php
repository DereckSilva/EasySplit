<?php

namespace App\Repository;

use App\Models\Intermediary;
use App\Repository\Interfaces\IntermediaryInterfaceRepository;
use Illuminate\Support\Facades\DB;

class IntermediaryRepository implements IntermediaryInterfaceRepository
{
    protected string $model = 'Intermediary';

    public function all(): array
    {
        $intermediaries = Intermediary::all();
        return empty($intermediaries) ? [] : $intermediaries->toArray();
    }

    public function find(string $column, string | int $value): array
    {
        $intermediary = Intermediary::where($column, $value)->get()->toArray();
        return empty($intermediary) ? [] : $intermediary[0];
    }

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
}
