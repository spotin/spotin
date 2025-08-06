import { OmitType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { CreateUser } from '@/users/types/create-user';

export class CreateUserDto
	extends OmitType(UserDto, [
		'id',
		'password',
		'bio',
		'createdAt',
		'updatedAt',
	] as const)
	implements CreateUser {}
