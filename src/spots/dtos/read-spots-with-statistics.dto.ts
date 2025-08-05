import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { SpotsStatisticsDto } from '@/spots/dtos/spots-statistics.dto';
import { ReadSpotsWithStatistics } from '@/spots/types/read-spots-with-statistics';
import { SpotsWithStatistics } from '@/spots/types/spots-with-statistics';
import { IsArray, ValidateNested } from 'class-validator';

export class ReadSpotsWithStatisticsDto implements ReadSpotsWithStatistics {
	/**
	 * Statistics for spots
	 */
	spotsStatistics: SpotsStatisticsDto;

	/**
	 * Spots
	 */
	@IsArray()
	@ValidateNested({ each: true })
	spots: ReadSpotDto[];

	constructor(entity: SpotsWithStatistics) {
		this.spotsStatistics = entity.spotsStatistics;
		this.spots = entity.spots.map((spot) => new ReadSpotDto(spot));
	}
}
