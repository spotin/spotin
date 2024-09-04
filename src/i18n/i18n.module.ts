import * as path from 'path';
import { Module } from '@nestjs/common';
import {
	AcceptLanguageResolver,
	I18nModule as NestI18nModule,
} from 'nestjs-i18n';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { EnvironmentVariables } from '@/config/config.constants';

@Module({
	imports: [
		NestI18nModule.forRootAsync({
			imports: [ConfigModule],
			inject: [ConfigService],
			useFactory: async (
				configService: ConfigService<EnvironmentVariables, true>,
			) => ({
				fallbackLanguage: 'en',
				loaderOptions: {
					path: path.join(__dirname, './translations'),
					watch: configService.get('NODE_ENV') === 'development',
				},
			}),
			resolvers: [AcceptLanguageResolver],
		}),
	],
	exports: [NestI18nModule],
})
export class I18nModule {}
