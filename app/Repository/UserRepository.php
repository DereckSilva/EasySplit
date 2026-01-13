<?php

namespace App\Repository;

use App\DTO\UserDTO;
use App\Models\User;
use App\Repository\Interfaces\UserInterfaceRepository;
use App\Trait\ResponseHttp;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use PDOException;

class UserRepository implements UserInterfaceRepository {

  use ResponseHttp;

  protected $model = 'User';

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

  public function updatePassword(array $userPassword): array | HttpResponseException {

    DB::beginTransaction();

    try {

      $password       = bcrypt($userPassword['password']);
      $user           = User::where('email', '=', $userPassword['email'])->first();
      $user->password = $password;
      $user->save();

      DB::commit();
      return [
        'status'     => true,
        'message'    => 'Senha atualizada com sucesso',
        'statusCode' => 200,
        'data'       => $user->toArray()
      ];
    } catch (PDOException $exception) {
      DB::rollBack();
      return $this->retornoExceptionErroRequest(false, 'Houve um erro ao atualizar a senha do usuário: ' . $exception->getMessage(), 400, []);
    }

  }

  public function find(int|string $identifier, string $column = ''): array {
    return !empty($column)
      ? User::where($column, '=', $identifier)->first()->toArray()
      : User::find($identifier)->toArray();
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
