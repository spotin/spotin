import { PickType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { ResetPasswordRequest } from '@/auth/reset-password/types/reset-password-request.type';

export class ResetPasswordRequestDto
	extends PickType(UserDto, ['email'] as const)
	implements ResetPasswordRequest {}
