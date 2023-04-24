import { TokenDto } from '@/tokens/dto/token.dto';
import { OmitType } from '@nestjs/swagger';

export class CreateTokenDto extends OmitType(TokenDto, [
  'id',
  'createdAt',
  'updatedAt',
  'deletedAt',
] as const) {}
