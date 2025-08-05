import { CreateSpot } from '@/spots/types/create-spot';
import { Spot } from '@/spots/types/spot';
import { SpotWithUser } from '@/spots/types/spot-with-user';
import { SpotsWithStatistics } from '@/spots/types/spots-with-statistics';
import { UpdateSpot } from '@/spots/types/update-spot';
import { UserRole } from '@/users/enums/user-role';
import { User } from '@/users/types/user';
import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
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
				user: true,
			},
		});

		return {
			...spot,
			user: {
				...spot.user,
				role: UserRole[spot.user.role],
			},
			payload: spot.payload ? JSON.stringify(spot.payload) : null,
		};
	}

	async createSpot(createSpot: CreateSpot, user: User): Promise<Spot> {
		const newSpot = await this.prisma.spot.create({
			data: {
				...createSpot,
				// eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
				payload: createSpot.payload
					? JSON.parse(createSpot.payload)
					: undefined,
				user: {
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
				// eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
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
	async getPublicSpots(): Promise<SpotsWithStatistics> {
		const where: Prisma.SpotWhereInput = {
			public: true,
			deletedAt: {
				equals: null,
			},
			configured: true,
		};

		const publicSpots = await this.prisma.spot.findMany({
			where,
		});

		const spotsStatistics = await this.prisma.spot.aggregate({
			where,
			_count: true,
			_min: {
				latitude: true,
				longitude: true,
			},
			_max: {
				latitude: true,
				longitude: true,
			},
		});

		return {
			spotsStatistics: {
				count: spotsStatistics._count,
				latitude: {
					min: spotsStatistics._min.latitude ?? NaN,
					max: spotsStatistics._max.latitude ?? NaN,
				},
				longitude: {
					min: spotsStatistics._min.longitude ?? NaN,
					max: spotsStatistics._max.longitude ?? NaN,
				},
			},
			spots: publicSpots.map((publicSpot) => ({
				...publicSpot,
				payload: publicSpot.payload ? JSON.stringify(publicSpot.payload) : null,
			})),
		};
	}
}
