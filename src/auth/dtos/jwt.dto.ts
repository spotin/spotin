import { IsString } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { Jwt } from '@/auth/types/jwt.type';

export class JwtDto implements Jwt {
	/**
	 * The JWT to access protected resources
	 */
	@ApiProperty({ format: 'jwt' })
	@IsString()
	jwt: string;

	constructor(partial: Partial<JwtDto>) {
		Object.assign(this, partial);
	}
}
