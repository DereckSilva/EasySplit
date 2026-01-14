<?php

namespace App\Repository;

use App\DTO\UserDTO;
use App\Models\User;
use App\Repository\Interfaces\UserInterfaceRepository;
use App\Trait\ResponseHttp;
use Illuminate\Support\Facades\DB;
use PDOException;

class UserRepository implements UserInterfaceRepository {

  use ResponseHttp;

  protected string $model = 'User';

    public function create(UserDTO $data): array | bool {

        DB::beginTransaction();
        try {
          $user = User::create($data->toArray());
          $user->save();

          DB::commit();
          return $user->toArray();
        } catch (PDOException $exception) {
          DB::rollBack();
          return false;
        }
    }

    public function updatePassword(array $data): array | bool {

        DB::beginTransaction();

        try {

          $password       = bcrypt($data['password']);
          $user           = User::where('email', '=', $data['email'])->first();
          $user->password = $password;
          $user->save();

          DB::commit();
          return $user->toArray();
        } catch (PDOException $exception) {
          DB::rollBack();
          return false;
        }

    }

    public function find(int|string $identifier, string $column = ''): array {
        return !empty($column)
          ? User::where($column, '=', $identifier)->first()->toArray()
          : User::find($identifier)->toArray();
    }

    public function findUserCustom(array $data): array {
        $user = User::where($data)->first();
        return !empty($user) ? $user->toArray() : [];
    }

    public function all(): array {
        return User::all()->toArray();
    }

    public function update($id, array $data): array | bool
    {
        DB::beginTransaction();
        try {
            User::where('id', $id)->update($data);
            $user = User::find($id)->toArray();

            DB::commit();
            return $user;
        } catch (PDOException $exception) {
            DB::rollBack();
            return false;
        }

    }

    public function delete(int $id): bool
    {
        return false;
    }
}
