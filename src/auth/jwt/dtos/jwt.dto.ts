import { IsString } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { Jwt } from '@/auth/jwt/types/jwt.type';

export class JwtDto implements Jwt {
	/**
	 * The JWT to access protected resources
	 */
	@ApiProperty({ format: 'jwt' })
	@IsString()
	jwt: string;

	constructor(entity: Jwt) {
		this.jwt = entity.jwt;
	}
}
