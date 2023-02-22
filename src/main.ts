import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import * as nunjucks from 'nunjucks';
import { PrismaService } from './prisma/prisma.service';
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger';
import * as cookieParser from 'cookie-parser';

const IS_PRODUCTION = false;

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);

  const opts: nunjucks.ConfigureOptions = {
    express: app,
    autoescape: true,
    watch: !IS_PRODUCTION,
    noCache: !IS_PRODUCTION,
  };
  nunjucks.configure(join(__dirname, 'views'), opts);

  app.setBaseViewsDir(join(__dirname, 'views'));
  app.useStaticAssets(join(__dirname, 'public'));
  app.setViewEngine('njk');

  const prismaService = app.get(PrismaService);
  await prismaService.enableShutdownHooks(app);

  const config = new DocumentBuilder()
    .setTitle('SpotIn API')
    .setDescription('The SpotIn API description')
    .setVersion('1.0')
    .addTag('spots')
    .build();
  const document = SwaggerModule.createDocument(app, config);
  SwaggerModule.setup('swagger-ui', app, document);

  app.use(cookieParser());

  await app.listen(3000);
}
bootstrap();
