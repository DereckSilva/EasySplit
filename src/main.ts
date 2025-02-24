import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { ValidationPipe } from '@nestjs/common';
import { ErrorFoundUserFilter } from './filters/error-found-user.filter';
import { ErrorInvalidPasswordFilter } from './filters/error-invalid-password.filter';
import { ErrorUpdateUserFilter } from './filters/error-update-user.filter';
import { ErrorInvalidOldPasswordFilter } from './filters/error-invalid-old-password.filter';
import { ErrorRemoveUserFilter } from './filters/error-remove-user.filter';
import { ErrorRoleUserFilter } from './filters/error-role-user.filter';
import { ErrorEmptyIntermediaryFilter } from './filters/error-empty-intermediary.filter';
import { ErrorRemoveExpenseFilter } from './filters/error-remove-expense.filter';
import { SwaggerModule, DocumentBuilder } from '@nestjs/swagger';
import { AppModule as AppModuleApi } from './api-docs/app.module';
import { ErrorFoundExpenseFilter } from './filters/error-found-expense.filter';
import { ErrorIntermediaryFilter } from './filters/error-intermediary-expense.filter';
import { ErrorOldDatePaymentFilter } from './filters/error-old-date-expense.filter';
import { ErrorDatePaymentFilter } from './filters/error-date-expense.filter';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  const appSwagger = await NestFactory.create(AppModuleApi);

  // filtros para aplicação e api
  app.useGlobalFilters(
    new ErrorFoundUserFilter(),
    new ErrorInvalidPasswordFilter(),
    new ErrorInvalidOldPasswordFilter(),
    new ErrorUpdateUserFilter(),
    new ErrorRemoveUserFilter(),
    new ErrorRoleUserFilter(),
    new ErrorEmptyIntermediaryFilter(),
    new ErrorRemoveExpenseFilter(),
    new ErrorFoundExpenseFilter(),
    new ErrorOldDatePaymentFilter(),
    new ErrorDatePaymentFilter(),
    new ErrorIntermediaryFilter(),
  );
  appSwagger.useGlobalFilters(
    new ErrorFoundUserFilter(),
    new ErrorInvalidPasswordFilter(),
    new ErrorInvalidOldPasswordFilter(),
    new ErrorUpdateUserFilter(),
    new ErrorRemoveUserFilter(),
    new ErrorRoleUserFilter(),
    new ErrorEmptyIntermediaryFilter(),
    new ErrorRemoveExpenseFilter(),
    new ErrorFoundExpenseFilter(),
    new ErrorOldDatePaymentFilter(),
    new ErrorDatePaymentFilter(),
    new ErrorIntermediaryFilter(),
  );

  // validação para aplicação e api
  app.useGlobalPipes(new ValidationPipe());
  appSwagger.useGlobalPipes(new ValidationPipe());

  const options = new DocumentBuilder()
    .setTitle('EasySplit')
    .setDescription(
      'Essa API tem como foco a organização de contas pessoais e a divisão inteligente entre contas que tenham dois ou mais destinatários de uma mesma conta',
    )
    .setVersion('1.0')
    .addTag('User')
    .addTag('Expense')
    .addBearerAuth()
    .build();

  const document = SwaggerModule.createDocument(appSwagger, options);
  SwaggerModule.setup('api-docs', appSwagger, document);

  await app.listen(process.env.PORT ?? 3000);
  await appSwagger.listen(process.env.PORT ?? 3001);
}
bootstrap();
