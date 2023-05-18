import * as nunjucks from 'nunjucks';
import * as cookieParser from 'cookie-parser';
import { HttpAdapterHost } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import { PrismaClientExceptionFilter, PrismaService } from 'nestjs-prisma';
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';

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

  nunjucks
    .configure(join(__dirname, '..', 'views'), {
      express: app,
      watch: process.env.NODE_ENV === 'development',
    })
    .addGlobal('title', 'AHHHHH');

  app.setBaseViewsDir(join(__dirname, '..', 'views'));
  app.useStaticAssets(join(__dirname, '..', 'public'));
  app.setViewEngine('njk');

  const prismaService = app.get(PrismaService);
  await prismaService.enableShutdownHooks(app);

  const config = new DocumentBuilder()
    .setTitle('Spot in API')
    .setDescription('The Spot in API description')
    .setVersion(process.env.npm_package_version as string)
    .addCookieAuth(
      JWT_AUTH_KEY,
      {
        type: 'apiKey',
        in: 'cookie',
        description: 'The cookie containing the JWT',
      },
      JWT_AUTH_KEY,
    )
    .addApiKey(
      {
        type: 'apiKey',
        in: 'header',
        description: 'The token to access protected endpoints',
      },
      TOKEN_AUTH_KEY,
    )
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
