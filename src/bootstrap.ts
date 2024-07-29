import * as nunjucks from 'nunjucks';
import { I18nContext, I18nService } from 'nestjs-i18n';
import * as cookieParser from 'cookie-parser';
import { HttpAdapterHost } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import { PrismaClientExceptionFilter } from 'nestjs-prisma';
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger';
import {
	PASSWORD_RESET_HEADER_NAME,
	PassportStrategy,
	TOKEN_HEADER_NAME,
} from '@/auth/auth.constants';

export async function bootstrap(
	app: NestExpressApplication,
): Promise<NestExpressApplication> {
	const { httpAdapter } = app.get(HttpAdapterHost);

	app.useGlobalFilters(new PrismaClientExceptionFilter(httpAdapter));

	app.useGlobalPipes(
		new ValidationPipe({
			transform: true,
			whitelist: true, // Remove unknown properties from DTOs
		}),
	);

	// https://docs.nestjs.com/security/cors
	app.enableCors();

	// https://docs.nestjs.com/techniques/session
	app.set('trust proxy', process.env.NODE_ENV === 'production');

	nunjucks
		.configure(join(__dirname, '..', 'views'), {
			express: app,
			watch: process.env.NODE_ENV === 'development',
		})
		.addGlobal('title', 'Spot in');

	app.setBaseViewsDir(join(__dirname, '..', 'views'));
	app.useStaticAssets(join(__dirname, '..', 'public'));
	app.setViewEngine('njk');

	const i18n = app.get<I18nService>(I18nService);

	// Inspiration: https://github.com/toonvanstrijp/nestjs-i18n/blob/e0f3398682e540c93bd39fb29a4ceb270d28464c/src/i18n.module.ts#L90-L92
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	app.setLocal('t', (key: string, lang: string | undefined, args: any) => {
		let detectedLanguage = lang;

		if (I18nContext.current()) {
			detectedLanguage = I18nContext.current()?.lang;
		}

		return i18n.t(key, { lang: detectedLanguage, args });
	});

	const config = new DocumentBuilder()
		.setTitle('Spot in API')
		.setDescription('The Spot in API description')
		.setVersion(process.env.npm_package_version as string)
		.addBearerAuth(
			{
				type: 'http',
				description: 'The JWT to access protected endpoints',
			},
			PassportStrategy.JWT,
		)
		.addApiKey(
			{
				type: 'apiKey',
				description: 'The token to access protected endpoints',
				name: TOKEN_HEADER_NAME,
			},
			PassportStrategy.TOKEN,
		)
		.addApiKey(
			{
				type: 'apiKey',
				description: 'The token to reset the password',
				name: PASSWORD_RESET_HEADER_NAME,
			},
			PassportStrategy.RESET_PASSWORD,
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

	// https://docs.nestjs.com/techniques/cookies
	app.use(cookieParser());

	return app;
}
