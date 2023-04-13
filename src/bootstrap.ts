import * as nunjucks from 'nunjucks';
import * as cookieParser from 'cookie-parser';
import { HttpAdapterHost } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import { PrismaClientExceptionFilter, PrismaService } from 'nestjs-prisma';
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger';

const isProduction = process.env.NODE_ENV === 'production';

export async function bootstrap(
  app: NestExpressApplication,
): Promise<NestExpressApplication> {
  const { httpAdapter } = app.get(HttpAdapterHost);

  app.useGlobalFilters(new PrismaClientExceptionFilter(httpAdapter));
  app.useGlobalPipes(
    new ValidationPipe({
      transform: true,
    }),
  );

  nunjucks.configure(join(__dirname, '..', 'views'), {
    express: app,
    autoescape: true,
    watch: !isProduction,
    noCache: !isProduction,
  });

  app.setBaseViewsDir(join(__dirname, '..', 'views'));
  app.useStaticAssets(join(__dirname, '..', 'public'));
  app.setViewEngine('njk');

  const prismaService = app.get(PrismaService);
  await prismaService.enableShutdownHooks(app);

  const config = new DocumentBuilder()
    .setTitle('SpotIn API')
    .setDescription('The SpotIn API description')
    .setVersion(process.env.npm_package_version as string)
    .addCookieAuth('accessToken', {
      type: 'apiKey',
      bearerFormat: 'jwt',
      in: 'cookie',
      description: 'The cookie containing the access token',
    })
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

  return app;
}
