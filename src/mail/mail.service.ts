import { Injectable } from '@nestjs/common';
import { MailerService } from '@nestjs-modules/mailer';
import { ConfigService } from '@nestjs/config';
import { EnvironmentVariables, FQDN } from '@/config/config.constants';
import { MailTemplate } from '@/mail/mail.constants';
import { User } from '@/users/types/user';
import { UserWithResetPasswordRequest } from '@/reset-password-requests/types/user-with-reset-password-request';

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
		userWithResetPasswordRequest: UserWithResetPasswordRequest,
	): Promise<void> {
		const link = `${this.configService.get(FQDN, {
			infer: true,
		})}/auth/reset-password?token=${userWithResetPasswordRequest.resetPasswordRequest?.token}`;

		await this.mailerService.sendMail({
			to: userWithResetPasswordRequest.email,
			subject: 'Reset your password',
			template: MailTemplate.RESET_PASSWORD,
			context: {
				username: userWithResetPasswordRequest.username,
				link,
			},
		});
	}
}
