import { ReadUserWithSpots } from '@/users/types/read-user-with-spots';
import { IsArray, ValidateNested } from 'class-validator';
import { Type } from 'class-transformer';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { ReadUserDto } from '@/users/dtos/read-user.dto';
import { UserWithSpots } from '@/users/types/user-with-spots';

export class ReadUserWithSpotsDto
	extends ReadUserDto
	implements ReadUserWithSpots
{
	/**
	 * Spots created by the user
	 */
	@IsArray()
	@ValidateNested({ each: true })
	@Type(() => ReadSpotDto)
	spots: ReadSpotDto[];

	constructor(entity: UserWithSpots) {
		super(entity);

		this.spots = entity.spots.map((spot) => new ReadSpotDto(spot));
	}
}
