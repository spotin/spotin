import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtAccessTokenOrUnrestrictedAuthGuard extends AuthGuard([
	PassportStrategy.JWT_ACCESS_TOKEN,
	PassportStrategy.UNRESTRICTED,
]) {}
