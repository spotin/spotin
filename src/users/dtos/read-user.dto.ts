import { OmitType } from '@nestjs/swagger';
import { UserDto } from './user.dto';

export class ReadUserDto extends OmitType(UserDto, ['password'] as const) {
  constructor(partial: Partial<UserDto>) {
    super();

    // Exclude password property from the object
    delete partial.password;

    Object.assign(this, partial);
  }
}
