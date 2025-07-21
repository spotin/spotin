import { IsString } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { Jwt } from '@/auth/jwt/types/jwt.type';

export class JwtDto implements Jwt {
	/**
	 * The JWT to access protected resources
	 */
	@ApiProperty({ format: 'jwt' })
	@IsString()
	accessToken: string;

	/**
	 * The JWT to refresh the access token
	 */
	@ApiProperty({ format: 'jwt' })
	@IsString()
	refreshToken: string;

	constructor(entity: Jwt) {
		this.accessToken = entity.accessToken;
		this.refreshToken = entity.refreshToken;
	}
}
