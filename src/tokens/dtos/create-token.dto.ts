import { TokenDto } from '@/tokens/dtos/token.dto';
import { OmitType } from '@nestjs/swagger';

export class CreateTokenDto extends OmitType(TokenDto, [
	'id',
	'hash',
	'createdAt',
	'updatedAt',
] as const) {}
