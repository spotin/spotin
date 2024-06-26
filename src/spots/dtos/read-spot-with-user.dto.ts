import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { ReadSpotWithUser } from '@/spots/types/read-spot-with-user';
import { SpotWithUser } from '@/spots/types/spot-with-user';
import { ReadUserDto } from '@/users/dtos/read-user.dto';

export class ReadSpotWithUserDto
	extends ReadSpotDto
	implements ReadSpotWithUser
{
	/**
	 * User that created the spot
	 */
	user: ReadUserDto;

	constructor(entity: SpotWithUser) {
		super(entity);

		this.user = new ReadUserDto(entity.user);
	}
}
