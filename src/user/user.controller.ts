import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  UsePipes,
  ValidationPipe,
  Sse,
  HttpStatus,
  UseGuards,
} from '@nestjs/common';
import { UserService } from './user.service';
import { CreateUserDto } from './dto/create-user.dto';
import { UpdateUserDto } from './dto/update-user.dto';
import { FindUserDto } from './dto/find-user.dto';
import { fromEvent, map, Observable } from 'rxjs';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { User } from './interfaces/user.interface';
import { UpdatePasswordDto } from './dto/update-password.dto';
import { AuthGuard } from 'src/auth/auth.guards';
import { ErrorFoundUser } from 'src/errors/errors';
import { Public } from 'src/decorator/is-public.decorator';

@UseGuards(AuthGuard)
@Controller('user')
export class UserController {
  constructor(
    private readonly userService: UserService,
    private readonly eventEmitter: EventEmitter2,
  ) {}

  @Post()
  @Public()
  @UsePipes(new ValidationPipe())
  async create(@Body() createUserDto: CreateUserDto) {
    const user = await this.userService.findOne(createUserDto.email);
    if (user.length > 0) {
      return [
        {
          message: 'Usuário já cadastrado',
          statusCode: HttpStatus.FOUND,
          data: {
            id: user[0]._id,
            name: user[0].name,
            email: user[0].email,
            slug: user[0].slug,
            recipients: user[0].recipients,
            role: user[0].role,
          },
        },
      ];
    }
    return {
      message: 'Usuário cadastrado com sucesso',
      statusCode: HttpStatus.CREATED,
      data: await this.userService.create(createUserDto),
    };
  }

  @Get('all')
  async findAll() {
    return await this.userService.findAll();
  }

  @Get('one')
  @UsePipes(new ValidationPipe())
  async findOne(@Body() user: FindUserDto) {
    const person = await this.userService.findOne(user.email);
    if (person.length === 0) {
      throw new ErrorFoundUser();
    }
    return [
      {
        message: 'Usuário encontrado',
        statusCode: HttpStatus.FOUND,
        user: {
          name: person[0].name,
          email: person[0].email,
          slug: person[0].slug,
        },
      },
    ];
  }

  @Patch()
  @UsePipes(new ValidationPipe())
  update(@Body() updateUserDto: UpdateUserDto) {
    return this.userService.update(updateUserDto.email, updateUserDto);
  }

  @Patch('update-password')
  @UsePipes(new ValidationPipe())
  async updatePassword(@Body() updatePass: UpdatePasswordDto) {
    const user = await this.userService.findOne(updatePass.email);
    if (user.length === 0) {
      throw new ErrorFoundUser();
    }
    await this.userService.updatePassword(user[0], updatePass);
    return [
      {
        message: 'Senha atualizada com sucesso',
        statusCode: HttpStatus.OK,
      },
    ];
  }

  @Delete('remove/:email')
  @UsePipes(new ValidationPipe())
  async remove(@Param('email') email: string) {
    const user = await this.userService.findOne(email);
    if (user.length === 0) {
      throw new ErrorFoundUser();
    }
    await this.userService.remove(user[0]);
    return [
      {
        message: 'Usuário excluído com sucesso',
        statusCode: HttpStatus.OK,
      },
    ];
  }

  @Sse('updated-user')
  updatedUser(): Observable<User> {
    return fromEvent(this.eventEmitter, 'user.updated').pipe(
      map((data) => data as User),
    );
  }

  @Sse('created-user')
  createdUser(): Observable<User> {
    console.log('usuario criado');
    return fromEvent(this.eventEmitter, 'user.created').pipe(
      map((data) => data as User),
    );
  }
}
