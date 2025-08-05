import { Stats } from '@/stats/types/stats';
import { UserRole } from '@/users/enums/user-role';
import { Injectable } from '@nestjs/common';
import { PrismaService } from 'nestjs-prisma';

@Injectable()
export class StatsService {
	constructor(private readonly prisma: PrismaService) {}

	async getStats(): Promise<Stats> {
		const [users, certifiedUsers, publicSpots] = await this.prisma.$transaction(
			[
				this.prisma.user.aggregate({
					_count: true,
				}),
				this.prisma.user.aggregate({
					_count: true,
					where: {
						role: {
							not: UserRole.STANDARD_USER,
						},
					},
				}),
				this.prisma.spot.aggregate({
					_count: true,
					where: {
						public: true,
					},
				}),
			],
		);

		return {
			numberOfUsers: users._count,
			numberOfCertifiedUsers: certifiedUsers._count,
			numberOfPublicSpots: publicSpots._count,
		};
	}
}
