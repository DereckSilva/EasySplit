import {
  ArgumentsHost,
  Catch,
  ExceptionFilter,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';
import { ErrorUserFields } from 'src/errors/errors';

@Catch(ErrorUserFields)
export class ErrorUserFieldsFilter implements ExceptionFilter {
  catch(exception: ErrorUserFields, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const field = exception.FIELD;

    response.status(HttpStatus.BAD_REQUEST).json({
      message: `O campo ${field} é obrigatório para criação do usuário`,
      statusCode: HttpStatus.BAD_REQUEST,
    });
  }
}
