import { OmitType, PartialType } from '@nestjs/swagger';
import { SpotDto } from '@/spots/dtos/spot.dto';
import { UpdateSpot } from '@/spots/types/update-spot';

export class ConfigureSpotDto
	extends PartialType(
		OmitType(SpotDto, ['id', 'public', 'createdAt', 'updatedAt'] as const),
	)
	implements UpdateSpot {}
