import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';
import { randomUUID } from 'crypto';
import { MailService } from '@/mail/mail.service';
import { ResetPasswordRequest } from '@/reset-password-requests/types/reset-password-request';
import { User } from '@/users/types/user';
import { UserRole } from '@/users/enums/user-role';
import { UserWithResetPasswordRequest } from '@/reset-password-requests/types/user-with-reset-password-request';

@Injectable()
export class ResetPasswordRequestsService {
	constructor(
		private readonly mail: MailService,
		private readonly prisma: PrismaService,
	) {}

	async getUserWithResetPasswordRequest(
		userId: string,
	): Promise<UserWithResetPasswordRequest> {
		const userWithResetPasswordRequest =
			await this.prisma.user.findFirstOrThrow({
				where: { id: userId },
				include: {
					resetPasswordRequest: true,
				},
			});

		return {
			...userWithResetPasswordRequest,
			role: UserRole[userWithResetPasswordRequest.role],
			resetPasswordRequest:
				userWithResetPasswordRequest.resetPasswordRequest as ResetPasswordRequest,
		};
	}

	async createOrUpdateResetPasswordRequest(
		user: User,
	): Promise<UserWithResetPasswordRequest> {
		const token = randomUUID();

		const userWithResetPasswordRequest = await this.prisma.user.update({
			where: {
				id: user.id,
			},
			data: {
				resetPasswordRequest: {
					upsert: {
						create: {
							token,
						},
						update: {
							token,
						},
					},
				},
			},
			include: {
				resetPasswordRequest: true,
			},
		});

		return {
			...userWithResetPasswordRequest,
			resetPasswordRequest:
				userWithResetPasswordRequest.resetPasswordRequest as ResetPasswordRequest,
			role: UserRole[userWithResetPasswordRequest.role],
		};
	}

	async sendResetPasswordRequestForNewUser(
		newUser: User,
		lang: string,
	): Promise<void> {
		const passwordResetRequest =
			await this.createOrUpdateResetPasswordRequest(newUser);

		await this.mail.sendWelcomeMail(
			newUser,
			lang,
			(passwordResetRequest.resetPasswordRequest as ResetPasswordRequest).token,
		);
	}

	async sendResetPasswordRequestForUser(
		user: User,
		lang: string,
	): Promise<void> {
		const passwordResetRequest =
			await this.createOrUpdateResetPasswordRequest(user);

		await this.mail.sendResetPasswordMail(
			user,
			lang,
			(passwordResetRequest.resetPasswordRequest as ResetPasswordRequest).token,
		);
	}

	async sendRecoverAccountForUser(user: User, lang: string): Promise<void> {
		const passwordResetRequest =
			await this.createOrUpdateResetPasswordRequest(user);

		await this.mail.sendAccountRecoverMail(
			user,
			lang,
			(passwordResetRequest.resetPasswordRequest as ResetPasswordRequest).token,
		);
	}

	async deleteResetPasswordRequestForUser(userId: string): Promise<void> {
		const userWithResetPasswordRequest =
			await this.getUserWithResetPasswordRequest(userId);

		const { resetPasswordRequest } = userWithResetPasswordRequest;

		if (resetPasswordRequest) {
			await this.prisma.resetPasswordRequest.delete({
				where: {
					id: resetPasswordRequest.id,
				},
			});
		}
	}
}
