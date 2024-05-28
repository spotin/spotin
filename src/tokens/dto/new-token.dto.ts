import { TokenDto } from '@/tokens/dto/token.dto';
import { OmitType } from '@nestjs/swagger';
import { IsString, Length } from 'class-validator';

export class NewTokenDto extends OmitType(TokenDto, [
	'userId',
	'hash',
] as const) {
	/**
	 * The token value
	 */
	@IsString()
	@Length(64)
	value: string;

	constructor(partial: Partial<TokenDto> & { value: string }) {
		super();

		// Exclude userId and hash properties from the object
		delete partial.userId;
		delete partial.hash;

		Object.assign(this, partial);
	}
}
