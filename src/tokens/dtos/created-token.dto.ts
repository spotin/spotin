import { TokenDto } from '@/tokens/dtos/token.dto';
import { CreatedToken } from '@/tokens/types/created-token';
import { OmitType } from '@nestjs/swagger';
import { IsString, Length } from 'class-validator';

export class CreatedTokenDto
	extends OmitType(TokenDto, ['hash'] as const)
	implements CreatedToken
{
	/**
	 * The token value
	 */
	@IsString()
	@Length(64)
	value: string;

	constructor(entity: CreatedToken) {
		super();

		this.id = entity.id;
		this.name = entity.name;
		this.value = entity.value;
		this.createdAt = entity.createdAt;
		this.updatedAt = entity.updatedAt;
	}
}
