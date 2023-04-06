import { OmitType } from '@nestjs/swagger';
import { UserDto } from './user.dto';

export class CreateUserDto extends OmitType(UserDto, [
  'id',
  'spots',
  'createdAt',
  'updatedAt',
  'deletedAt',
] as const) {}