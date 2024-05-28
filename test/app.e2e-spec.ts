import * as request from 'supertest';
import { Test, TestingModule } from '@nestjs/testing';
import { AppModule } from '@/app.module';
import { NestExpressApplication } from '@nestjs/platform-express';
import { bootstrap } from '@/bootstrap';

describe('AppController (e2e)', () => {
	let app: NestExpressApplication;

	beforeEach(async () => {
		const moduleFixture: TestingModule = await Test.createTestingModule({
			imports: [AppModule],
		}).compile();

		const instance =
			moduleFixture.createNestApplication<NestExpressApplication>();

		app = await bootstrap(instance);

		await app.init();
	});

	afterEach(async () => {
		await app.close();
	});

	it('/ (GET)', async () => {
		return request(app.getHttpServer()).get('/').expect(200);
	});
});
