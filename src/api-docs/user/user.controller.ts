import {
  Body,
  Controller,
  Delete,
  Get,
  HttpStatus,
  Param,
  Patch,
  Post,
  UseGuards,
  UsePipes,
  ValidationPipe,
} from '@nestjs/common';
import {
  ApiBearerAuth,
  ApiOperation,
  ApiResponse,
  ApiTags,
} from '@nestjs/swagger';
import { UserService } from 'src/user/user.service';
import { CreateUserDto } from 'src/user/dto/create-user.dto';
import { UserModel } from './model/user.model';
import { UpdatePasswordDto } from 'src/user/dto/update-password.dto';
import { ErrorFoundUser } from 'src/errors/errors';

@ApiBearerAuth()
@ApiTags('User')
@Controller('user')
export class UserController {
  constructor(private readonly userService: UserService) {}

  @Post()
  @ApiOperation({ summary: 'Cria um usuário.' })
  @ApiResponse({
    status: HttpStatus.CREATED,
    description: 'Usuário criado com sucesso.',
    type: UserModel,
  })
  @UsePipes(new ValidationPipe())
  @UseGuards()
  async create(@Body() createUser: CreateUserDto) {
    return await this.userService.create(createUser);
  }

  @Get('one/:email')
  @ApiOperation({ summary: 'Busca um usuário específico.' })
  @ApiResponse({
    status: HttpStatus.FOUND,
    description: 'Usuário encontrado com sucesso.',
    type: UserModel,
  })
  @UsePipes(new ValidationPipe())
  async findOne(@Param('email') email: string) {
    return await this.userService.findOne(email);
  }

  @Get('all')
  @ApiOperation({ summary: 'Busca todos os usuários.' })
  @ApiResponse({
    status: HttpStatus.FOUND,
    description: 'Usuários encontrados com sucesso.',
    type: UserModel,
  })
  @UsePipes(new ValidationPipe())
  async findAll() {
    return await this.userService.findAll();
  }

  @Patch(':email')
  @ApiOperation({ summary: 'Atualiza os dados de um usuário.' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Dados atualizados com sucesso.',
    type: UserModel,
  })
  @ApiBearerAuth()
  @UsePipes(new ValidationPipe())
  async updateUser(
    @Param('email') email: string,
    @Body() updateUser: CreateUserDto,
  ) {
    return await this.userService.update(email, updateUser);
  }

  @Patch('update-password')
  @ApiOperation({ summary: 'Atualiza a senha do usuário' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Atualização de senha realizada com sucesso',
    example: [
      {
        status: HttpStatus.OK,
        data: [],
      },
    ],
  })
  @UsePipes(new ValidationPipe())
  async udatePassword(@Body() updatePassword: UpdatePasswordDto) {
    const user = await this.findOne(updatePassword.email);
    if (user.length === 0) {
      throw new ErrorFoundUser();
    }
    return await this.userService.updatePassword(user[0], updatePassword);
  }

  @Delete('remove/:id')
  @ApiOperation({ summary: 'Remove um usuário' })
  @ApiBearerAuth()
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Usuário removido com sucesso',
    example: [
      {
        status: HttpStatus.OK,
        message: 'Usuário removido com sucesso',
        data: [],
      },
    ],
  })
  async remove(@Param('id') id: string) {
    const user = await this.userService.findOne(id);
    if (user.length === 0) {
      throw new ErrorFoundUser();
    }
    return await this.userService.remove(user[0]);
  }
}
