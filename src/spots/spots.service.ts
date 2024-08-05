import { CreateSpot } from '@/spots/types/create-spot';
import { Spot } from '@/spots/types/spot';
import { SpotWithUser } from '@/spots/types/spot-with-user';
import { UpdateSpot } from '@/spots/types/update-spot';
import { UserRole } from '@/users/enums/user-role';
import { User } from '@/users/types/user';
import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';

@Injectable()
export class SpotsService {
	constructor(private readonly prisma: PrismaService) {}

	async getSpots(user: User): Promise<Spot[]> {
		const spots = await this.prisma.spot.findMany({
			where: {
				userId: {
					equals: user.id,
				},
				deletedAt: {
					equals: null,
				},
			},
		});

		return spots.map((spot) => ({
			...spot,
			payload: spot.payload ? JSON.stringify(spot.payload) : null,
		}));
	}

	async getSpot(spotId: string, user?: User): Promise<Spot> {
		const spot = await this.prisma.spot.findFirstOrThrow({
			where: {
				id: spotId,
				userId: user?.id,
				deletedAt: {
					equals: null,
				},
			},
		});

		return {
			...spot,
			payload: spot.payload ? JSON.stringify(spot.payload) : null,
		};
	}

	async getSpotWithUser(spotId: string): Promise<SpotWithUser> {
		const spot = await this.prisma.spot.findFirstOrThrow({
			where: {
				id: spotId,
				deletedAt: {
					equals: null,
				},
			},
			include: {
				users: true,
			},
		});

		return {
			...spot,
			user: {
				...spot.users,
				role: UserRole[spot.users.role],
			},
			payload: spot.payload ? JSON.stringify(spot.payload) : null,
		};
	}

	async createSpot(createSpot: CreateSpot, user: User): Promise<Spot> {
		const newSpot = await this.prisma.spot.create({
			data: {
				...createSpot,
				payload: createSpot.payload
					? JSON.parse(createSpot.payload)
					: undefined,
				users: {
					connect: {
						id: user.id,
					},
				},
			},
		});

		return {
			...newSpot,
			payload: newSpot.payload ? JSON.stringify(newSpot.payload) : null,
		};
	}

	async updateSpot(
		spotId: string,
		updateSpot: UpdateSpot,
		user: User | null,
	): Promise<Spot> {
		const updatedSpot = await this.prisma.spot.update({
			where: {
				id: spotId,
				deletedAt: null,
				userId: user ? user.id : undefined,
			},
			data: {
				...updateSpot,
				payload: updateSpot.payload
					? JSON.parse(updateSpot.payload)
					: undefined,
			},
		});

		return {
			...updatedSpot,
			payload: updatedSpot.payload ? JSON.stringify(updatedSpot.payload) : null,
		};
	}

	async deleteSpot(spotId: string, user: User): Promise<void> {
		await this.prisma.spot.update({
			where: {
				id: spotId,
				userId: user.id,
				deletedAt: null,
			},
			data: {
				deletedAt: new Date(),
			},
		});
	}

	/** List public spots */
	async getPublicSpots(): Promise<Spot[]> {
		const publicSpots = await this.prisma.spot.findMany({
			where: {
				public: true,
				deletedAt: {
					equals: null,
				},
			},
		});

		return publicSpots.map((publicSpot) => ({
			...publicSpot,
			payload: publicSpot.payload ? JSON.stringify(publicSpot.payload) : null,
		}));
	}
}
