import {
  Body,
  Controller,
  Delete,
  Get,
  HttpStatus,
  Param,
  Patch,
  Post,
  UsePipes,
  ValidationPipe,
} from '@nestjs/common';
import {
  ApiBearerAuth,
  ApiOperation,
  ApiResponse,
  ApiTags,
} from '@nestjs/swagger';
import { ExpenseService } from 'src/expense/expense.service';
import { ExpenseModel } from './model/expense.model';
import { CreateExpenseDto } from 'src/expense/dto/create-expense.dto';
import { UpdateExpenseDto } from 'src/expense/dto/update-expense.dto';

@ApiBearerAuth()
@Controller('expense')
@ApiTags('Expense')
export class ExpenseController {
  constructor(private readonly expenseService: ExpenseService) {}

  @Post()
  @ApiOperation({ summary: 'Cria uma conta para o usuário.' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Conta encontrada com sucesso.',
    type: ExpenseModel,
  })
  @UsePipes(new ValidationPipe())
  async create(@Body() createExpense: CreateExpenseDto) {
    return createExpense + '';
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Atualia uma conta do usuário.' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Conta atualizada com sucesso',
    type: ExpenseModel,
  })
  @UsePipes(new ValidationPipe())
  async update(
    @Param('id') id: string,
    @Body() updateExpense: UpdateExpenseDto,
  ) {
    return id + updateExpense + '';
  }

  @Get('one/:id')
  @ApiOperation({ summary: 'Busca uma conta específica do usuário.' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Conta encontrada com sucesso.',
    type: '',
  })
  @UsePipes(new ValidationPipe())
  async findOne(@Param('id') id: string) {
    return id + '';
  }

  @Get('all')
  @ApiOperation({ summary: 'Busca todas as contas do usuário.' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Contas encontradas com sucesso.',
    type: '',
  })
  @UsePipes(new ValidationPipe())
  async findAll() {
    return await this.expenseService.findAll();
  }

  @Delete('remove/:id')
  @ApiOperation({ summary: 'Remove uma conta do usuário.' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Conta removida com sucesso.',
    example: [
      {
        status: HttpStatus.OK,
        message: 'Conta removida com sucesso',
        data: [],
      },
    ],
  })
  @UsePipes(new ValidationPipe())
  async remove(@Param('id') id: string) {
    return id + '';
  }
}
