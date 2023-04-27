import { TokenDto } from '@/tokens/dto/token.dto';
import { PickType } from '@nestjs/swagger';

export class CreateTokenDto extends PickType(TokenDto, ['name'] as const) {}
