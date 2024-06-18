import { PickType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { LoginUser } from '@/auth/local/types/login-user.type';

export class LoginUserDto
	extends PickType(UserDto, ['email', 'password'] as const)
	implements LoginUser {}
