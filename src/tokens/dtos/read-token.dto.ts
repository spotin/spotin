import { TokenDto } from '@/tokens/dtos/token.dto';
import { ReadToken } from '@/tokens/types/read-token';
import { Token } from '@/tokens/types/token';
import { OmitType } from '@nestjs/swagger';

export class ReadTokenDto
	extends OmitType(TokenDto, ['hash'] as const)
	implements ReadToken
{
	constructor(entity: Token) {
		super();

		this.id = entity.id;
		this.name = entity.name;
		this.createdAt = entity.createdAt;
		this.updatedAt = entity.updatedAt;
	}
}
