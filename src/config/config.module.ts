import * as Joi from 'joi';
import { Module } from '@nestjs/common';
import { ConfigModule as NestConfigModule } from '@nestjs/config';
import {
	BASE_URL,
	JWT_EXPIRATION_TIME,
	JWT_SECRET,
	MAIL_HOST,
	MAIL_PASS,
	MAIL_PORT,
	MAIL_SECURE,
	MAIL_USER,
	MAIL_SENDER_NAME,
	NODE_ENV,
} from '@/config/config.constants';

@Module({
	imports: [
		NestConfigModule.forRoot({
			validationSchema: Joi.object({
				[BASE_URL]: Joi.string().required(),
				[JWT_SECRET]: Joi.string().required(),
				[JWT_EXPIRATION_TIME]: Joi.string().required(),
				[MAIL_HOST]: Joi.string().required(),
				[MAIL_PORT]: Joi.number().required().default(587),
				[MAIL_USER]: Joi.string().required(),
				[MAIL_PASS]: Joi.string().required(),
				[MAIL_SECURE]: Joi.boolean().required().default(true),
				[MAIL_SENDER_NAME]: Joi.string().required(),
				[NODE_ENV]: Joi.string()
					.valid('development', 'test', 'production')
					.default('production'),
			}),
			validationOptions: {
				allowUnknown: true,
				abortEarly: false,
			},
			expandVariables: true,
		}),
	],
	exports: [NestConfigModule],
})
export class ConfigModule {}
