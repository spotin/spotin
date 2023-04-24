import { TokenDto } from '@/tokens/dto/token.dto';
import { OmitType } from '@nestjs/swagger';

export class ReadTokenDto extends OmitType(TokenDto, ['userId'] as const) {
  constructor(partial: Partial<TokenDto>) {
    super();

    // Exclude userId property from the object
    delete partial.userId;

    Object.assign(this, partial);
  }
}
