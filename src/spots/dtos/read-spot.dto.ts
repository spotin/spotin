import { SpotDto } from '@/spots/dtos/spot.dto';
import { ReadSpot } from '@/spots/types/read-spot';
import { Spot } from '@/spots/types/spot';

export class ReadSpotDto extends SpotDto implements ReadSpot {
	constructor(entity: Spot) {
		super();

		this.id = entity.id;
		this.name = entity.name;
		this.description = entity.description;
		this.latitude = entity.latitude;
		this.longitude = entity.longitude;
		this.payload = entity.payload;
		this.websiteTarget = entity.websiteTarget;
		this.directAccessToWebsiteTarget = entity.directAccessToWebsiteTarget;
		this.public = entity.public;
		this.createdAt = entity.createdAt;
		this.updatedAt = entity.updatedAt;
	}
}
