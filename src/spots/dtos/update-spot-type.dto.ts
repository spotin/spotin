import { OmitType } from '@nestjs/swagger';
import { Spot } from './spot.dto';

export class UpdateSpotDto extends OmitType(Spot, ['uuid']) {}
