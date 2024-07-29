import * as path from 'path';
import { Module } from '@nestjs/common';
import {
	AcceptLanguageResolver,
	I18nModule as NestI18nModule,
} from 'nestjs-i18n';

@Module({
	imports: [
		NestI18nModule.forRoot({
			fallbackLanguage: 'en',
			loaderOptions: {
				path: path.join(__dirname, './translations'),
			},
			resolvers: [AcceptLanguageResolver],
		}),
	],
	exports: [NestI18nModule],
})
export class I18nModule {}
