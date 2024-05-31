import { OmitType, PartialType } from '@nestjs/swagger';
import { SpotDto } from '@/spots/dtos/spot.dto';

export class UpdateSpotDto extends PartialType(
	OmitType(SpotDto, [
		'id',
		'userId',
		'createdAt',
		'updatedAt',
		'deletedAt',
	] as const),
) {}
