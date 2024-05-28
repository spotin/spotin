import { TokenDto } from '@/tokens/dto/token.dto';
import { OmitType } from '@nestjs/swagger';

export class ReadTokenDto extends OmitType(TokenDto, [
	'userId',
	'hash',
] as const) {
	constructor(partial: Partial<TokenDto>) {
		super();

		// Exclude userId and hash properties from the object
		delete partial.userId;
		delete partial.hash;

		Object.assign(this, partial);
	}
}
