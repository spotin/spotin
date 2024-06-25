import { OmitType } from '@nestjs/swagger';
import { SpotDto } from '@/spots/dtos/spot.dto';
import { CreateSpot } from '@/spots/types/create-spot';

export class CreateSpotDto
	extends OmitType(SpotDto, ['id', 'createdAt', 'updatedAt'] as const)
	implements CreateSpot {}
