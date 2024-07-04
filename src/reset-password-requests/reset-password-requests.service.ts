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

	async createResetPasswordRequest(
		userId: string,
	): Promise<ResetPasswordRequest> {
		const token = randomUUID();

		const requestPasswordReset = await this.prisma.resetPasswordRequest.create({
			data: {
				token,
				user: {
					connect: {
						id: userId,
					},
				},
			},
		});

		return requestPasswordReset;
	}

	async updateResetPasswordRequest(
		requestPasswordResetId: string,
	): Promise<ResetPasswordRequest> {
		const token = randomUUID();

		const requestPasswordReset = await this.prisma.resetPasswordRequest.update({
			where: {
				id: requestPasswordResetId,
			},
			data: {
				token,
			},
		});

		return requestPasswordReset;
	}

	async sendResetPasswordRequestForUser(user: User): Promise<void> {
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

		await this.mail.sendResetPasswordMail({
			...userWithResetPasswordRequest,
			resetPasswordRequest:
				userWithResetPasswordRequest.resetPasswordRequest as ResetPasswordRequest,
			role: UserRole[userWithResetPasswordRequest.role],
		});
	}

	async sendResetPasswordRequestForNewUser(newUser: User): Promise<void> {
		const passwordResetRequest = await this.createResetPasswordRequest(
			newUser.id,
		);

		await this.mail.sendWelcomeMail(newUser, passwordResetRequest.token);
	}

	async deleteResetPasswordRequestForUser(user: User): Promise<void> {
		const userWithResetPasswordRequest =
			await this.getUserWithResetPasswordRequest(user.id);

		const { resetPasswordRequest } = userWithResetPasswordRequest;

		if (resetPasswordRequest) {
			await await this.prisma.resetPasswordRequest.delete({
				where: {
					id: resetPasswordRequest.id,
				},
			});
		}
	}
}
