import { OmitType } from '@nestjs/swagger';
import { Spot } from './spot.dto';

export class UpdateSpotDto extends OmitType(Spot, [
  'uuid',
  'created_at',
  'updated_at',
  'deleted_at',
]) {}
