import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
import { PrismaService } from 'nestjs-prisma';
import * as bcrypt from 'bcrypt';
import { randomBytes } from 'crypto';
import { ResetPasswordRequestsService } from '@/reset-password-requests/reset-password-requests.service';

@Injectable()
export class UsersService {
	constructor(
		private readonly prisma: PrismaService,
		private readonly resetPasswordRequestsService: ResetPasswordRequestsService,
	) {}

	async getUsers() {
		return await this.prisma.user.findMany({
			include: {
				spots: true,
			},
		});
	}

	async getUser(id: string) {
		return await this.prisma.user.findFirst({
			where: { id },
			include: {
				spots: true,
			},
		});
	}

	async getUserByEmail(email: string) {
		return await this.prisma.user.findFirst({
			where: { email },
		});
	}

	async getUserByTokenHash(tokenHash: string) {
		const token = await this.prisma.token.findFirst({
			where: {
				hash: {
					equals: tokenHash,
				},
			},
		});

		if (!token) {
			return null;
		}

		const user = await this.prisma.user.findFirst({
			where: {
				id: {
					equals: token.userId,
				},
			},
		});

		return user;
	}

	async getUserByResetPasswordRequestToken(token: string) {
		return await this.prisma.user.findFirst({
			where: {
				resetPasswordRequest: {
					token,
				},
			},
		});
	}

	async createUser(createUser: Omit<Prisma.UserCreateInput, 'password'>) {
		// Generate a random password for the user until they set it
		const password = await bcrypt.hashSync(
			randomBytes(20).toString('hex'),
			bcrypt.genSaltSync(10),
		);

		const newUser = await this.prisma.user.create({
			data: {
				...createUser,
				password,
			},
			include: {
				spots: true,
			},
		});

		await this.resetPasswordRequestsService.sendResetPasswordRequestForNewUser(
			newUser,
		);

		return newUser;
	}

	async updateUser(userId: string, updateUser: Prisma.UserUpdateInput) {
		let password = updateUser.password;

		if (password && typeof password === 'string') {
			password = await bcrypt.hashSync(password, bcrypt.genSaltSync(10));
		}

		return await this.prisma.user.update({
			where: {
				id: userId,
			},
			data: {
				...updateUser,
				password,
			},
			include: {
				spots: true,
			},
		});
	}

	deleteUser(userId: string) {
		return this.prisma.user.delete({
			where: {
				id: userId,
			},
		});
	}
}
