import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';
import * as argon2id from 'argon2';
import { randomBytes } from 'crypto';
import { ResetPasswordRequestsService } from '@/reset-password-requests/reset-password-requests.service';
import { User } from '@/users/types/user';
import { CreateUser } from '@/users/types/create-user';
import { UpdateUser } from '@/users/types/update-user';
import { UserRole } from '@/users/enums/user-role';
import { UserWithSpots } from '@/users/types/user-with-spots';

@Injectable()
export class UsersService {
	constructor(
		private readonly prisma: PrismaService,
		private readonly resetPasswordRequestsService: ResetPasswordRequestsService,
	) {}

	async getUsers(): Promise<User[]> {
		const users = await this.prisma.user.findMany();

		return users.map((user) => ({
			...user,
			role: UserRole[user.role],
		}));
	}

	async getUser(id: string): Promise<User> {
		const user = await this.prisma.user.findFirstOrThrow({
			where: { id },
		});

		return {
			...user,
			role: UserRole[user.role],
		};
	}

	async getUserWithPublicSpotsByUsername(
		username: string,
	): Promise<UserWithSpots> {
		const userWithSpots = await this.prisma.user.findFirstOrThrow({
			where: { username },
			include: {
				spots: {
					where: {
						referenced: true,
					},
				},
			},
		});

		return {
			...userWithSpots,
			spots: userWithSpots.spots.map((spot) => ({
				...spot,
				payload: spot.payload ? JSON.stringify(spot.payload) : null,
			})),
			role: UserRole[userWithSpots.role],
		};
	}

	async getUserByEmail(email: string): Promise<User> {
		const user = await this.prisma.user.findFirstOrThrow({
			where: { email },
		});

		return {
			...user,
			role: UserRole[user.role],
		};
	}

	async getUserByTokenHash(tokenHash: string): Promise<User> {
		const token = await this.prisma.token.findFirstOrThrow({
			where: {
				hash: {
					equals: tokenHash,
				},
			},
		});

		const user = await this.prisma.user.findFirstOrThrow({
			where: {
				id: {
					equals: token.userId,
				},
			},
		});

		return {
			...user,
			role: UserRole[user.role],
		};
	}

	async getUserByResetPasswordRequestToken(token: string): Promise<User> {
		const user = await this.prisma.user.findFirstOrThrow({
			where: {
				resetPasswordRequest: {
					token,
				},
			},
		});

		return {
			...user,
			role: UserRole[user.role],
		};
	}

	async createUser(createUser: CreateUser): Promise<User> {
		// Generate a random password for the user until they set it
		const password = await argon2id.hash(randomBytes(20).toString('hex'));

		const newUser = await this.prisma.user.create({
			data: {
				...createUser,
				password,
			},
		});

		await this.resetPasswordRequestsService.sendResetPasswordRequestForNewUser({
			...newUser,
			role: UserRole[newUser.role],
		});

		return {
			...newUser,
			role: UserRole[newUser.role],
		};
	}

	async updateUser(userId: string, updateUser: UpdateUser): Promise<User> {
		let password = updateUser.password;

		if (password && typeof password === 'string') {
			password = await argon2id.hash(password);
		}

		const updatedUser = await this.prisma.user.update({
			where: {
				id: userId,
			},
			data: {
				...updateUser,
				password,
			},
		});

		return {
			...updatedUser,
			role: UserRole[updatedUser.role],
		};
	}

	async deleteUser(userId: string): Promise<void> {
		await this.prisma.user.delete({
			where: {
				id: userId,
			},
		});
	}
}
