import {
  ArgumentsHost,
  Catch,
  ExceptionFilter,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';
import { ErrorOldDatePayment } from 'src/errors/errors';

@Catch(ErrorOldDatePayment)
export class ErrorOldDatePaymentFilter implements ExceptionFilter {
  catch(exception: ErrorOldDatePayment, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();

    response.status(HttpStatus.BAD_REQUEST).json({
      message: 'A data de pagamento não pode ser menor que a data atual',
      statusCode: HttpStatus.BAD_REQUEST,
    });
  }
}
