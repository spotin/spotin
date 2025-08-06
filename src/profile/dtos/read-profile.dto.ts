import { ProfileDto } from '@/profile/dtos/profile.dto';
import { Profile } from '@/profile/types/profile';
import { ReadProfile } from '@/profile/types/read-profile';

export class ReadProfileDto extends ProfileDto implements ReadProfile {
	constructor(entity: Profile) {
		super();

		this.username = entity.username;
		this.email = entity.email;
		this.bio = entity.bio;
		this.createdAt = entity.createdAt;
	}
}
