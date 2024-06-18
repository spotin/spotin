import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtOrUnrestrictedAuthGuard extends AuthGuard([
	PassportStrategy.JWT,
	PassportStrategy.UNRESTRICTED,
]) {}
