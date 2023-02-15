import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import * as nunjucks from 'nunjucks';

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

  await app.listen(3000);
}
bootstrap();
