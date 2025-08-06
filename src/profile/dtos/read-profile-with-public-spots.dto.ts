import { ReadProfileDto } from '@/profile/dtos/read-profile.dto';
import { ProfileWithPublicSpots } from '@/profile/types/profile-with-public-spots';
import { ReadProfileWithPublicSpots } from '@/profile/types/read-profile-with-public-spots';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { SpotsStatisticsDto } from '@/spots/dtos/spots-statistics.dto';
import { OmitType } from '@nestjs/swagger';
import { IsArray, ValidateNested } from 'class-validator';

export class ReadProfileWithPublicSpotsDto
	extends OmitType(ReadProfileDto, ['email'] as const)
	implements ReadProfileWithPublicSpots
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

	constructor(entity: ProfileWithPublicSpots) {
		super();

		this.username = entity.username;
		this.createdAt = entity.createdAt;
		this.bio = entity.bio;
		this.spotsStatistics = entity.spotsStatistics;
		this.spots = entity.spots.map((spot) => new ReadSpotDto(spot));
	}
}
