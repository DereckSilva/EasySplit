<?php

namespace App\Http\Controllers\Web;

use App\Repository\UserRepository;
use Illuminate\Contracts\View\View;

class RegisterController {

  protected $userRepository;

  public function __construct(UserRepository $userRepository) {
    $this->userRepository = $userRepository;
  }

  public function create(): View {
    
    return view(
      'register.user',
      ['message' => 'Usuário criado com sucesso']
    );
    
  }

  public function updatePassword(): View {
    
    return view(
      'register.password',
      ['message' => 'Senha atualizada com sucesso']
    );
    
  }
}
