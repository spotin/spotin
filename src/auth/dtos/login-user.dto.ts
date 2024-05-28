import { PickType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { LoginUser } from '@/auth/types/login-user.type';

export class LoginUserDto
	extends PickType(UserDto, ['username', 'password'] as const)
	implements LoginUser {}
