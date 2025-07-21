import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtRefreshTokenAuthGuard extends AuthGuard(
	PassportStrategy.JWT_REFRESH_TOKEN,
) {}
