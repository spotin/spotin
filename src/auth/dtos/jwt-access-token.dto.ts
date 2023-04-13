import { IsString } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';
import { JwtAccessToken } from '@/auth/types/jwt-access-token';

export class JwtAccessTokenDto implements JwtAccessToken {
  // The JWT access token to access protected resources
  @ApiProperty({ format: 'jwt' })
  @IsString()
  accessToken: string;

  constructor(partial: Partial<JwtAccessTokenDto>) {
    Object.assign(this, partial);
  }
}
