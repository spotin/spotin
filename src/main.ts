import { NestFactory } from '@nestjs/core';
import { NestExpressApplication } from '@nestjs/platform-express';
import { bootstrap } from '@/bootstrap';
import { AppModule } from '@/app.module';

const main = async () => {
  const instance = await NestFactory.create<NestExpressApplication>(AppModule);

  const app = await bootstrap(instance);

  await app.listen(3000);
};

main();
