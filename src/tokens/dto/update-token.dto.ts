import { OmitType, PartialType } from '@nestjs/swagger';
import { TokenDto } from '@/tokens/dto/token.dto';

export class UpdateTokenDto extends PartialType(
  OmitType(TokenDto, ['id', 'createdAt', 'updatedAt', 'deletedAt'] as const),
) {}
