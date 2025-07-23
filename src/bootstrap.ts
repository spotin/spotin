import * as cookieParser from 'cookie-parser';
import * as nunjucks from 'nunjucks';
import * as passport from 'passport';
import * as session from 'express-session';
import { I18nContext, I18nService } from 'nestjs-i18n';
import { HttpAdapterHost } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import { PrismaClientExceptionFilter, PrismaService } from 'nestjs-prisma';
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger';
import {
	PASSWORD_RESET_HEADER_NAME,
	PassportStrategy,
	TOKEN_HEADER_NAME,
} from '@/auth/auth.constants';
import { ConfigService } from '@nestjs/config';
import {
	EnvironmentVariables,
	NODE_ENV,
	SESSION_CLEANUP_INTERVAL,
	SESSION_MAX_AGE,
	SESSION_COOKIE_NAME,
	SESSION_SECRET,
} from '@/config/config.constants';
import { NotFoundViewExceptionFilter } from '@/common/filters/not-found-view-exception.filter';
import { PrismaSessionStore } from '@quixo3/prisma-session-store';
import helmet from 'helmet';

export function bootstrap(app: NestExpressApplication): NestExpressApplication {
	// https://docs.nestjs.com/security/helmet
	app.use(
		helmet({
			contentSecurityPolicy: false,
		}),
	);

	const { httpAdapter } = app.get(HttpAdapterHost);

	app.useGlobalFilters(new PrismaClientExceptionFilter(httpAdapter));
	app.useGlobalFilters(new NotFoundViewExceptionFilter());

	app.useGlobalPipes(
		new ValidationPipe({
			transform: true,
			whitelist: true, // Remove unknown properties from DTOs
		}),
	);

	const i18n = app.get<I18nService>(I18nService);
	const prisma = app.get<PrismaService>(PrismaService);
	const configService =
		app.get<ConfigService<EnvironmentVariables, true>>(ConfigService);

	// https://docs.nestjs.com/security/cors
	app.enableCors();

	// https://docs.nestjs.com/techniques/session
	app.set(
		'trust proxy',
		configService.get(NODE_ENV, { infer: true }) === 'production',
	);

	nunjucks
		.configure(join(__dirname, '..', 'views'), {
			express: app,
			watch: configService.get(NODE_ENV, { infer: true }) === 'development',
		})
		.addGlobal('title', 'Spot in');

	app.setBaseViewsDir(join(__dirname, '..', 'views'));
	app.useStaticAssets(join(__dirname, '..', 'public'));
	app.setViewEngine('njk');

	// Inspiration: https://github.com/toonvanstrijp/nestjs-i18n/blob/e0f3398682e540c93bd39fb29a4ceb270d28464c/src/i18n.module.ts#L90-L92
	app.setLocal('t', (key: string, args?: any, lang?: string) => {
		let detectedLanguage = lang;

		if (I18nContext.current()) {
			detectedLanguage = I18nContext.current()?.lang;
		}

		// eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
		return i18n.t(key, { lang: detectedLanguage, args });
	});

	app.use(
		// https://github.com/expressjs/session
		session({
			name: configService.get(SESSION_COOKIE_NAME, { infer: true }),
			cookie: {
				maxAge: configService.get(SESSION_MAX_AGE, { infer: true }),
				secure: configService.get(NODE_ENV, { infer: true }) === 'production',
				httpOnly: true,
				sameSite: 'lax',
			},
			secret: configService.get(SESSION_SECRET, { infer: true }),
			resave: false,
			saveUninitialized: false,
			rolling: true,
			unset: 'destroy',
			// https://github.com/kleydon/prisma-session-store
			store: new PrismaSessionStore(prisma, {
				checkPeriod: configService.get(SESSION_CLEANUP_INTERVAL, {
					infer: true,
				}),
				dbRecordIdIsSessionId: undefined,
				dbRecordIdFunction: (): string => crypto.randomUUID(),
			}),
		}),
	);

	// https://www.passportjs.org/concepts/authentication/sessions/
	app.use(passport.session());

	const config = new DocumentBuilder()
		.setTitle('Spot in API')
		.setDescription('The Spot in API')
		.setVersion(process.env.npm_package_version as string)
		.addSecurity(PassportStrategy.SESSION, {
			type: 'apiKey',
			in: 'cookie',
			description: 'The session cookie to access protected endpoints.',
			name: configService.get(SESSION_COOKIE_NAME, { infer: true }),
		})
		.addSecurity(PassportStrategy.TOKEN, {
			type: 'apiKey',
			in: 'header',
			description: 'The token to access protected endpoints.',
			name: TOKEN_HEADER_NAME,
		})
		.addSecurity(PassportStrategy.RESET_PASSWORD, {
			type: 'apiKey',
			in: 'header',
			description: 'The token to reset the password.',
			name: PASSWORD_RESET_HEADER_NAME,
		})
		.addSecurity(PassportStrategy.UNCONFIGURED_SPOT, {
			type: 'apiKey',
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

	// https://docs.nestjs.com/techniques/cookies
	app.use(cookieParser());

	return app;
}
