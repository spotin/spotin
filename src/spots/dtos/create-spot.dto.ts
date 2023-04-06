import { OmitType } from '@nestjs/swagger';
import { SpotDto } from './spot.dto';

export class CreateSpotDto extends OmitType(SpotDto, [
  'id',
  'userId',
  'createdAt',
  'updatedAt',
  'deletedAt',
] as const) {}
