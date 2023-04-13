import { OmitType } from '@nestjs/swagger';
import { UserDto } from './user.dto';
import { ReadSpotDto } from 'src/spots/dtos/read-spot.dto';

export class ReadUserDto extends OmitType(UserDto, ['password'] as const) {
  constructor(partial: Partial<UserDto>) {
    super();

    // Exclude password property from the object
    delete partial.password;

    if (partial.spots) {
      partial.spots = partial.spots.map((spot) => new ReadSpotDto(spot));
    }

    Object.assign(this, partial);
  }
}
