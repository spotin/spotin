import { IsString } from 'class-validator';
import { JwtAccessToken } from '../types/jwt-access-token';
import { ApiProperty } from '@nestjs/swagger';

export class JwtAccessTokenDto implements JwtAccessToken {
  // The JWT access token to access protected resources
  @ApiProperty({ format: 'jwt' })
  @IsString()
  accessToken: string;

  constructor(partial: Partial<JwtAccessTokenDto>) {
    Object.assign(this, partial);
  }
}
