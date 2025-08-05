import { Stats } from '@/stats/types/stats';
import { IsNumber, IsPositive } from 'class-validator';

export class StatsDto implements Stats {
	/**
	 * Number of users
	 */
	@IsNumber()
	@IsPositive()
	numberOfUsers: number;

	/**
	 * Number of certified users
	 */
	@IsNumber()
	@IsPositive()
	numberOfCertifiedUsers: number;

	/**
	 * Number of public spots
	 */
	@IsNumber()
	@IsPositive()
	numberOfPublicSpots: number;
}
