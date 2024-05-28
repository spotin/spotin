import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
import { PrismaService } from 'nestjs-prisma';
import * as bcrypt from 'bcrypt';

@Injectable()
export class UsersService {
	constructor(private readonly prisma: PrismaService) {}

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

	async getUserByUsername(username: string) {
		return await this.prisma.user.findFirst({
			where: { username },
			include: {
				spots: true,
			},
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

	async createUser(createUser: Prisma.UserCreateInput) {
		const newUser = await this.prisma.user.create({
			data: {
				...createUser,
				password: await bcrypt.hashSync(
					createUser.password,
					bcrypt.genSaltSync(10),
				),
			},
			include: {
				spots: true,
			},
		});
		return newUser;
	}

	async updateUser(userId: string, updateUser: Prisma.UserUpdateInput) {
		return await this.prisma.user.update({
			where: {
				id: userId,
			},
			data: updateUser,
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
