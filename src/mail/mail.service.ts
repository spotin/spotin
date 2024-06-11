import { Injectable } from '@nestjs/common';
import { MailerService } from '@nestjs-modules/mailer';
import { User } from '@prisma/client';
import { ConfigService } from '@nestjs/config';
import { EnvironmentVariables, FQDN } from '@/config/config.constants';
import { MailTemplate } from '@/mail/mail.constants';

@Injectable()
export class MailService {
	constructor(
		private readonly mailerService: MailerService,
		private readonly configService: ConfigService<EnvironmentVariables, true>,
	) {}

	public async sendWelcomeMail(user: User, tokenId: string) {
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

	public async sendPasswordResetMail(user: User, tokenId: string) {
		const link = `${this.configService.get(FQDN, {
			infer: true,
		})}/auth/reset-password?token=${tokenId}`;

		await this.mailerService.sendMail({
			to: user.email,
			subject: 'Reset your password',
			template: MailTemplate.RESET_PASSWORD,
			context: {
				username: user.username,
				link,
			},
		});
	}
}
