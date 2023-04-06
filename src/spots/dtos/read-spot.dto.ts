import { OmitType } from '@nestjs/swagger';
import { SpotDto } from './spot.dto';

export class ReadSpotDto extends OmitType(SpotDto, ['userId'] as const) {
  constructor(partial: Partial<SpotDto>) {
    super();

    // Exclude userId property from the object
    delete partial.userId;

    Object.assign(this, partial);
  }
}
