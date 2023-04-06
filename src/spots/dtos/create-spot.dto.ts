import { OmitType } from '@nestjs/swagger';
import { SpotDto } from './spot.dto';

export class CreateSpotDto extends OmitType(SpotDto, [
  'id',
  'createdAt',
  'updatedAt',
  'deletedAt',
] as const) {}
