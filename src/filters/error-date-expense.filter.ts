import {
  ArgumentsHost,
  Catch,
  ExceptionFilter,
  HttpStatus,
} from '@nestjs/common';
import { Response } from 'express';
import { ErrorDatePayment } from 'src/errors/errors';

@Catch(ErrorDatePayment)
export class ErrorDatePaymentFilter implements ExceptionFilter {
  catch(exception: ErrorDatePayment, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();

    response.status(HttpStatus.BAD_REQUEST).json({
      message: 'A data de pagamento e inválida',
      statusCode: HttpStatus.BAD_REQUEST,
    });
  }
}
