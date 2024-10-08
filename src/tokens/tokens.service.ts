import { CreateToken } from '@/tokens/types/create-token';
import { Token } from '@/tokens/types/token';
import { User } from '@/users/types/user';
import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';
import * as crypto from 'crypto';
import { CreatedToken } from '@/tokens/types/created-token';

@Injectable()
export class TokensService {
	constructor(private readonly prisma: PrismaService) {}

	/** List tokens from user */
	async getTokens(user: User): Promise<Token[]> {
		return await this.prisma.token.findMany({
			where: {
				userId: {
					equals: user.id,
				},
			},
		});
	}

	/** Read a token by id */
	async getToken(tokenId: string, user: User): Promise<Token> {
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
		createToken: CreateToken,
		user: User,
	): Promise<CreatedToken> {
		const value = crypto.randomBytes(64).toString('base64');
		const hash = crypto.createHash('sha256').update(value).digest('hex');

		const newToken = await this.prisma.token.create({
			data: {
				...createToken,
				hash,
				user: {
					connect: {
						id: user.id,
					},
				},
			},
		});

		return {
			...newToken,
			value,
		};
	}

	/** Delete a token by id */
	async deleteToken(tokenId: string, user: User): Promise<void> {
		await this.prisma.token.delete({
			where: {
				id: tokenId,
				userId: user.id,
			},
		});
	}
}
