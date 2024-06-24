import { OmitType, PartialType } from '@nestjs/swagger';
import { SpotDto } from '@/spots/dtos/spot.dto';
import { UpdateSpot } from '@/spots/types/update-spot';

export class UpdateSpotDto
	extends PartialType(
		OmitType(SpotDto, ['id', 'createdAt', 'updatedAt'] as const),
	)
	implements UpdateSpot {}
