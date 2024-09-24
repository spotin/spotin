import { Injectable } from '@nestjs/common';
import { MailerService } from '@nestjs-modules/mailer';
import { ConfigService } from '@nestjs/config';
import { EnvironmentVariables, BASE_URL } from '@/config/config.constants';
import { MailTemplate } from '@/mail/mail.constants';
import { User } from '@/users/types/user';
import { I18nService } from 'nestjs-i18n';

@Injectable()
export class MailService {
	constructor(
		private readonly configService: ConfigService<EnvironmentVariables, true>,
		private readonly i18nService: I18nService,
		private readonly mailerService: MailerService,
	) {}

	public async sendWelcomeMail(
		user: User,
		lang: string,
		tokenId: string,
	): Promise<void> {
		const subject = await this.i18nService.translate('mail.welcome.subject', {
			lang,
		});

		const link = `${this.configService.get(BASE_URL, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject,
			template: MailTemplate.WELCOME,
			context: {
				// https://github.com/toonvanstrijp/nestjs-i18n/issues/471
				i18nLang: lang,
				args: {
					username: user.username,
				},
				link,
			},
		});
	}

	public async sendResetPasswordMail(
		user: User,
		lang: string,
		tokenId: string,
	): Promise<void> {
		const subject = await this.i18nService.translate(
			'mail.passwordReset.subject',
			{
				lang,
			},
		);

		const link = `${this.configService.get(BASE_URL, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject,
			template: MailTemplate.RESET_PASSWORD,
			context: {
				// https://github.com/toonvanstrijp/nestjs-i18n/issues/471
				i18nLang: lang,
				args: {
					username: user.username,
				},
				link,
			},
		});
	}

	public async sendAccountRecoverMail(
		user: User,
		lang: string,
		tokenId: string,
	): Promise<void> {
		const subject = await this.i18nService.translate(
			'mail.recoverAccount.subject',
			{
				lang,
			},
		);

		const link = `${this.configService.get(BASE_URL, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject,
			template: MailTemplate.RECOVER_ACCOUNT,
			context: {
				// https://github.com/toonvanstrijp/nestjs-i18n/issues/471
				i18nLang: lang,
				args: {
					username: user.username,
				},
				link,
			},
		});
	}
}
