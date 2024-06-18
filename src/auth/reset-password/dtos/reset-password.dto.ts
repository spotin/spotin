import { ResetPassword } from '@/auth/reset-password/types/reset-password.type';
import { UserDto } from '@/users/dtos/user.dto';
import { PickType } from '@nestjs/swagger';

export class ResetPasswordDto
	extends PickType(UserDto, ['password'] as const)
	implements ResetPassword {}
