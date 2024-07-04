import { IsNumber, Min } from 'class-validator';
import { SpotsStatistics, Statistics } from '@/spots/types/spots-statistics';

export class StatisticsDto implements Statistics {
	/**
	 * Minimum value
	 */
	@IsNumber()
	min: number;

	/**
	 * Maximum value
	 */
	@IsNumber()
	max: number;
}

export class SpotsStatisticsDto implements SpotsStatistics {
	/**
	 * Total number of spots
	 */
	@IsNumber()
	@Min(0)
	count: number;

	/**
	 * Latitude statistics for the spots
	 */
	latitude: StatisticsDto;

	/**
	 * Longitude statistics for the spots
	 */
	longitude: StatisticsDto;
}
