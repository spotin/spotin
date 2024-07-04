import { ReadUserWithSpots } from '@/users/types/read-user-with-spots';
import { IsArray, ValidateNested } from 'class-validator';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { ReadUserDto } from '@/users/dtos/read-user.dto';
import { UserWithSpots } from '@/users/types/user-with-spots';
import { SpotsStatisticsDto } from '@/spots/dtos/spots-statistics.dto';

export class ReadUserWithSpotsDto
	extends ReadUserDto
	implements ReadUserWithSpots
{
	/**
	 * Statistics for user's spots
	 */
	spotsStatistics: SpotsStatisticsDto;

	/**
	 * Spots created by the user
	 */
	@IsArray()
	@ValidateNested({ each: true })
	spots: ReadSpotDto[];

	constructor(entity: UserWithSpots) {
		super(entity);

		this.spots = entity.spots.map((spot) => new ReadSpotDto(spot));
	}
}
