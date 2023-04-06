import { OmitType } from '@nestjs/swagger';
import { SpotDto } from './spot.dto';

export class UpdateSpotDto extends OmitType(SpotDto, [
  'id',
  'createdAt',
  'updatedAt',
  'deletedAt',
] as const) {}
