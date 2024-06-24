import { OmitType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { ReadUser } from '@/users/types/read-user';
import { User } from '@/users/types/user';

export class ReadUserDto
	extends OmitType(UserDto, ['password'] as const)
	implements ReadUser
{
	constructor(entity: User) {
		super();

		this.id = entity.id;
		this.username = entity.username;
		this.email = entity.email;
		this.role = entity.role;
		this.enabled = entity.enabled;
		this.createdAt = entity.createdAt;
		this.updatedAt = entity.updatedAt;
	}
}
