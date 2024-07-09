import { Injectable } from '@nestjs/common';
import { MailerService } from '@nestjs-modules/mailer';
import { ConfigService } from '@nestjs/config';
import { EnvironmentVariables, FQDN } from '@/config/config.constants';
import { MailTemplate } from '@/mail/mail.constants';
import { User } from '@/users/types/user';

@Injectable()
export class MailService {
	constructor(
		private readonly mailerService: MailerService,
		private readonly configService: ConfigService<EnvironmentVariables, true>,
	) {}

	public async sendWelcomeMail(user: User, tokenId: string): Promise<void> {
		const link = `${this.configService.get(FQDN, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject: `Spot in - Welcome`,
			template: MailTemplate.WELCOME,
			context: {
				username: user.username,
				link,
			},
		});
	}

	public async sendResetPasswordMail(
		user: User,
		tokenId: string,
	): Promise<void> {
		const link = `${this.configService.get(FQDN, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject: 'Spot in - Reset your password',
			template: MailTemplate.RESET_PASSWORD,
			context: {
				username: user.username,
				link,
			},
		});
	}

	public async sendAccountRecoverMail(
		user: User,
		tokenId: string,
	): Promise<void> {
		const link = `${this.configService.get(FQDN, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject: 'Spot in - Recover your account',
			template: MailTemplate.RECOVER_ACCOUNT,
			context: {
				username: user.username,
				link,
			},
		});
	}
}
