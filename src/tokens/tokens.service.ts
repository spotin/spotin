import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';
import { Prisma, User } from '@prisma/client';

@Injectable()
export class TokensService {
	constructor(private readonly prisma: PrismaService) {}

	/** List tokens from user */
	async getTokens(user: User) {
		return await this.prisma.token.findMany({
			where: {
				userId: {
					equals: user.id,
				},
			},
		});
	}

	/** Read a token by id */
	async getToken(tokenId: string, user: User) {
		const token = await this.prisma.token.findFirstOrThrow({
			where: {
				userId: {
					equals: user.id,
				},
				id: {
					equals: tokenId,
				},
			},
		});

		return token;
	}

	// Create a new token for a user
	async createToken(
		createToken: Prisma.TokenCreateWithoutUsersInput,
		user: User,
	) {
		const newToken = await this.prisma.token.create({
			data: {
				...createToken,
				users: {
					connect: {
						id: user.id,
					},
				},
			},
		});

		return newToken;
	}

	/** Delete a token by id */
	async deleteToken(tokenId: string, user: User) {
		await this.prisma.token.delete({
			where: {
				id: tokenId,
				userId: user.id,
			},
		});
	}
}
