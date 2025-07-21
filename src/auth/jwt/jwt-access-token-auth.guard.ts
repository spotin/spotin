import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtAccessTokenAuthGuard extends AuthGuard(
	PassportStrategy.JWT_ACCESS_TOKEN,
) {}
