import { HttpAdapterHost, NestFactory } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { AppModule } from './app.module';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import * as nunjucks from 'nunjucks';
import { PrismaService } from './prisma/prisma.service';
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger';
import * as cookieParser from 'cookie-parser';
import { PrismaClientKnownRequestExceptionsFilter } from './prisma/filters/prisma-client-known-request-exceptions.filter';
import { PrismaClientUnknownRequestExceptionsFilter } from './prisma/filters/prisma-client-unknown-request-exceptions.filter';
import { PrismaClientValidationExceptionsFilter } from './prisma/filters/prisma-client-validation-exceptions.filter';

const IS_PRODUCTION = false;

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);

  const { httpAdapter } = app.get(HttpAdapterHost);

  app.useGlobalPipes(
    new ValidationPipe({
      transform: true,
    }),
  );

  app.useGlobalFilters(
    new PrismaClientKnownRequestExceptionsFilter(httpAdapter),
    new PrismaClientUnknownRequestExceptionsFilter(httpAdapter),
    new PrismaClientValidationExceptionsFilter(httpAdapter),
  );

  const opts: nunjucks.ConfigureOptions = {
    express: app,
    autoescape: true,
    watch: !IS_PRODUCTION,
    noCache: !IS_PRODUCTION,
  };
  nunjucks.configure(join(__dirname, '..', 'views'), opts);

  app.setBaseViewsDir(join(__dirname, '..', 'views'));
  app.useStaticAssets(join(__dirname, '..', 'public'));
  app.setViewEngine('njk');

  const prismaService = app.get(PrismaService);
  await prismaService.enableShutdownHooks(app);

  const config = new DocumentBuilder()
    .setTitle('SpotIn API')
    .setDescription('The SpotIn API description')
    .setVersion(process.env.npm_package_version as string)
    .build();

  const document = SwaggerModule.createDocument(app, config);

  SwaggerModule.setup('api', app, document, {
    swaggerOptions: {
      showExtensions: true,
      tagsSorter: 'alpha',
      operationsSorter: 'alpha',
      persistAuthorization: true,
    },
  });

  app.use(cookieParser());

  await app.listen(3000);
}
bootstrap();
