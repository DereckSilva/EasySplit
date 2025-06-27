<?php

namespace App\Repository;

use App\Models\User;
use App\Trait\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use PDOException;

class UserRepository {

  use Request;

  protected $model = 'User';

  public function create(array $user): array | HttpResponseException {

    DB::beginTransaction();
    try {
      $userFound = User::where('email', '=', $user['email'])
        ->get()
        ->toArray();
  
      if (!empty($userFound)) {
        return $this->retornoExceptionErroRequest(false, 'O e-mail informado já está cadastrado.', 422, []);
      }

      $user['password'] = bcrypt($user['password']);
      $user = User::create($user);
      $user->save();

      DB::commit();
      return [
        'status'     => true,
        'message'    => 'Usuário cadastrado com sucesso',
        'statusCode' => 200,
        'data' => $user->toArray()
      ];
    } catch (PDOException $exception) {
      DB::rollBack();
      return $this->retornoExceptionErroRequest(false, 'Houve um erro ao criar o usuário: ' . $exception->getMessage(), 400, []);
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

  public function find(int|string $identifier, string $collumn = ''): array | User | HttpResponseException {
    $user = !empty($collumn)
      ? User::where($collumn, '=', $identifier)->first()
      : User::find($identifier);
    
    if (empty($user)) {
      return $this->retornoExceptionErroRequest(false, 'O proprietário da conta ou o intermediário não foi encontrado.', 300, []);
    }
    $user = !empty($collumn) ? $user : $user->toArray();
    
    if (!empty($collumn) && $user instanceof User) {
      return $user;
    }
    $notificationRepository  = app('App\Repository\NotificationRepository');
    $user['notifications']   = $notificationRepository->findNotifications($identifier);
    return [
      'status'     => true,
      'data'       => $user,
      'message'    => 'Usuário encotrado com sucesso',
      'statusCode' => 200
    ];
  }

  public function findAll(): array {
    return User::all()->toArray();
  }

}
