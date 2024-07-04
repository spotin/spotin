import { PickType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { Profile } from '@/profile/types/profile';

export class ProfileDto
	extends PickType(UserDto, ['username', 'email'] as const)
	implements Profile {}
