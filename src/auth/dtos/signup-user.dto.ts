import { PickType } from '@nestjs/swagger';
import { UserDto } from '@/users/dtos/user.dto';
import { SignupUser } from '@/auth/types/signup-user.type';

export class SignupUserDto
  extends PickType(UserDto, ['username', 'email', 'password'] as const)
  implements SignupUser {}
