import * as path from 'path';
import { Module } from '@nestjs/common';
import { MailerModule } from '@nestjs-modules/mailer';
import { MailService } from '@/mail/mail.service';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { HandlebarsAdapter } from '@nestjs-modules/mailer/dist/adapters/handlebars.adapter';
import {
	EnvironmentVariables,
	MAIL_HOST,
	MAIL_PASS,
	MAIL_PORT,
	MAIL_SECURE,
	MAIL_SENDER_NAME,
	MAIL_USER,
} from '@/config/config.constants';
import { PrismaModule } from 'nestjs-prisma';

@Module({
	imports: [
		ConfigModule,
		MailerModule.forRootAsync({
			imports: [ConfigModule],
			inject: [ConfigService],
			useFactory: async (
				configService: ConfigService<EnvironmentVariables, true>,
			) => ({
				transport: {
					host: configService.get(MAIL_HOST, { infer: true }),
					port: configService.get(MAIL_PORT, { infer: true }),
					secure: configService.get(MAIL_SECURE, { infer: true }),
					ignoreTLS: true,
					auth: {
						user: configService.get(MAIL_USER, { infer: true }),
						pass: configService.get(MAIL_PASS, { infer: true }),
					},
				},
				defaults: {
					from: `"${configService.get(MAIL_SENDER_NAME, { infer: true })}" <${configService.get(MAIL_USER, { infer: true })}>`,
				},
				template: {
					dir: path.join(__dirname, '/templates'),
					adapter: new HandlebarsAdapter(),
					options: {
						strict: true,
					},
				},
			}),
		}),
		PrismaModule,
	],
	providers: [MailService],
	exports: [MailService],
})
export class MailModule {}
