import {
  ArgumentsHost,
  Catch,
  ExceptionFilter,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';
import { ErrorIntermediary } from 'src/errors/errors';

@Catch(ErrorIntermediary)
export class ErrorIntermediaryFilter implements ExceptionFilter {
  catch(exception: ErrorIntermediary, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();

    response.status(HttpStatus.BAD_REQUEST).json({
      message: 'Não é permitido um terceiro para essa conta',
      statusCode: HttpStatus.BAD_REQUEST,
    });
  }
}
