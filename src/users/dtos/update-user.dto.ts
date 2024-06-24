import { OmitType, PartialType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { UpdateUser } from '@/users/types/update-user';

export class UpdateUserDto
	extends PartialType(
		OmitType(UserDto, ['id', 'createdAt', 'updatedAt'] as const),
	)
	implements UpdateUser {}
