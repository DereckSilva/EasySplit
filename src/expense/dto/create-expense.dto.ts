import { PartialType } from '@nestjs/mapped-types';
import {
  IsIn,
  IsNotEmpty,
  IsNumber,
  IsString,
  MinLength,
} from 'class-validator';
import { CreateUserDto } from 'src/user/dto/create-user.dto';

export class CreateExpenseDto extends PartialType(CreateUserDto) {
  @IsNotEmpty({ message: 'O nome é obrigatório' })
  @IsString({ message: 'O nome da conta precisa ser uma string' })
  @MinLength(10, {
    message: 'A descrição da conta precisa ter no mínimo 10 caracteres',
  })
  description: string;

  @IsNotEmpty({ message: 'O preço é obrigatório' })
  @IsNumber()
  price: number;

  @IsNotEmpty({ message: 'O número de parcelas é obrigatório' })
  @IsNumber()
  parcels: number;

  @IsNotEmpty({ message: 'A data de pagamento é obrigatória' })
  datePayment: Date;

  @IsNotEmpty({ message: 'O intermediário é obrigatório' })
  @IsIn([true, false], {
    message: 'Informe se a conta possui ou não intermediário',
  })
  intermediary: boolean;

  @IsNotEmpty({ message: 'O ID do pagador não pode ser vazio' })
  @IsString({ message: 'Informe o ID do pagador' })
  payeeId: string;

  user: { CreateUserDto: CreateUserDto };

  intermediaryIds: [string];
  maturityPayment: Date;
  slug: string;
}
