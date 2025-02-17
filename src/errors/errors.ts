export class ErrorInvalidPassword extends Error {
  constructor() {
    super('Senha inválida');
  }
}

export class ErrorInvalidOldPassword extends Error {
  constructor() {
    super('Senha atual inválida');
  }
}

export class ErrorFoundUser extends Error {
  constructor() {
    super('Houve um erro ao tentar encontrar um usuário');
  }
}

export class ErrorInsertUser extends Error {
  constructor() {
    super('Houve um erro ao realizar a inserção do usuário');
  }
}

export class ErrorUpdateUser extends Error {
  constructor() {
    super('Houve um erro ao realizar a atualização do usuário');
  }
}

export class ErrorRemoveUser extends Error {
  constructor() {
    super('Houve um erro ao remover um usuário');
  }
}
export class ErrorRemoveExpense extends Error {
  constructor() {
    super('Houve um erro ao remover uma conta');
  }
}

export class ErrorFoundExpense extends Error {
  constructor() {
    super('Nenhuma conta foi encontrada para o usuário');
  }
}

export class ErrorEmptyIntermediary extends Error {
  constructor() {
    super('Deve-se inserir o ID do terceiro da conta');
  }
}

export class ErrorRoleUser extends Error {
  USER: string;
  constructor(user: string) {
    super(
      `O usuário ${user} não tem função adequada para realizar atualização do usuário`,
    );
    this.USER = user;
  }
}
