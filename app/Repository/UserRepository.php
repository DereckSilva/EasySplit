<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use PDOException;

class UserRepository {

  protected $model = 'User';

  public function create(array $user): array {

    DB::beginTransaction();
    try {
      $userFound = User::where('email', '=', $user['email'])
        ->get()
        ->toArray();
  
      if (!empty($userFound)) {
        return [
          'status'     => false,
          'message'    => 'O e-mail informado já está cadastrado',
          'statusCode' => 422
        ];
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

      return [
        'status'     => false,
        'message'    => 'Houve um erro ao criar o usuário ' . $exception->getMessage(),
        'statusCode' => 400
      ];
    }
  }

  public function updatePassword(array $userPassword): array {

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
      return [
        'status'     => false,
        'message'    => 'Houve um erro ao atualizar a senha do usuário ' . $exception->getMessage(),
        'statusCode' => 400
      ];
    }

  }

  public function find(int|string $identifier, string $collumn = ''): array | User {
    $user = !empty($collumn)
      ? User::where($collumn, '=', $identifier)
      : User::find($identifier);
    
    if (empty($user)) {
      return [
        'status'     => false,
        'data'       => [],
        'message'    => 'Nenhum usuário foi encontrado',
        'statusCode' => 300
      ];
    }
    $user = !empty($collumn) ? $user->first() : $user->toArray();
    
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
