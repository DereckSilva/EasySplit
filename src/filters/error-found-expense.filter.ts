import {
  ArgumentsHost,
  Catch,
  ExceptionFilter,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';
import { ErrorFoundExpense } from 'src/errors/errors';

@Catch(ErrorFoundExpense)
export class ErrorFoundExpenseFilter implements ExceptionFilter {
  catch(exception: ErrorFoundExpense, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();

    response.status(HttpStatus.NOT_FOUND).json({
      message: 'Nenhuma conta foi encontrada para o usuário!',
      statusCode: HttpStatus.NOT_FOUND,
    });
  }
}
