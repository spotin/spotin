import { NestFactory } from '@nestjs/core';
import { NestExpressApplication } from '@nestjs/platform-express';
import { bootstrap } from '@/bootstrap';
import { AppModule } from '@/app.module';

const main = async (): Promise<void> => {
	const instance = await NestFactory.create<NestExpressApplication>(AppModule);

	const app = bootstrap(instance);

	await app.listen(3000);
};

void main();
