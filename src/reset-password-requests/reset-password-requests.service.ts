import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';
import { randomUUID } from 'crypto';
import { User } from '@prisma/client';
import { MailService } from '@/mail/mail.service';

@Injectable()
export class ResetPasswordRequestsService {
	constructor(
		private readonly mail: MailService,
		private readonly prisma: PrismaService,
	) {}

	async getResetPasswordRequests() {
		return await this.prisma.resetPasswordRequest.findMany({});
	}

	async getResetPasswordRequest(id: string) {
		return await this.prisma.resetPasswordRequest.findFirst({
			where: { id },
		});
	}

	async getResetPasswordRequestByToken(token: string) {
		return await this.prisma.resetPasswordRequest.findFirst({
			where: { token },
		});
	}

	async createResetPasswordRequest(userId: string) {
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

	async updateResetPasswordRequest(requestPasswordResetId: string) {
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

	async deleteResetPasswordRequest(requestPasswordResetId: string) {
		await this.prisma.resetPasswordRequest.delete({
			where: {
				id: requestPasswordResetId,
			},
		});
	}

	async sendResetPasswordRequestForUser(user: User) {
		let requestPasswordReset;

		if (user.resetPasswordRequestId) {
			requestPasswordReset = await this.updateResetPasswordRequest(
				user.resetPasswordRequestId,
			);
		} else {
			requestPasswordReset = await this.createResetPasswordRequest(user.id);
		}

		await this.mail.sendResetPasswordMail(user, requestPasswordReset.token);
	}

	async sendResetPasswordRequestForNewUser(newUser: User) {
		const passwordResetRequest = await this.createResetPasswordRequest(
			newUser.id,
		);

		await this.mail.sendWelcomeMail(newUser, passwordResetRequest.token);
	}

	async deleteResetPasswordRequestForUser(user: User) {
		if (user.resetPasswordRequestId) {
			await this.deleteResetPasswordRequest(user.resetPasswordRequestId);
		}
	}
}
